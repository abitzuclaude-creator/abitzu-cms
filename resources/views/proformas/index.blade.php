<!DOCTYPE html>
<html lang="en"><head><meta charset="UTF-8"/><meta name="viewport" content="width=device-width, initial-scale=1.0"/><title>Invoices – Abitzu CMS</title>
@include('partials.styles')
</head><body>
@include('partials.topbar')
<div class="main">
  @if(session('success'))<div class="alert-success">{{ session('success') }}</div>@endif
  <div class="flex items-center justify-between mb-4">
    <h2 style="margin:0;font-size:22px;font-weight:700">Proforma Invoices</h2>
    <a href="{{ route('proformas.create') }}" class="btn btn-primary">+ New Invoice</a>
  </div>
  <div class="card" style="padding:0;overflow:hidden">
    <div style="padding:16px;border-bottom:1px solid #eef0f3;display:flex;gap:8px">
      <form method="GET" style="display:flex;gap:8px;flex:1">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Search PI#, client name..." style="flex:1;padding:9px 12px;border:1px solid #d8dbe1;border-radius:8px;font-size:14px"/>
        <select name="status" style="padding:9px 12px;border:1px solid #d8dbe1;border-radius:8px;font-size:14px">
          <option value="">All statuses</option>
          @foreach(['unpaid','partially_paid','paid','disputed'] as $s)<option value="{{ $s }}" @selected(request('status')==$s)>{{ ucfirst(str_replace('_',' ',$s)) }}</option>@endforeach
        </select>
        <button type="submit" class="btn btn-primary">Filter</button>
        @if(request()->hasAny(['q','status']))<a href="{{ route('proformas.index') }}" class="btn btn-outline">Clear</a>@endif
      </form>
    </div>
    <table>
      <thead><tr><th>PI #</th><th>Client</th><th>Date</th><th>Due</th><th>Amount</th><th>Balance</th><th>Status</th><th>Agent</th><th></th></tr></thead>
      <tbody>
        @forelse($proformas as $pi)
        <tr>
          <td><a href="{{ route('proformas.show', $pi) }}" style="color:#1f6feb;font-weight:600;text-decoration:none">PI-{{ str_pad($pi->pi_number, 4, '0', STR_PAD_LEFT) }}</a></td>
          <td><a href="{{ route('clients.show', $pi->client_id) }}" style="color:#16181d;text-decoration:none">{{ $pi->client->business_name }}</a></td>
          <td>{{ $pi->pi_date->format('d M Y') }}</td>
          <td @if($pi->isOverdue())style="color:#dc2626;font-weight:600"@endif>{{ $pi->due_date->format('d M Y') }}</td>
          <td class="text-mono text-right">₹{{ number_format($pi->grand_total, 0) }}</td>
          <td class="text-mono text-right" style="font-weight:600;@if($pi->balance_due > 0)color:#dc2626 @else color:#16a34a @endif">₹{{ number_format($pi->balance_due, 0) }}</td>
          <td><span class="badge @if($pi->status=='paid')badge-green @elseif($pi->status=='unpaid')badge-red @else badge-yellow @endif" style="text-transform:capitalize">{{ str_replace('_',' ',$pi->status) }}</span></td>
          <td>{{ $pi->assignedAgent?->name ?: '—' }}</td>
          <td><a href="{{ route('proformas.edit', $pi) }}" class="btn btn-outline btn-sm">Edit</a></td>
        </tr>
        @empty
        <tr><td colspan="9" style="text-align:center;color:#8b909b;padding:32px">No invoices found.</td></tr>
        @endforelse
      </tbody>
    </table>
    @if($proformas->hasPages())<div style="padding:16px">{{ $proformas->links('partials.pagination') }}</div>@endif
  </div>
</div>
</body></html>
