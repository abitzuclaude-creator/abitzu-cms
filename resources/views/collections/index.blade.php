<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Abitzu Collections</title>
<link rel="preconnect" href="https://fonts.googleapis.com" />
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
<link href="https://fonts.googleapis.com/css2?family=Hanken+Grotesk:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet" />
<style>
:root{
  --bg:#f7f8fa; --panel:#ffffff; --panel-2:#fbfcfd;
  --border:#e8eaee; --border-2:#eef0f3; --border-strong:#d8dbe1;
  --ink:#16181d; --ink-2:#5b606b; --ink-3:#8b909b; --ink-4:#aeb3bd;
  --accent:#1f6feb; --accent-ink:#1a5fcc; --accent-tint:#eaf2fe;
  --danger:#dc2626; --danger-tint:#fdecec;
  --warn:#b45309; --warn-tint:#fdf3e7;
  --ok:#16a34a; --ok-tint:#e9f7ef;
  --shadow-sm:0 1px 2px rgba(20,24,33,.05);
  --shadow-md:0 4px 14px rgba(20,24,33,.08),0 1px 3px rgba(20,24,33,.06);
  --shadow-lg:0 16px 48px rgba(20,24,33,.16),0 4px 12px rgba(20,24,33,.08);
  --r-sm:6px; --r:9px; --r-lg:13px; --r-xl:18px;
  --mono:'JetBrains Mono',ui-monospace,monospace;
  --inverse-bg:#16181d; --inverse-fg:#ffffff;
}
.app{transition:background-color .35s ease, color .35s ease}
.topbar,.metrics,.col,.card,.tbl,.gcard,.drawer,.input,.btn,.seg,.search{transition:background-color .35s ease, border-color .35s ease, color .35s ease}
*{box-sizing:border-box}
html,body{margin:0;height:100%}
body{
  font-family:'Hanken Grotesk',system-ui,-apple-system,sans-serif;
  background:var(--bg); color:var(--ink);
  font-size:14px; line-height:1.45;
  -webkit-font-smoothing:antialiased; text-rendering:optimizeLegibility;
}
#root{height:100%}
button{font-family:inherit;cursor:pointer}
::-webkit-scrollbar{width:10px;height:10px}
::-webkit-scrollbar-thumb{background:#d3d7df;border-radius:20px;border:3px solid var(--bg)}
::-webkit-scrollbar-thumb:hover{background:#bcc1cc}
.mono{font-family:var(--mono);font-feature-settings:'tnum'}

.app{display:flex;flex-direction:column;height:100%;overflow:hidden}
.topbar{display:flex;align-items:center;gap:14px;padding:0 22px;height:58px;background:var(--panel);border-bottom:1px solid var(--border);flex:none}
.brandmark{display:flex;align-items:center;gap:10px;padding-right:6px;flex:none}
.logo{width:28px;height:28px;border-radius:8px;background:var(--accent);color:#fff;display:grid;place-items:center;font-weight:700;font-size:15px;letter-spacing:-.5px;box-shadow:inset 0 0 0 1px rgba(255,255,255,.12)}
.brand-name{font-weight:700;font-size:15px;letter-spacing:-.2px;white-space:nowrap}
.brand-sub{font-size:11px;color:var(--ink-3);font-weight:500;margin-top:-2px;white-space:nowrap}
.topbar-spacer{flex:1}
.search{display:flex;align-items:center;gap:8px;background:var(--panel-2);border:1px solid var(--border);border-radius:var(--r);padding:0 11px;height:34px;width:280px;color:var(--ink-3);transition:border-color .15s,box-shadow .15s}
.search:focus-within{border-color:var(--accent);box-shadow:0 0 0 3px var(--accent-tint);background:var(--panel)}
.search input{border:0;background:none;outline:none;flex:1;font-family:inherit;font-size:13.5px;color:var(--ink)}
.search input::placeholder{color:var(--ink-4)}
.search kbd{font-family:var(--mono);font-size:10px;color:var(--ink-4);border:1px solid var(--border);border-radius:4px;padding:1px 5px;background:var(--panel)}
.seg{display:flex;background:var(--panel-2);border:1px solid var(--border);border-radius:var(--r);padding:3px;gap:2px}
.seg button{display:flex;align-items:center;gap:6px;border:0;background:none;color:var(--ink-3);padding:5px 11px;border-radius:6px;font-size:13px;font-weight:600;transition:.12s}
.seg button:hover{color:var(--ink-2)}
.seg button.on{background:var(--panel);color:var(--ink);box-shadow:var(--shadow-sm)}
.iconbtn{display:grid;place-items:center;width:34px;height:34px;border-radius:var(--r);border:1px solid var(--border);background:var(--panel-2);color:var(--ink-2);transition:.12s}
.iconbtn:hover{background:var(--panel);color:var(--ink);border-color:var(--border-strong)}
.btn{display:inline-flex;align-items:center;gap:7px;height:34px;padding:0 13px;border-radius:var(--r);font-size:13px;font-weight:600;border:1px solid var(--border);background:var(--panel-2);color:var(--ink);transition:.12s}
.btn:hover{background:var(--panel);border-color:var(--border-strong)}
.btn.primary{background:var(--accent);border-color:var(--accent);color:#fff;box-shadow:var(--shadow-sm)}
.btn.primary:hover{background:var(--accent-ink)}
.btn.ghost{border-color:transparent;background:none;color:var(--ink-2)}
.btn.ghost:hover{background:var(--border-2);color:var(--ink)}
.avatar{display:inline-grid;place-items:center;border-radius:50%;color:#fff;font-weight:700;letter-spacing:.2px;flex:none;box-shadow:inset 0 0 0 1px rgba(255,255,255,.18)}
.avatar-btn{border:0;background:none;padding:0;border-radius:50%;display:inline-flex}
.avatar-row{display:flex}
.avatar-row .avatar{box-shadow:0 0 0 2px var(--panel)}
.avatar-row .avatar+.avatar{margin-left:-6px}
.avatar-row.filter button{transition:transform .1s,opacity .12s;opacity:.45}
.avatar-row.filter button:hover{transform:translateY(-1px);opacity:.8}
.avatar-row.filter button.on{opacity:1;transform:translateY(-1px)}
.metrics{display:flex;gap:0;padding:14px 22px;background:var(--panel);border-bottom:1px solid var(--border);flex:none;overflow-x:auto}
.metric{padding:2px 26px;border-right:1px solid var(--border-2);min-width:fit-content}
.metric:first-child{padding-left:0}
.metric:last-child{border-right:0}
.metric .m-label{display:flex;align-items:center;gap:6px;font-size:11.5px;font-weight:600;color:var(--ink-3);text-transform:uppercase;letter-spacing:.4px}
.metric .m-value{font-family:var(--mono);font-size:23px;font-weight:600;letter-spacing:-.6px;margin-top:5px;color:var(--ink)}
.metric .m-value.danger{color:var(--danger)} .metric .m-value.ok{color:var(--ok)}
.metric .m-sub{font-size:12px;color:var(--ink-3);margin-top:2px;font-weight:500}
.board-wrap{flex:1;overflow:auto;padding:18px 22px 26px}
.board{display:flex;gap:14px;align-items:flex-start;min-height:100%}
.col{width:300px;flex:none;background:var(--panel-2);border:1px solid var(--border);border-radius:var(--r-lg);display:flex;flex-direction:column;max-height:100%}
.col.drop{border-color:var(--accent);background:var(--accent-tint);box-shadow:0 0 0 3px var(--accent-tint)}
.col-head{display:flex;align-items:center;gap:8px;padding:12px 13px 10px}
.col-dot{width:9px;height:9px;border-radius:3px;flex:none}
.col-title{font-weight:700;font-size:13px;letter-spacing:-.1px}
.col-count{font-family:var(--mono);font-size:11.5px;font-weight:600;color:var(--ink-3);background:var(--border-2);border-radius:20px;padding:1px 8px}
.col-sum{margin-left:auto;font-family:var(--mono);font-size:12px;font-weight:600;color:var(--ink-2)}
.col-body{padding:0 9px 9px;display:flex;flex-direction:column;gap:9px;overflow-y:auto}
.col-empty{text-align:center;color:var(--ink-4);font-size:12.5px;padding:18px 8px;border:1px dashed var(--border-strong);border-radius:var(--r);margin:2px}
.card{background:var(--panel);border:1px solid var(--border);border-radius:var(--r);padding:12px 13px;box-shadow:var(--shadow-sm);cursor:pointer;transition:box-shadow .14s,border-color .14s,transform .04s;position:relative}
.card:hover{box-shadow:var(--shadow-md);border-color:var(--border-strong)}
.card:active{transform:scale(.997)}
.card.dragging{opacity:.4}
.card-top{display:flex;align-items:flex-start;justify-content:space-between;gap:8px}
.card-brand{font-weight:700;font-size:14px;letter-spacing:-.2px;line-height:1.25}
.card-biz{font-size:12px;color:var(--ink-3);margin-top:1px;line-height:1.3}
.card-pi{font-family:var(--mono);font-size:10.5px;color:var(--ink-4);font-weight:500;margin-top:7px}
.card-amt-wrap{text-align:right;flex:none}
.card-amt{font-family:var(--mono);font-size:15px;font-weight:600;letter-spacing:-.4px}
.card-row{display:flex;align-items:center;gap:7px;margin-top:11px;flex-wrap:wrap}
.chip{display:inline-flex;align-items:center;gap:5px;font-size:11.5px;font-weight:600;padding:3px 8px;border-radius:20px;line-height:1;white-space:nowrap}
.chip.muted{background:var(--border-2);color:var(--ink-2)}
.chip.danger{background:var(--danger-tint);color:var(--danger)}
.chip.warn{background:var(--warn-tint);color:var(--warn)}
.chip.accent{background:var(--accent-tint);color:var(--accent-ink)}
.chip.violet{background:#f1ecfd;color:#7c3aed}
.chip.ok{background:var(--ok-tint);color:var(--ok)}
.card-foot{display:flex;align-items:center;justify-content:space-between;margin-top:11px;padding-top:10px;border-top:1px solid var(--border-2)}
.card-meta{display:flex;align-items:center;gap:5px;font-size:11.5px;color:var(--ink-3);font-weight:500}
.progress{height:5px;border-radius:20px;background:var(--border-2);overflow:hidden;margin-top:10px}
.progress > i{display:block;height:100%;background:var(--warn);border-radius:20px}
.quick{display:flex;gap:5px;opacity:0;transition:opacity .12s;justify-content:flex-end;margin-top:6px}
.card:hover .quick{opacity:1}
.quickbtn{display:grid;place-items:center;width:26px;height:26px;border-radius:7px;border:1px solid var(--border);background:var(--panel);color:var(--ink-2)}
.quickbtn:hover{background:var(--accent-tint);color:var(--accent-ink);border-color:var(--accent-tint)}
.quickbtn.wa:hover{background:var(--ok-tint);color:var(--ok);border-color:var(--ok-tint)}
.table-wrap{flex:1;overflow:auto;padding:18px 22px 26px}
.tbl{width:100%;border-collapse:separate;border-spacing:0;background:var(--panel);border:1px solid var(--border);border-radius:var(--r-lg);overflow:hidden;box-shadow:var(--shadow-sm)}
.tbl th{position:sticky;top:0;background:var(--panel-2);text-align:left;font-size:11px;font-weight:700;color:var(--ink-3);text-transform:uppercase;letter-spacing:.5px;padding:11px 14px;border-bottom:1px solid var(--border);z-index:1;white-space:nowrap}
.tbl td{padding:12px 14px;border-bottom:1px solid var(--border-2);vertical-align:middle}
.tbl tr:last-child td{border-bottom:0}
.tbl tbody tr{cursor:pointer;transition:background .1s}
.tbl tbody tr:hover{background:var(--panel-2)}
.tbl .num{font-family:var(--mono);font-weight:600;text-align:right}
.t-brand{font-weight:700;letter-spacing:-.1px}
.t-biz{font-size:12px;color:var(--ink-3)}
.stage-pill{display:inline-flex;align-items:center;gap:6px;font-size:12px;font-weight:600;padding:4px 10px;border-radius:20px;white-space:nowrap}
.stage-pill .col-dot{width:7px;height:7px}
.grid-wrap{flex:1;overflow:auto;padding:18px 22px 26px}
.grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(310px,1fr));gap:14px}
.gcard{background:var(--panel);border:1px solid var(--border);border-radius:var(--r-lg);padding:16px;box-shadow:var(--shadow-sm);cursor:pointer;transition:box-shadow .14s,border-color .14s}
.gcard:hover{box-shadow:var(--shadow-md);border-color:var(--border-strong)}
.scrim{position:fixed;inset:0;background:rgba(20,24,33,.32);backdrop-filter:blur(2px);opacity:0;animation:fade .18s forwards;z-index:40}
@keyframes fade{to{opacity:1}}
.drawer{position:fixed;top:0;right:0;height:100%;width:520px;max-width:94vw;background:var(--panel);box-shadow:var(--shadow-lg);z-index:41;display:flex;flex-direction:column;transform:translateX(100%);animation:slidein .26s cubic-bezier(.16,.84,.44,1) forwards}
@keyframes slidein{to{transform:translateX(0)}}
.dh{padding:18px 22px 16px;border-bottom:1px solid var(--border)}
.dh-top{display:flex;align-items:flex-start;justify-content:space-between;gap:10px}
.dh-brand{font-size:20px;font-weight:700;letter-spacing:-.4px}
.dh-biz{font-size:13px;color:var(--ink-3);margin-top:2px}
.dh-amt{font-family:var(--mono);font-size:22px;font-weight:600;letter-spacing:-.5px;text-align:right;flex:none}
.dh-amt small{display:block;font-size:11px;color:var(--ink-3);font-weight:500;letter-spacing:0}
.dh-meta{display:flex;align-items:center;gap:14px;margin-top:14px;flex-wrap:wrap}
.dbody{flex:1;overflow-y:auto;padding:18px 22px 28px}
.sec{margin-bottom:22px}
.sec-h{display:flex;align-items:center;gap:7px;font-size:11.5px;font-weight:700;color:var(--ink-3);text-transform:uppercase;letter-spacing:.5px;margin-bottom:11px}
.kv{display:grid;grid-template-columns:130px 1fr;gap:7px 12px;font-size:13.5px}
.kv dt{color:var(--ink-3);font-weight:500}
.kv dd{margin:0;color:var(--ink);font-weight:500}
.kv dd.mono{font-size:12.5px}
.field{margin-bottom:13px}
.field label{display:block;font-size:12px;font-weight:600;color:var(--ink-2);margin-bottom:5px}
.input,select.input,textarea.input{width:100%;border:1px solid var(--border-strong);border-radius:var(--r-sm);padding:8px 10px;font-family:inherit;font-size:13.5px;color:var(--ink);background:var(--panel);outline:none;transition:.14s}
.input:focus{border-color:var(--accent);box-shadow:0 0 0 3px var(--accent-tint)}
textarea.input{resize:vertical;min-height:64px;line-height:1.5}
.two{display:grid;grid-template-columns:1fr 1fr;gap:11px}
.stage-select{display:flex;flex-wrap:wrap;gap:6px}
.stage-opt{display:inline-flex;align-items:center;gap:6px;font-size:12.5px;font-weight:600;padding:6px 11px;border-radius:20px;border:1px solid var(--border);background:var(--panel-2);color:var(--ink-2);transition:.12s}
.stage-opt:hover{border-color:var(--border-strong)}
.stage-opt.on{color:#fff;border-color:transparent}
.stage-opt .col-dot{width:7px;height:7px}
.calls{display:flex;flex-direction:column;gap:8px;margin-bottom:11px}
.call-item{display:flex;gap:10px;font-size:13px;background:var(--panel-2);border:1px solid var(--border-2);border-radius:var(--r-sm);padding:9px 11px}
.call-item .ci-date{font-family:var(--mono);font-size:11.5px;color:var(--accent-ink);font-weight:600;flex:none;width:52px}
.call-item .ci-note{color:var(--ink-2)}
.timeline{display:flex;flex-direction:column;gap:0;position:relative}
.tl{display:flex;gap:12px;padding-bottom:16px;position:relative}
.tl:before{content:'';position:absolute;left:7px;top:18px;bottom:-2px;width:1.5px;background:var(--border)}
.tl:last-child:before{display:none}
.tl-dot{width:15px;height:15px;border-radius:50%;flex:none;display:grid;place-items:center;margin-top:1px;z-index:1;background:var(--panel);border:1.5px solid var(--border-strong)}
.tl-dot i{width:6px;height:6px;border-radius:50%;display:block}
.tl-body{flex:1}
.tl-text{font-size:13px;color:var(--ink);font-weight:500}
.tl-meta{font-size:11.5px;color:var(--ink-3);margin-top:2px;display:flex;align-items:center;gap:6px}
.dfoot{padding:14px 22px;border-top:1px solid var(--border);display:flex;gap:10px;background:var(--panel-2);flex:none}
.toast{position:fixed;bottom:22px;left:50%;transform:translateX(-50%);background:var(--inverse-bg);color:var(--inverse-fg);padding:11px 18px;border-radius:var(--r);font-size:13px;font-weight:600;box-shadow:var(--shadow-lg);z-index:80;display:flex;align-items:center;gap:9px;animation:toast .3s cubic-bezier(.16,.84,.44,1)}
@keyframes toast{from{opacity:0;transform:translate(-50%,12px)}}
.toast svg{color:#5ee08f}
.empty-state{display:grid;place-items:center;height:100%;color:var(--ink-3);text-align:center}
.app.compact .metrics{padding:9px 22px}
.app.compact .metric{padding:1px 22px}
.app.compact .metric .m-value{font-size:19px;margin-top:2px}
.app.compact .metric .m-sub{display:none}
.app.compact .board-wrap,.app.compact .table-wrap,.app.compact .grid-wrap{padding:12px 18px 18px}
.app.compact .board{gap:10px}
.app.compact .col{width:262px}
.app.compact .col-head{padding:10px 11px 8px}
.app.compact .col-body{gap:6px;padding:0 7px 7px}
.app.compact .card{padding:9px 11px}
.app.compact .card-biz{display:none}
.app.compact .card-pi{margin-top:5px}
.app.compact .card-row{margin-top:8px}
.app.compact .card-foot{margin-top:8px;padding-top:7px}
.app.spacious .metrics{padding:20px 26px}
.app.spacious .metric{padding:3px 30px}
.app.spacious .metric .m-value{font-size:26px}
.app.spacious .board-wrap,.app.spacious .table-wrap,.app.spacious .grid-wrap{padding:26px 28px 34px}
.app.spacious .board{gap:18px}
.app.spacious .col{width:330px}
.app.spacious .col-head{padding:15px 16px 12px}
.app.spacious .col-body{gap:12px;padding:0 12px 13px}
.app.spacious .card{padding:16px 17px}
.app.spacious .card-brand{font-size:15px}
.app.spacious .card-row{margin-top:14px}
.app.spacious .card-foot{margin-top:14px;padding-top:13px}
</style>
</head>
<body>
<div id="root"></div>

<!-- Runtime config passed from Laravel to React -->
<script>
window.__ABITZU_CONFIG = {
  csrfToken: "{{ csrf_token() }}",
  apiBase: "",
          currentUser: {!! json_encode(auth()->user()->only('id','name','email','role')) !!},
          teamMembers: {!! json_encode($agents->map(fn($u) => ['id'=>$u->id,'name'=>$u->name,'email'=>$u->email,'role'=>$u->role])) !!},
  initialInvoices: @json($invoices),
};
</script>

<script src="https://unpkg.com/react@18.3.1/umd/react.development.js" crossorigin="anonymous"></script>
<script src="https://unpkg.com/react-dom@18.3.1/umd/react-dom.development.js" crossorigin="anonymous"></script>
<script src="https://unpkg.com/@babel/standalone@7.29.0/babel.min.js" crossorigin="anonymous"></script>

<script type="text/babel" src="/js/collections/data.jsx"></script>
<script type="text/babel" src="/js/collections/icons.jsx"></script>
<script type="text/babel" src="/js/collections/views.jsx"></script>
<script type="text/babel" src="/js/collections/drawer.jsx"></script>
<script type="text/babel" src="/js/collections/tweaks-panel.jsx"></script>
<script type="text/babel" src="/js/collections/app.jsx"></script>
</body>
</html>
