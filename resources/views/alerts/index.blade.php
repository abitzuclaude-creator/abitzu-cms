<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Alerts – Abitzu CMS</title>
<style>
body{font-family:system-ui,sans-serif;background:#f7f8fa;color:#16181d;margin:0}
.topbar{display:flex;align-items:center;gap:16px;padding:0 24px;height:58px;background:#fff;border-bottom:1px solid #e8eaee}
.logo{width:28px;height:28px;border-radius:8px;background:#1f6feb;color:#fff;display:grid;place-items:center;font-weight:700;font-size:15px}
.nav-links{display:flex;gap:8px}
.nav-links a{display:inline-flex;align-items:center;gap:6px;padding:6px 13px;border-radius:8px;font-size:13px;font-weight:600;text-decoration:none;color:#5b606b}
.nav-links a:hover,.nav-links a.active{background:#eaf2fe;color:#1f6feb}
.main{max-width:900px;margin:32px auto;padding:0 24px}
.alert-card{background:#fff;border:1px solid #e8eaee;border-radius:12px;padding:16px 20px;margin-bottom:12px;display:flex;align-items:flex-start;gap:16px}
.badge{font-size:11.5px;font-weight:600;padding:3px 10px;border-radius:20px}
.badge.open{background:#fdecec;color:#dc2626}
.badge.resolved{background:#e9f7ef;color:#16a34a}
.badge.ignored{background:#f1f5f9;color:#64748b}
</style>
</head>
<body>
<div class="topbar">
  <div class="logo">A</div>
  <h1 style="font-size:15px;font-weight:700;margin:0">Abitzu CMS</h1>
  <div style="flex:1"></div>
  <nav class="nav-links">
    <a href="{{ route('dashboard') }}">Dashboard</a>
    <a href="{{ route('collections.index') }}">Collections</a>
    <a href="{{ route('alerts.index') }}" class="active">Alerts</a>
  </nav>
  <form method="POST" action="{{ route('logout') }}" style="margin:0">@csrf
    <button type="submit" style="border:1px solid #e8eaee;background:#fbfcfd;color:#5b606b;padding:6px 13px;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer">Sign out</button>
  </form>
</div>
<div class="main">
  <h2 style="font-size:22px;font-weight:700;margin:0 0 20px">Alerts</h2>
  @forelse($alerts as $alert)
  <div class="alert-card">
    <div style="flex:1">
      <div style="font-weight:700;font-size:14px">{{ $alert->title }}</div>
      <div style="color:#5b606b;font-size:13px;margin-top:3px">{{ $alert->description }}</div>
      <div style="font-size:11.5px;color:#8b909b;margin-top:6px">{{ $alert->created_at->diffForHumans() }}</div>
    </div>
    <span class="badge {{ $alert->status }}">{{ ucfirst($alert->status) }}</span>
  </div>
  @empty
  <div style="text-align:center;color:#8b909b;padding:40px">No alerts.</div>
  @endforelse
</div>
</body>
</html>
