// ---- Shared bits ------------------------------------------------------
function outstanding(inv){ return Math.max(0, inv.amount - (inv.paidAmount||0)); }

function StageChip({ inv }){
  if (inv.stage === 'paid')
    return <span className="chip ok"><Icon name="check" size={12}/>Paid {fmtShortDate(inv.paidDate)}</span>;
  if (inv.stage === 'promised' && inv.promiseDate){
    const d = dueLabel(inv.promiseDate);
    return <span className={'chip '+(d.tone==='danger'?'danger':'violet')}><Icon name="clock" size={12}/>Promise {fmtShortDate(inv.promiseDate)}</span>;
  }
  if (inv.stage === 'partial')
    return <span className="chip warn"><Icon name="rupee" size={12}/>{fmtINR(outstanding(inv))} left</span>;
  if (inv.stage === 'disputed')
    return <span className="chip danger"><Icon name="alert" size={12}/>Disputed</span>;
  const d = dueLabel(inv.dueDate);
  return <span className={'chip '+(d.tone==='danger'?'danger':d.tone==='warn'?'warn':'muted')}>
    <Icon name="calendar" size={12}/>{d.text}</span>;
}

// ---- Kanban card ------------------------------------------------------
function Card({ inv, onOpen, onLogCall, onWhatsApp, onDragStart, onDragEnd, dragging }){
  const m = MEMBER[inv.assignee];
  const pct = inv.amount ? Math.round((inv.paidAmount||0)/inv.amount*100) : 0;
  return (
    <div className={'card'+(dragging?' dragging':'')} draggable
      onDragStart={e=>onDragStart(e, inv.id)} onDragEnd={onDragEnd}
      onClick={()=>onOpen(inv.id)}>
      <div className="card-top">
        <div style={{minWidth:0}}>
          <div className="card-brand">{inv.brand}</div>
          <div className="card-biz">{inv.business}</div>
        </div>
        <div className="card-amt-wrap">
          <div className="card-amt">{fmtINR(outstanding(inv))}</div>
        </div>
      </div>
      <div className="card-pi">{inv.pi}</div>
      {inv.stage==='partial' && <div className="progress"><i style={{width:pct+'%'}}/></div>}
      <div className="card-row"><StageChip inv={inv}/></div>
      <div className="quick">
        {inv.stage!=='paid' && <button className="quickbtn wa" title="WhatsApp reminder"
          onClick={e=>{e.stopPropagation();onWhatsApp(inv.id);}}><Icon name="whatsapp" size={13}/></button>}
        <button className="quickbtn" title="Log a call"
          onClick={e=>{e.stopPropagation();onLogCall(inv.id);}}><Icon name="phone" size={13}/></button>
        <button className="quickbtn" title="Open"
          onClick={e=>{e.stopPropagation();onOpen(inv.id);}}><Icon name="arrowRight" size={13}/></button>
      </div>
      <div className="card-foot">
        <div className="card-meta">
          {inv.calls.length>0
            ? <><Icon name="phone" size={12}/>{inv.calls.length + (inv.calls.length>1?' calls':' call')}</>
            : <span style={{color:'var(--ink-4)'}}>No calls yet</span>}
        </div>
        <Avatar member={m} size={22}/>
      </div>
    </div>
  );
}

// ---- Board ------------------------------------------------------------
function Board({ invoices, onOpen, onLogCall, onWhatsApp, onMove }){
  const [dragId, setDragId] = React.useState(null);
  const [overCol, setOverCol] = React.useState(null);
  const start = (e, id)=>{ setDragId(id); e.dataTransfer.effectAllowed='move'; };
  const end = ()=>{ setDragId(null); setOverCol(null); };
  const drop = (stage)=>{ if(dragId) onMove(dragId, stage); end(); };

  return (
    <div className="board-wrap">
      <div className="board">
        {STAGES.map(st=>{
          const items = invoices.filter(i=>i.stage===st.id);
          const sum = items.reduce((a,i)=>a+outstanding(i),0);
          return (
            <div key={st.id}
              className={'col'+(overCol===st.id?' drop':'')}
              onDragOver={e=>{e.preventDefault();setOverCol(st.id);}}
              onDragLeave={e=>{ if(e.currentTarget===e.target) setOverCol(null); }}
              onDrop={()=>drop(st.id)}>
              <div className="col-head">
                <span className="col-dot" style={{background:st.color}}/>
                <span className="col-title">{st.short}</span>
                <span className="col-count">{items.length}</span>
                {st.id!=='paid' && sum>0 && <span className="col-sum">{fmtINR(sum)}</span>}
              </div>
              <div className="col-body">
                {items.length===0 && <div className="col-empty">{overCol===st.id?'Drop here':'\u2014'}</div>}
                {items.map(inv=>(
                  <Card key={inv.id} inv={inv} onOpen={onOpen} onLogCall={onLogCall} onWhatsApp={onWhatsApp}
                    onDragStart={start} onDragEnd={end} dragging={dragId===inv.id}/>
                ))}
              </div>
            </div>
          );
        })}
      </div>
    </div>
  );
}

// ---- Table ------------------------------------------------------------
function Table({ invoices, onOpen }){
  return (
    <div className="table-wrap">
      <table className="tbl">
        <thead><tr>
          <th>Brand / Business</th><th>Proforma #</th><th>Stage</th>
          <th>Assignee</th><th>Due</th><th>Calls</th>
          <th style={{textAlign:'right'}}>Amount</th><th style={{textAlign:'right'}}>Outstanding</th>
        </tr></thead>
        <tbody>
          {invoices.map(inv=>{
            const st = STAGE[inv.stage]; const m = MEMBER[inv.assignee];
            const d = dueLabel(inv.dueDate);
            return (
              <tr key={inv.id} onClick={()=>onOpen(inv.id)}>
                <td><div className="t-brand">{inv.brand}</div><div className="t-biz">{inv.business}</div></td>
                <td className="mono" style={{fontSize:'12px',color:'var(--ink-2)'}}>{inv.pi}</td>
                <td><span className="stage-pill" style={{background:st.tint,color:st.color}}>
                  <span className="col-dot" style={{background:st.color}}/>{st.short}</span></td>
                <td>{m ? <div style={{display:'flex',alignItems:'center',gap:8}}><Avatar member={m} size={22}/>
                  <span style={{fontSize:13}}>{m.name.split(' ')[0]}</span></div> : '\u2014'}</td>
                <td><span style={{fontSize:12.5,fontWeight:600,
                  color:d.tone==='danger'?'var(--danger)':d.tone==='warn'?'var(--warn)':'var(--ink-2)'}}>
                  {inv.stage==='paid'?'\u2014':d.text}</span></td>
                <td className="mono" style={{fontSize:13,color:'var(--ink-2)'}}>{inv.calls.length||'\u2014'}</td>
                <td className="num">{fmtINR(inv.amount)}</td>
                <td className="num" style={{color:outstanding(inv)>0?'var(--ink)':'var(--ok)'}}>
                  {outstanding(inv)>0?fmtINR(outstanding(inv)):'Cleared'}</td>
              </tr>
            );
          })}
        </tbody>
      </table>
    </div>
  );
}

// ---- Cards grid -------------------------------------------------------
function CardsGrid({ invoices, onOpen }){
  return (
    <div className="grid-wrap">
      <div className="grid">
        {invoices.map(inv=>{
          const st = STAGE[inv.stage]; const m = MEMBER[inv.assignee];
          return (
            <div key={inv.id} className="gcard" onClick={()=>onOpen(inv.id)}>
              <div style={{display:'flex',justifyContent:'space-between',alignItems:'flex-start',gap:10}}>
                <div style={{minWidth:0}}>
                  <div style={{fontWeight:700,fontSize:15.5,letterSpacing:'-.3px'}}>{inv.brand}</div>
                  <div style={{fontSize:12.5,color:'var(--ink-3)',marginTop:2}}>{inv.business}</div>
                </div>
                <span className="stage-pill" style={{background:st.tint,color:st.color}}>
                  <span className="col-dot" style={{background:st.color}}/>{st.short}</span>
              </div>
              <div style={{display:'flex',justifyContent:'space-between',alignItems:'flex-end',marginTop:16}}>
                <div>
                  <div style={{fontSize:11,color:'var(--ink-3)',fontWeight:600,textTransform:'uppercase',letterSpacing:'.4px'}}>Outstanding</div>
                  <div className="mono" style={{fontSize:21,fontWeight:600,letterSpacing:'-.5px',marginTop:3}}>{fmtINR(outstanding(inv))}</div>
                </div>
                <Avatar member={m} size={26}/>
              </div>
              <div style={{display:'flex',alignItems:'center',justifyContent:'space-between',marginTop:14,paddingTop:13,borderTop:'1px solid var(--border-2)'}}>
                <span className="mono" style={{fontSize:11,color:'var(--ink-4)'}}>{inv.pi}</span>
                <StageChip inv={inv}/>
              </div>
            </div>
          );
        })}
      </div>
    </div>
  );
}

// ---- Metric bar -------------------------------------------------------
function MetricBar({ invoices }){
  const open = invoices.filter(i=>i.stage!=='paid');
  const totalOut = open.reduce((a,i)=>a+outstanding(i),0);
  const overdue = invoices.filter(i=>i.stage!=='paid' && (i.stage==='overdue' || daysFromToday(i.dueDate)<0));
  const overdueAmt = overdue.reduce((a,i)=>a+outstanding(i),0);
  const collected = invoices.filter(i=>i.paidDate && new Date(i.paidDate).getMonth()===4)
    .reduce((a,i)=>a+(i.paidAmount||0),0);
  const promiseWk = invoices.filter(i=>i.promiseDate && daysFromToday(i.promiseDate)>=0 && daysFromToday(i.promiseDate)<=7);

  const M = ({icon,label,value,sub,cls})=>(
    <div className="metric">
      <div className="m-label"><Icon name={icon} size={13}/>{label}</div>
      <div className={'m-value '+(cls||'')}>{value}</div>
      {sub && <div className="m-sub">{sub}</div>}
    </div>
  );
  return (
    <div className="metrics">
      <M icon="rupee" label="Total outstanding" value={fmtINR(totalOut)} sub={open.length+' open invoices'}/>
      <M icon="alert" label="Overdue" value={fmtINR(overdueAmt)} sub={overdue.length+' invoices past due'} cls="danger"/>
      <M icon="clock" label="Promised this week" value={promiseWk.length} sub={fmtINR(promiseWk.reduce((a,i)=>a+outstanding(i),0))+' expected'}/>
      <M icon="trend" label="Collected in May" value={fmtINR(collected)} sub="paid + part payments" cls="ok"/>
    </div>
  );
}

Object.assign(window, { Card, Board, Table, CardsGrid, MetricBar, StageChip, outstanding });
