<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Dashboard – Abitzu CMS</title>
<style>
body{font-family:system-ui,sans-serif;background:#f7f8fa;color:#16181d;margin:0}
.topbar{display:flex;align-items:center;gap:16px;padding:0 24px;height:58px;background:#fff;border-bottom:1px solid #e8eaee}
.logo{width:28px;height:28px;border-radius:8px;background:#1f6feb;color:#fff;display:grid;place-items:center;font-weight:700;font-size:15px}
h1{font-size:15px;font-weight:700;margin:0}
.spacer{flex:1}
.nav-links{display:flex;gap:8px}
.nav-links a{display:inline-flex;align-items:center;gap:6px;padding:6px 13px;border-radius:8px;font-size:13px;font-weight:600;text-decoration:none;color:#5b606b;transition:.12s}
.nav-links a:hover{background:#eaf2fe;color:#1f6feb}
.nav-links a.active{background:#1f6feb;color:#fff}
.main{max-width:1200px;margin:32px auto;padding:0 24px}
.stat-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:28px}
.stat-card{background:#fff;border:1px solid #e8eaee;border-radius:12px;padding:18px 20px;box-shadow:0 1px 2px rgba(20,24,33,.05)}
.stat-label{font-size:11.5px;font-weight:600;color:#8b909b;text-transform:uppercase;letter-spacing:.4px}
.stat-value{font-family:monospace;font-size:24px;font-weight:700;margin-top:6px;color:#16181d}
.stat-value.danger{color:#dc2626}
.stat-value.ok{color:#16a34a}
.stat-sub{font-size:12px;color:#8b909b;margin-top:3px}
.section-title{font-size:16px;font-weight:700;margin:0 0 14px}
.cta{display:inline-flex;align-items:center;gap:8px;background:#1f6feb;color:#fff;padding:9px 18px;border-radius:9px;text-decoration:none;font-weight:600;font-size:14px}
</style>
</head>
<body>
<div class="topbar">
  <div class="logo">A</div>
  <h1>Abitzu CMS</h1>
  <div class="spacer"></div>
  <nav class="nav-links">
    <a href="{{ route('dashboard') }}" class="active">Dashboard</a>
    <a href="{{ route('collections.index') }}">Collections</a>
    <a href="{{ route('alerts.index') }}">Alerts @if($stats['open_alerts'] > 0)<span style="background:#dc2626;color:#fff;border-radius:20px;padding:1px 6px;font-size:10px;margin-left:2px">{{ $stats['open_alerts'] }}</span>@endif</a>
  </nav>
  <span style="font-size:13px;color:#5b606b;margin-left:8px">{{ auth()->user()->name }}</span>
  <form method="POST" action="{{ route('logout') }}" style="margin:0">
    @csrf
    <button type="submit" style="border:1px solid #e8eaee;background:#fbfcfd;color:#5b606b;padding:6px 13px;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer">Sign out</button>
  </form>
</div>
<div class="main">
  <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px">
    <h2 style="margin:0;font-size:22px;font-weight:700">Overview</h2>
    <a href="{{ route('collections.index') }}" class="cta">Open Collections →</a>
  </div>
  <div class="stat-grid">
    <div class="stat-card">
      <div class="stat-label">Total outstanding</div>
      <div class="stat-value">₹{{ number_format($stats['total_outstanding'], 0) }}</div>
      <div class="stat-sub">{{ $stats['open_count'] }} open invoices</div>
    </div>
    <div class="stat-card">
      <div class="stat-label">Overdue</div>
      <div class="stat-value danger">₹{{ number_format($stats['overdue_amount'], 0) }}</div>
      <div class="stat-sub">{{ $stats['overdue_count'] }} past due</div>
    </div>
    <div class="stat-card">
      <div class="stat-label">Promised this week</div>
      <div class="stat-value">{{ $stats['promise_week'] }}</div>
      <div class="stat-sub">invoices with promise date</div>
    </div>
    <div class="stat-card">
      <div class="stat-label">Open alerts</div>
      <div class="stat-value @if($stats['open_alerts'] > 0)danger @endif">{{ $stats['open_alerts'] }}</div>
      <div class="stat-sub">sequence gaps, issues</div>
    </div>
  </div>

  <p style="color:#5b606b;font-size:13.5px;margin-top:8px">
    The full collections kanban (Board, Table, Cards views) is at
    <a href="{{ route('collections.index') }}" style="color:#1f6feb;font-weight:600">Collections</a>.
  </p>

  <h3 class="section-title" style="margin-top:28px">Agents</h3>
  <div style="background:#fff;border:1px solid #e8eaee;border-radius:12px;overflow:hidden">
    <table style="width:100%;border-collapse:collapse">
      <thead><tr style="background:#fbfcfd">
        <th style="text-align:left;padding:11px 16px;font-size:11px;font-weight:700;color:#8b909b;text-transform:uppercase;letter-spacing:.5px;border-bottom:1px solid #e8eaee">Agent</th>
        <th style="text-align:left;padding:11px 16px;font-size:11px;font-weight:700;color:#8b909b;text-transform:uppercase;letter-spacing:.5px;border-bottom:1px solid #e8eaee">Role</th>
        <th style="text-align:right;padding:11px 16px;font-size:11px;font-weight:700;color:#8b909b;text-transform:uppercase;letter-spacing:.5px;border-bottom:1px solid #e8eaee">Open PIs</th>
      </tr></thead>
      <tbody>
        @foreach($agents as $agent)
        <tr style="border-bottom:1px solid #eef0f3">
          <td style="padding:12px 16px;font-weight:600">{{ $agent->name }}</td>
          <td style="padding:12px 16px;color:#5b606b;text-transform:capitalize">{{ $agent->role }}</td>
          <td style="padding:12px 16px;text-align:right;font-family:monospace;font-weight:600">{{ $agent->open_pi }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
</body>
</html>
