<!DOCTYPE html>
<html lang="en"><head><meta charset="UTF-8"/><meta name="viewport" content="width=device-width, initial-scale=1.0"/><title>Clients – Abitzu CMS</title>
@include('partials.styles')
</head><body>
@include('partials.topbar')
<div class="main">
  @if(session('success'))<div class="alert-success">{{ session('success') }}</div>@endif
  <div class="flex items-center justify-between mb-4">
    <h2 style="margin:0;font-size:22px;font-weight:700">Clients</h2>
    <a href="{{ route('clients.create') }}" class="btn btn-primary">+ New Client</a>
  </div>
  <div class="card" style="padding:0;overflow:hidden">
    <div style="padding:16px;border-bottom:1px solid #eef0f3">
      <form method="GET" style="display:flex;gap:8px">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Search by name, GSTIN, ID..." style="flex:1;padding:9px 12px;border:1px solid #d8dbe1;border-radius:8px;font-size:14px"/>
        <button type="submit" class="btn btn-primary">Search</button>
        @if(request('q'))<a href="{{ route('clients.index') }}" class="btn btn-outline">Clear</a>@endif
      </form>
    </div>
    <table>
      <thead><tr>
        <th>Business Name</th><th>Invoice Name</th><th>GSTIN</th><th>Agent</th><th>Open PIs</th><th>Status</th><th></th>
      </tr></thead>
      <tbody>
        @forelse($clients as $c)
        <tr>
          <td><a href="{{ route('clients.show', $c) }}" style="color:#1f6feb;font-weight:600;text-decoration:none">{{ $c->business_name }}</a><br><span class="text-muted">{{ $c->business_id }}</span></td>
          <td>{{ $c->invoice_name }}</td>
          <td style="font-family:monospace;font-size:13px">{{ $c->gstin ?: '—' }}</td>
          <td>{{ $c->assignedAgent?->name ?: '—' }}</td>
          <td class="text-right text-mono" style="font-weight:600">{{ $c->open_pi }}</td>
          <td>@if($c->is_active)<span class="badge badge-green">Active</span>@else<span class="badge badge-red">Inactive</span>@endif</td>
          <td><a href="{{ route('clients.edit', $c) }}" class="btn btn-outline btn-sm">Edit</a></td>
        </tr>
        @empty
        <tr><td colspan="7" style="text-align:center;color:#8b909b;padding:32px">No clients found.</td></tr>
        @endforelse
      </tbody>
    </table>
    @if($clients->hasPages())
    <div style="padding:16px">{{ $clients->links('partials.pagination') }}</div>
    @endif
  </div>
</div>
</body></html>
