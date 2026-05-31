// ---- Abitzu Collections : backend-wired app.jsx ----
const { useState, useMemo, useEffect, useCallback } = React;

// Read config injected by Laravel/Blade
const CFG = window.__ABITZU_CONFIG || {};
const CSRF = CFG.csrfToken || document.querySelector('meta[name="csrf-token"]')?.content || '';
const API = CFG.apiBase || '';

// Override static INVOICES with data from backend
const LIVE_INVOICES = (CFG.initialInvoices || []).map(inv => ({
  ...inv,
  calls: inv.calls || [],
  activity: inv.activity || [],
  paidAmount: inv.paidAmount || 0,
  promiseDate: inv.promiseDate || null,
  paidDate: inv.paidDate || null,
  notes: inv.notes || '',
  reply: inv.reply || '',
}));

// Expressive theme system
const ACCENTS = {
  '#1f6feb': { ink:'#1a5fcc', inkDark:'#7db0fb', tint:'#eaf2fe', tintDark:'rgba(56,128,242,.22)' },
  '#7c3aed': { ink:'#6a2fce', inkDark:'#b794f6', tint:'#f1ecfd', tintDark:'rgba(139,92,246,.24)' },
  '#0d9488': { ink:'#0b7d72', inkDark:'#5fd6c8', tint:'#e6f6f4', tintDark:'rgba(20,184,166,.22)' },
  '#475569': { ink:'#374151', inkDark:'#a8b3c4', tint:'#eef1f5', tintDark:'rgba(120,135,160,.25)' },
  '#d97706': { ink:'#b45309', inkDark:'#f0b561', tint:'#fdf3e7', tintDark:'rgba(217,119,6,.24)' },
};
const PAPER_VARS = {
  '--bg':'#f3efe6','--panel':'#fffdf8','--panel-2':'#f9f4ea',
  '--border':'#e7ded0','--border-2':'#eee7da','--border-strong':'#dacfba',
  '--ink':'#2b2519','--ink-2':'#6c6453','--ink-3':'#9b917b','--ink-4':'#b4ab95',
};
const DARK_VARS = {
  '--bg':'#14161b','--panel':'#1b1e25','--panel-2':'#20232b',
  '--border':'#2b303a','--border-2':'#252a33','--border-strong':'#3a404c',
  '--ink':'#eceef3','--ink-2':'#a6acba','--ink-3':'#7a8090','--ink-4':'#565c69',
  '--danger':'#f06d6d','--warn':'#e6a23c','--ok':'#43d484',
  '--danger-tint':'rgba(240,109,109,.16)','--warn-tint':'rgba(230,162,60,.18)','--ok-tint':'rgba(67,212,132,.15)',
};

function themeVars(t) {
  const a = ACCENTS[t.accent] || ACCENTS['#1f6feb'];
  const dark = t.surface === 'graphite';
  const base = dark ? DARK_VARS : t.surface === 'paper' ? PAPER_VARS : {};
  return { ...base, '--accent': t.accent, '--accent-ink': dark ? a.inkDark : a.ink, '--accent-tint': dark ? a.tintDark : a.tint };
}

const TWEAK_DEFAULTS = /*EDITMODE-BEGIN*/{ "surface": "light", "density": "default", "accent": "#1f6feb" }/*EDITMODE-END*/;

// ---- API helpers --------------------------------------------------------
async function apiFetch(url, opts = {}) {
  const res = await fetch(API + url, {
    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json', ...opts.headers },
    ...opts,
  });
  if (!res.ok) throw new Error(await res.text());
  return res.json();
}

// ---- WhatsApp modal (backed by API) ------------------------------------
function WhatsAppModal({ inv, onClose, onSent }) {
  const [data, setData] = useState(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    apiFetch('/api/whatsapp/compose/' + inv.id)
      .then(setData)
      .catch(() => {
        // Fallback to local compose
        const out = Math.max(0, inv.amount - (inv.paidAmount || 0));
        const post = daysFromToday(inv.dueDate) < 0;
        const msg = post
          ? `Dear ${inv.contact}, Abitzu proforma ${inv.pi} for ${inv.brand} (${fmtINR(out)}) was due on ${fmtDate(inv.dueDate)} and is still outstanding. Please clear at the earliest.\n\n— Team Abitzu`
          : `Dear ${inv.contact}, gentle reminder — Abitzu proforma ${inv.pi} for ${inv.brand} amounting to ${fmtINR(out)} is due on ${fmtDate(inv.dueDate)}.\n\n— Team Abitzu`;
        setData({ message: msg, template_type: post ? 'post_due' : 'pre_due', wa_url: null, contact_name: inv.contact });
      })
      .finally(() => setLoading(false));
  }, [inv.id]);

  const send = async () => {
    if (!data) return;
    try { navigator.clipboard && navigator.clipboard.writeText(data.message); } catch(e) {}
    const waUrl = data.wa_url || ('https://wa.me/?text=' + encodeURIComponent(data.message));
    try { window.open(waUrl, '_blank'); } catch(e) {}
    try { await apiFetch('/api/whatsapp/log', { method: 'POST', body: JSON.stringify({ proforma_invoice_id: inv.id, template_type: data.template_type }) }); } catch(e) {}
    onSent(inv.id, data.template_type === 'post_due');
  };

  const post = data?.template_type === 'post_due';

  return (
    <>
      <div className="scrim" onClick={onClose}/>
      <div style={{position:'fixed',inset:0,display:'grid',placeItems:'center',zIndex:60,padding:24}} onClick={onClose}>
        <div onClick={e=>e.stopPropagation()} style={{width:480,maxWidth:'94vw',background:'var(--panel)',borderRadius:'var(--r-lg)',boxShadow:'var(--shadow-lg)',overflow:'hidden'}}>
          <div style={{padding:'16px 20px',borderBottom:'1px solid var(--border)',display:'flex',alignItems:'center',justifyContent:'space-between'}}>
            <div>
              <div style={{fontWeight:700,fontSize:15.5,letterSpacing:'-.2px'}}>WhatsApp reminder</div>
              <div style={{fontSize:12.5,color:'var(--ink-3)',marginTop:1}}>{inv.brand} · {inv.pi}</div>
            </div>
            <button className="iconbtn" style={{border:'none',background:'none'}} onClick={onClose}><Icon name="x" size={18}/></button>
          </div>
          <div style={{padding:'16px 20px'}}>
            {loading ? <div style={{color:'var(--ink-3)',textAlign:'center',padding:24}}>Loading template…</div> : <>
              <div style={{display:'flex',alignItems:'center',gap:8,marginBottom:12}}>
                <span style={{fontSize:12,fontWeight:600,color:'var(--ink-3)'}}>Template</span>
                <span className={'chip '+(post?'danger':'accent')}>{post?'Post-due':'Pre-due'} reminder</span>
                <span style={{fontSize:12,color:'var(--ink-3)',marginLeft:'auto'}}>to {data?.contact_name || inv.contact}</span>
              </div>
              <div style={{background:'#e5ddd5',borderRadius:'var(--r)',padding:14}}>
                <div style={{background:'#dcf8c6',borderRadius:'10px 10px 2px 10px',padding:'11px 13px',fontSize:13.5,lineHeight:1.5,color:'#0b2e13',whiteSpace:'pre-wrap',boxShadow:'0 1px 1px rgba(0,0,0,.12)'}}>{data?.message}</div>
              </div>
              <div style={{fontSize:11.5,color:'var(--ink-3)',marginTop:11,lineHeight:1.5}}>Message copied to clipboard — WhatsApp opens in a new tab. Paste &amp; send there — we'll log it automatically.</div>
            </>}
          </div>
          <div style={{padding:'13px 20px',borderTop:'1px solid var(--border)',display:'flex',justifyContent:'flex-end',gap:10,background:'var(--panel-2)'}}>
            <button className="btn ghost" onClick={onClose}>Cancel</button>
            <button className="btn primary" style={{background:'var(--ok)',borderColor:'var(--ok)'}} onClick={send} disabled={loading}><Icon name="whatsapp" size={15}/>Copy &amp; open WhatsApp</button>
          </div>
        </div>
      </div>
    </>
  );
}

// ---- Main App ----------------------------------------------------------
function App() {
  const [t, setTweak] = useTweaks(TWEAK_DEFAULTS);
  const [invoices, setInvoices] = useState(LIVE_INVOICES);
  const [view, setView]   = useState('board');
  const [query, setQuery] = useState('');
  const [assignee, setAssignee] = useState(null);
  const [openId, setOpenId] = useState(null);
  const [waId, setWaId]   = useState(null);
  const [toast, setToast] = useState(null);

  // Build team from injected config (falls back to static TEAM)
  const team = useMemo(() => {
    const COLORS = ['#1f6feb','#7c3aed','#0d9488','#b45309','#dc2626'];
    const members = (CFG.teamMembers || TEAM).map((m, i) => ({
      id: String(m.id),
      name: m.name,
      initials: m.name.split(' ').map(p => p[0]).join('').toUpperCase().slice(0, 2),
      color: COLORS[i % COLORS.length],
    }));
    return members;
  }, []);

  const MEMBER_MAP = useMemo(() => Object.fromEntries(team.map(m => [String(m.id), m])), [team]);

  const showToast = (msg) => { setToast(msg); clearTimeout(window.__t); window.__t = setTimeout(() => setToast(null), 2400); };

  useEffect(() => {
    const root = document.documentElement;
    const vars = themeVars(t);
    Object.entries(vars).forEach(([k, v]) => root.style.setProperty(k, v));
    return () => Object.keys(vars).forEach(k => root.style.removeProperty(k));
  }, [t.surface, t.accent]);

  const patch = useCallback(async (id, changes) => {
    // Optimistic local update
    setInvoices(prev => prev.map(inv => inv.id === id ? { ...inv, ...changes } : inv));

    try {
      if ('stage' in changes) {
        await apiFetch('/api/collections/' + id + '/stage', { method: 'PATCH', body: JSON.stringify({ stage: changes.stage }) });
      }
      if ('promiseDate' in changes) {
        await apiFetch('/api/collections/' + id + '/promise', { method: 'PATCH', body: JSON.stringify({ promise_date: changes.promiseDate }) });
      }
      if ('assignee' in changes) {
        const agentId = typeof changes.assignee === 'object' ? changes.assignee.id : changes.assignee;
        await apiFetch('/api/collections/' + id + '/assignee', { method: 'PATCH', body: JSON.stringify({ assignee_id: agentId }) });
      }
    } catch (e) {
      console.error('patch failed', e);
    }
  }, []);

  const addCall = useCallback(async (id, note) => {
    setInvoices(prev => prev.map(inv => {
      if (inv.id !== id) return inv;
      const auto = inv.stage === 'new' ? { stage: 'called' } : {};
      return { ...inv, ...auto, calls: [...inv.calls, { date: TODAY.toISOString().slice(0, 10), note }] };
    }));
    try {
      await apiFetch('/api/interactions', {
        method: 'POST',
        body: JSON.stringify({ proforma_invoice_id: id, type: 'phone_call', notes: note, disposition: 'reached' }),
      });
    } catch (e) { console.error('addCall failed', e); }
  }, []);

  const sentWhatsApp = useCallback(async (id, post) => {
    setInvoices(prev => prev.map(inv => {
      if (inv.id !== id) return inv;
      const auto = inv.stage === 'new' ? { stage: 'called' } : {};
      return { ...inv, ...auto };
    }));
    setWaId(null);
    showToast('WhatsApp opened · reminder logged');
  }, []);

  const move = useCallback((id, stage) => {
    const inv = invoices.find(i => i.id === id);
    patch(id, { stage });
    if (inv && inv.stage !== stage) showToast(inv.brand + ' → ' + STAGE[stage].short);
  }, [invoices, patch]);

  const filtered = useMemo(() => {
    const q = query.trim().toLowerCase();
    return invoices.filter(i => {
      if (assignee && String(i.assignee?.id || i.assignee) !== String(assignee)) return false;
      if (!q) return true;
      return [i.brand, i.business, i.pi, i.contact, i.gst].join(' ').toLowerCase().includes(q);
    });
  }, [invoices, query, assignee]);

  const open = openId ? invoices.find(i => i.id === openId) : null;
  const wa = waId ? invoices.find(i => i.id === waId) : null;

  // Build a MEMBER-compatible map that works with existing Drawer/Avatar components
  // Override global MEMBER with team from backend
  useEffect(() => {
    Object.assign(window.MEMBER, MEMBER_MAP);
    window.TEAM.splice(0, window.TEAM.length, ...team);
  }, [MEMBER_MAP, team]);

  useEffect(() => {
    const k = e => {
      if (e.key === 'Escape') { setWaId(null); setOpenId(null); }
      if (e.key === '/' && document.activeElement.tagName !== 'INPUT' && document.activeElement.tagName !== 'TEXTAREA') {
        e.preventDefault();
        document.getElementById('searchbox')?.focus();
      }
    };
    window.addEventListener('keydown', k);
    return () => window.removeEventListener('keydown', k);
  }, []);

  // Resolve assignee for Avatar display (handles both string id and object)
  const resolvedInvoices = useMemo(() => filtered.map(inv => ({
    ...inv,
    assignee: typeof inv.assignee === 'object' ? String(inv.assignee?.id) : String(inv.assignee),
  })), [filtered]);

  const VIEWS = [['board','board','Board'],['table','table','Table'],['cards','grid','Cards']];

  return (
    <div className={'app' + (t.density !== 'default' ? ' ' + t.density : '')}>
      <header className="topbar">
        <div className="brandmark">
          <div className="logo">A</div>
          <div>
            <div className="brand-name">Abitzu Collections</div>
            <div className="brand-sub">Subscription follow-ups</div>
          </div>
        </div>
        <div className="topbar-spacer"/>
        <div className="search">
          <Icon name="search" size={15}/>
          <input id="searchbox" placeholder="Search brand, PI no, GST…" value={query} onChange={e => setQuery(e.target.value)}/>
          <kbd>/</kbd>
        </div>
        <div className="avatar-row filter" title="Filter by assignee">
          {team.map(m => (
            <button key={m.id} className={'avatar-btn' + (assignee === m.id ? ' on' : '')}
              onClick={() => setAssignee(assignee === m.id ? null : m.id)} title={m.name}>
              <Avatar member={m} size={26}/>
            </button>
          ))}
        </div>
        <div className="seg">
          {VIEWS.map(([id, icon, label]) => (
            <button key={id} className={view === id ? 'on' : ''} onClick={() => setView(id)}><Icon name={icon} size={15}/>{label}</button>
          ))}
        </div>
        <button className="btn primary" onClick={() => showToast('Checking support@abitzu.com for new proformas…')}>
          <Icon name="mail" size={15}/>Sync inbox
        </button>
      </header>

      <MetricBar invoices={invoices}/>

      {assignee && (
        <div style={{padding:'8px 22px',background:'var(--accent-tint)',borderBottom:'1px solid var(--border)',fontSize:13,color:'var(--accent-ink)',fontWeight:600,display:'flex',alignItems:'center',gap:8}}>
          <Icon name="filter" size={13}/>Showing {MEMBER_MAP[assignee]?.name || 'Agent'}'s invoices ({filtered.length})
          <button className="btn ghost" style={{height:26,padding:'0 8px',marginLeft:4,color:'var(--accent-ink)'}} onClick={() => setAssignee(null)}>Clear</button>
        </div>
      )}

      {filtered.length === 0
        ? <div className="empty-state"><div><div style={{fontSize:15,fontWeight:600,color:'var(--ink-2)'}}>No invoices match</div><div style={{marginTop:4}}>Try clearing search or filters</div></div></div>
        : view === 'board' ? <Board invoices={resolvedInvoices} onOpen={setOpenId} onLogCall={(id) => { addCall(id, 'Quick call logged'); showToast('Call logged'); }} onWhatsApp={setWaId} onMove={move}/>
        : view === 'table' ? <Table invoices={resolvedInvoices} onOpen={setOpenId}/>
        : <CardsGrid invoices={resolvedInvoices} onOpen={setOpenId}/>}

      {open && <Drawer inv={open} onClose={() => setOpenId(null)} onPatch={patch} onAddCall={addCall} onWhatsApp={setWaId} onToast={showToast}/>}
      {wa && <WhatsAppModal inv={wa} onClose={() => setWaId(null)} onSent={sentWhatsApp}/>}
      {toast && <div className="toast"><Icon name="check" size={16}/>{toast}</div>}

      <TweaksPanel title="Tweaks">
        <TweakSection label="Surface" />
        <TweakRadio label="Mood" value={t.surface}
          options={[{value:'light',label:'Light'},{value:'paper',label:'Paper'},{value:'graphite',label:'Graphite'}]}
          onChange={v => setTweak('surface', v)} />
        <TweakSection label="Density" />
        <TweakRadio label="Rhythm" value={t.density}
          options={[{value:'compact',label:'Compact'},{value:'default',label:'Default'},{value:'spacious',label:'Spacious'}]}
          onChange={v => setTweak('density', v)} />
        <TweakSection label="Accent" />
        <TweakColor label="Color" value={t.accent}
          options={['#1f6feb','#7c3aed','#0d9488','#475569','#d97706']}
          onChange={v => setTweak('accent', v)} />
      </TweaksPanel>
    </div>
  );
}

ReactDOM.createRoot(document.getElementById('root')).render(<App/>);
