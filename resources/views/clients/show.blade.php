<!DOCTYPE html>
<html lang="en"><head><meta charset="UTF-8"/><meta name="viewport" content="width=device-width, initial-scale=1.0"/><title>{{ $client->business_name }} – Abitzu CMS</title>
@include('partials.styles')
</head><body>
@include('partials.topbar')
<div class="main">
  @if(session('success'))<div class="alert-success">{{ session('success') }}</div>@endif
  <div class="flex items-center justify-between mb-4">
    <div class="flex items-center gap-3">
      <a href="{{ route('clients.index') }}" style="color:#5b606b;text-decoration:none;font-size:14px">← Clients</a>
      <h2 style="margin:0;font-size:22px;font-weight:700">{{ $client->business_name }}</h2>
      @if($client->is_active)<span class="badge badge-green">Active</span>@else<span class="badge badge-red">Inactive</span>@endif
    </div>
    <a href="{{ route('clients.edit', $client) }}" class="btn btn-primary">Edit</a>
  </div>

  <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:24px">
    <div class="card">
      <h3 style="font-size:14px;font-weight:700;color:#8b909b;text-transform:uppercase;margin:0 0 12px">Business Details</h3>
      <div style="display:grid;grid-template-columns:120px 1fr;gap:8px;font-size:14px">
        <span class="text-muted">ID</span><span>{{ $client->business_id }}</span>
        <span class="text-muted">Invoice Name</span><span>{{ $client->invoice_name }}</span>
        <span class="text-muted">Type</span><span style="text-transform:capitalize">{{ str_replace('_', ' ', $client->invoice_type) }}</span>
        <span class="text-muted">GSTIN</span><span style="font-family:monospace">{{ $client->gstin ?: '—' }}</span>
        <span class="text-muted">PAN</span><span style="font-family:monospace">{{ $client->pan ?: '—' }}</span>
        <span class="text-muted">Address</span><span>{{ $client->address ?: '—' }}</span>
        <span class="text-muted">Agent</span><span>{{ $client->assignedAgent?->name ?: '— Unassigned —' }}</span>
      </div>
    </div>
    <div class="card">
      <h3 style="font-size:14px;font-weight:700;color:#8b909b;text-transform:uppercase;margin:0 0 12px">Owner Contact</h3>
      <div style="display:grid;grid-template-columns:100px 1fr;gap:8px;font-size:14px">
        <span class="text-muted">Name</span><span>{{ $client->owner_name ?: '—' }}</span>
        <span class="text-muted">Email</span><span>{{ $client->owner_email ?: '—' }}</span>
        <span class="text-muted">Phone 1</span><span>{{ $client->owner_phone1 ?: '—' }}</span>
        <span class="text-muted">Phone 2</span><span>{{ $client->owner_phone2 ?: '—' }}</span>
      </div>
    </div>
  </div>

  <div class="card">
    <div class="flex items-center justify-between mb-4">
      <h3 style="font-size:16px;font-weight:700;margin:0">Proforma Invoices ({{ $client->proformaInvoices->count() }})</h3>
      <a href="{{ route('proformas.create') }}?client_id={{ $client->id }}" class="btn btn-primary btn-sm">+ New Invoice</a>
    </div>
    <table>
      <thead><tr><th>PI #</th><th>Date</th><th>Due</th><th>Amount</th><th>Balance</th><th>Status</th><th>Stage</th></tr></thead>
      <tbody>
        @forelse($client->proformaInvoices as $pi)
        <tr>
          <td><a href="{{ route('proformas.show', $pi) }}" style="color:#1f6feb;font-weight:600;text-decoration:none">PI-{{ str_pad($pi->pi_number, 4, '0', STR_PAD_LEFT) }}</a></td>
          <td>{{ $pi->pi_date->format('d M Y') }}</td>
          <td>{{ $pi->due_date->format('d M Y') }}</td>
          <td class="text-mono text-right">₹{{ number_format($pi->grand_total, 0) }}</td>
          <td class="text-mono text-right" style="@if($pi->balance_due > 0)color:#dc2626;font-weight:600@else color:#16a34a @endif">₹{{ number_format($pi->balance_due, 0) }}</td>
          <td><span class="badge @if($pi->status=='paid')badge-green @elseif($pi->status=='unpaid')badge-red @else badge-yellow @endif" style="text-transform:capitalize">{{ str_replace('_', ' ', $pi->status) }}</span></td>
          <td><span class="badge badge-blue" style="text-transform:capitalize">{{ $pi->collection_stage }}</span></td>
        </tr>
        @empty
        <tr><td colspan="7" class="text-muted" style="text-align:center;padding:24px">No invoices yet.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  @if($client->locations->count())
  <div class="card mt-4">
    <h3 style="font-size:16px;font-weight:700;margin:0 0 12px">Locations ({{ $client->locations->count() }})</h3>
    <table>
      <thead><tr><th>Name</th><th>Location ID</th><th>Address</th><th>GSTIN</th><th>Status</th></tr></thead>
      <tbody>
        @foreach($client->locations as $loc)
        <tr>
          <td style="font-weight:600">{{ $loc->location_name }}</td>
          <td class="text-mono text-muted">{{ $loc->location_id ?: '—' }}</td>
          <td>{{ $loc->address ?: '—' }}</td>
          <td class="text-mono">{{ $loc->gstin ?: '—' }}</td>
          <td>@if($loc->is_active)<span class="badge badge-green">Active</span>@else<span class="badge badge-red">Inactive</span>@endif</td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  @endif
</div>
</body></html>
