<div class="topbar">
  <div class="logo">A</div>
  <h1>Abitzu CMS</h1>
  <div class="spacer"></div>
  <nav class="nav-links">
    <a href="{{ route('dashboard') }}" @if(request()->routeIs('dashboard'))class="active"@endif>Dashboard</a>
    <a href="{{ route('collections.index') }}" @if(request()->routeIs('collections.*'))class="active"@endif>Collections</a>
    <a href="{{ route('clients.index') }}" @if(request()->routeIs('clients.*'))class="active"@endif>Clients</a>
    <a href="{{ route('proformas.index') }}" @if(request()->routeIs('proformas.*'))class="active"@endif>Invoices</a>
    @if(auth()->user()->canManage())
    <a href="{{ route('agents.index') }}" @if(request()->routeIs('agents.*'))class="active"@endif>Agents</a>
    @endif
    <a href="{{ route('alerts.index') }}" @if(request()->routeIs('alerts.*'))class="active"@endif>Alerts</a>
  </nav>
  <span style="font-size:13px;color:#5b606b;margin-left:8px">{{ auth()->user()->name }}</span>
  <form method="POST" action="{{ route('logout') }}" style="margin:0">@csrf
    <button type="submit" class="btn-outline">Sign out</button>
  </form>
</div>
