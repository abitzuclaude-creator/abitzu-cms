// ---- Detail drawer ----------------------------------------------------
function Drawer({ inv, onClose, onPatch, onAddCall, onWhatsApp, onToast }){
  const st = STAGE[inv.stage];
  const out = Math.max(0, inv.amount-(inv.paidAmount||0));
  const [callNote, setCallNote] = React.useState('');
  const [reply, setReply] = React.useState(inv.reply||'');
  React.useEffect(()=>{ setReply(inv.reply||''); }, [inv.id]);

  const tlIcon = { call:'phone', payment:'rupee', promise:'clock', stage:'arrowRight', created:'file', note:'message' };
  const tlColor = { call:'#1f6feb', payment:'#16a34a', promise:'#7c3aed', stage:'#5b606b', created:'#8b909b', note:'#b45309' };

  const addCall = ()=>{ onAddCall(inv.id, callNote.trim() || 'Called \u2014 no notes'); setCallNote(''); onToast('Call logged for '+inv.brand); };

  return (
    <>
      <div className="scrim" onClick={onClose}/>
      <aside className="drawer" onClick={e=>e.stopPropagation()}>
        <div className="dh">
          <div className="dh-top">
            <div style={{minWidth:0}}>
              <div className="dh-brand">{inv.brand}</div>
              <div className="dh-biz">{inv.business}</div>
            </div>
            <div style={{display:'flex',alignItems:'flex-start',gap:10}}>
              <div className="dh-amt">{fmtINR(out)}<small>{out>0?'outstanding':'fully cleared'}</small></div>
              <button className="iconbtn" onClick={onClose} style={{border:'none',background:'none'}}><Icon name="x" size={18}/></button>
            </div>
          </div>
          <div className="dh-meta">
            <span className="stage-pill" style={{background:st.tint,color:st.color,fontSize:13,padding:'5px 12px'}}>
              <span className="col-dot" style={{background:st.color}}/>{st.label}</span>
            <span className="mono" style={{fontSize:12,color:'var(--ink-3)'}}>{inv.pi}</span>
            {inv.gst==='A/f' && <span className="chip warn"><Icon name="alert" size={12}/>GSTIN awaiting filing</span>}
          </div>
        </div>

        <div className="dbody">
          <div className="sec">
            <div className="sec-h"><Icon name="bolt" size={13}/>Follow-up</div>
            <div className="field">
              <label>Stage</label>
              <div className="stage-select">
                {STAGES.map(s=>(
                  <button key={s.id} className={'stage-opt'+(s.id===inv.stage?' on':'')}
                    style={s.id===inv.stage?{background:s.color}:{}}
                    onClick={()=>onPatch(inv.id,{stage:s.id})}>
                    <span className="col-dot" style={{background:s.id===inv.stage?'#fff':s.color}}/>{s.short}
                  </button>
                ))}
              </div>
            </div>
            <div className="two">
              <div className="field">
                <label>Assigned to</label>
                <select className="input" value={inv.assignee} onChange={e=>onPatch(inv.id,{assignee:e.target.value})}>
                  {TEAM.map(t=><option key={t.id} value={t.id}>{t.name}</option>)}
                </select>
              </div>
              <div className="field">
                <label>Promise to pay</label>
                <input type="date" className="input" value={inv.promiseDate||''}
                  onChange={e=>onPatch(inv.id,{promiseDate:e.target.value||null})}/>
              </div>
            </div>
            <div className="two">
              <div className="field">
                <label>Payment received (\u20b9)</label>
                <input type="number" className="input mono" value={inv.paidAmount||''} placeholder="0"
                  onChange={e=>onPatch(inv.id,{paidAmount:Number(e.target.value)||0})}/>
              </div>
              <div className="field">
                <label>Payment date</label>
                <input type="date" className="input" value={inv.paidDate||''}
                  onChange={e=>onPatch(inv.id,{paidDate:e.target.value||null})}/>
              </div>
            </div>
            <div className="field">
              <label>Customer reply / notes</label>
              <textarea className="input" value={reply} placeholder="What did the customer say?"
                onChange={e=>setReply(e.target.value)}
                onBlur={()=>{ if(reply!==(inv.reply||'')) onPatch(inv.id,{reply}); }}/>
            </div>
          </div>

          <div className="sec">
            <div className="sec-h"><Icon name="phone" size={13}/>Call attempts ({inv.calls.length})</div>
            {inv.calls.length>0 && (
              <div className="calls">
                {[...inv.calls].reverse().map((c,i)=>(
                  <div key={i} className="call-item">
                    <span className="ci-date">{fmtShortDate(c.date)}</span>
                    <span className="ci-note">{c.note}</span>
                  </div>
                ))}
              </div>
            )}
            <div style={{display:'flex',gap:8}}>
              <input className="input" placeholder="Log a new call\u2026" value={callNote}
                onChange={e=>setCallNote(e.target.value)}
                onKeyDown={e=>{ if(e.key==='Enter') addCall(); }}/>
              <button className="btn primary" style={{flex:'none'}} onClick={addCall}>
                <Icon name="phone" size={14}/>Log</button>
            </div>
          </div>

          <div className="sec">
            <div className="sec-h"><Icon name="file" size={13}/>Proforma details</div>
            <dl className="kv">
              <dt>Invoice no.</dt><dd className="mono">{inv.pi}</dd>
              <dt>Invoice date</dt><dd>{fmtDate(inv.piDate)}</dd>
              <dt>Due date</dt><dd>{fmtDate(inv.dueDate)}</dd>
              <dt>Subscription</dt><dd>{inv.subPeriod}</dd>
              <dt>Amount</dt><dd className="mono">{fmtINR(inv.amount)}</dd>
              <dt>GST no.</dt><dd className="mono">{inv.gst}</dd>
              <dt>Business</dt><dd>{inv.business}</dd>
              <dt>Address</dt><dd>{inv.address}</dd>
              <dt>Contact person</dt><dd>{inv.contact}</dd>
              <dt>Owner email</dt><dd>{inv.ownerEmail}</dd>
            </dl>
          </div>

          <div className="sec">
            <div className="sec-h"><Icon name="clock" size={13}/>Activity</div>
            <div className="timeline">
              {[...inv.activity].reverse().map((a,i)=>{
                const who = a.by==='system' ? 'System' : (MEMBER[a.by]?.name || a.by);
                return (
                  <div key={i} className="tl">
                    <span className="tl-dot"><i style={{background:tlColor[a.t]||'#8b909b'}}/></span>
                    <div className="tl-body">
                      <div className="tl-text">{a.text}</div>
                      <div className="tl-meta">{who} \u00b7 {fmtDate(a.at)}</div>
                    </div>
                  </div>
                );
              })}
            </div>
          </div>
        </div>

        <div className="dfoot">
          {inv.stage!=='paid' && <button className="btn primary" style={{flex:1,background:'var(--ok)',borderColor:'var(--ok)'}}
            onClick={()=>onWhatsApp(inv.id)}><Icon name="whatsapp" size={15}/>WhatsApp</button>}
          <button className="btn" style={{flex:1}} onClick={()=>onToast('Reminder email queued to '+inv.ownerEmail)}>
            <Icon name="mail" size={15}/>Send email</button>
          <button className="btn" onClick={()=>onToast('Exporting '+inv.pi+'.pdf')}>
            <Icon name="download" size={15}/></button>
        </div>
      </aside>
    </>
  );
}

Object.assign(window, { Drawer });
