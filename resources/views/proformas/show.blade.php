<!DOCTYPE html>
<html lang="en"><head><meta charset="UTF-8"/><meta name="viewport" content="width=device-width, initial-scale=1.0"/><title>PI-{{ str_pad($proforma->pi_number, 4, '0', STR_PAD_LEFT) }} – Abitzu CMS</title>
@include('partials.styles')
</head><body>
@include('partials.topbar')
<div class="main">
  @if(session('success'))<div class="alert-success">{{ session('success') }}</div>@endif
  <div class="flex items-center justify-between mb-4">
    <div class="flex items-center gap-3">
      <a href="{{ route('proformas.index') }}" style="color:#5b606b;text-decoration:none;font-size:14px">← Invoices</a>
      <h2 style="margin:0;font-size:22px;font-weight:700">PI-{{ str_pad($proforma->pi_number, 4, '0', STR_PAD_LEFT) }}</h2>
      <span class="badge @if($proforma->status=='paid')badge-green @elseif($proforma->status=='unpaid')badge-red @else badge-yellow @endif" style="text-transform:capitalize">{{ str_replace('_',' ',$proforma->status) }}</span>
    </div>
    <a href="{{ route('proformas.edit', $proforma) }}" class="btn btn-primary">Edit</a>
  </div>

  <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:24px">
    <div class="card">
      <h3 style="font-size:14px;font-weight:700;color:#8b909b;text-transform:uppercase;margin:0 0 12px">Invoice Details</h3>
      <div style="display:grid;grid-template-columns:130px 1fr;gap:8px;font-size:14px">
        <span class="text-muted">Client</span><a href="{{ route('clients.show', $proforma->client) }}" style="color:#1f6feb;text-decoration:none;font-weight:600">{{ $proforma->client->business_name }}</a>
        <span class="text-muted">Invoice Name</span><span>{{ $proforma->invoice_name }}</span>
        <span class="text-muted">PI Date</span><span>{{ $proforma->pi_date->format('d M Y') }}</span>
        <span class="text-muted">Due Date</span><span @if($proforma->isOverdue())style="color:#dc2626;font-weight:600"@endif>{{ $proforma->due_date->format('d M Y') }}@if($proforma->isOverdue()) (Overdue)@endif</span>
        <span class="text-muted">Period</span><span>{{ $proforma->usage_period_start->format('M Y') }} – {{ $proforma->usage_period_end->format('M Y') }}</span>
        <span class="text-muted">Billing Cycle</span><span style="text-transform:capitalize">{{ str_replace('_',' ',$proforma->billing_cycle) }}</span>
        <span class="text-muted">Stage</span><span class="badge badge-blue" style="text-transform:capitalize">{{ $proforma->collection_stage }}</span>
        <span class="text-muted">Agent</span><span>{{ $proforma->assignedAgent?->name ?: '— Unassigned —' }}</span>
      </div>
    </div>
    <div class="card">
      <h3 style="font-size:14px;font-weight:700;color:#8b909b;text-transform:uppercase;margin:0 0 12px">Financials</h3>
      <div style="display:grid;grid-template-columns:120px 1fr;gap:8px;font-size:14px">
        <span class="text-muted">Subtotal</span><span class="text-mono">₹{{ number_format($proforma->subtotal, 2) }}</span>
        <span class="text-muted">Tax ({{ $proforma->tax_rate }}%)</span><span class="text-mono">₹{{ number_format($proforma->tax_amount, 2) }}</span>
        <span class="text-muted">Grand Total</span><span class="text-mono" style="font-weight:700;font-size:18px">₹{{ number_format($proforma->grand_total, 2) }}</span>
        <span class="text-muted">Balance Due</span><span class="text-mono" style="font-weight:700;font-size:18px;@if($proforma->balance_due > 0)color:#dc2626 @else color:#16a34a @endif">₹{{ number_format($proforma->balance_due, 2) }}</span>
        <span class="text-muted">GSTIN</span><span class="text-mono">{{ $proforma->gstin ?: '—' }}</span>
      </div>
    </div>
  </div>

  @if($proforma->lineItems->count())
  <div class="card mb-4">
    <h3 style="font-size:16px;font-weight:700;margin:0 0 12px">Line Items ({{ $proforma->lineItems->count() }})</h3>
    <table><thead><tr><th>#</th><th>Location</th><th>Description</th><th>Price/mo</th><th>Discount</th><th>Days</th><th>Subtotal</th><th>Tax</th><th>Total</th></tr></thead>
    <tbody>@foreach($proforma->lineItems as $li)
      <tr><td>{{ $li->line_number }}</td><td>{{ $li->location_name }}</td><td>{{ $li->description }}</td>
      <td class="text-mono text-right">₹{{ number_format($li->price_per_month,0) }}</td>
      <td class="text-mono text-right">₹{{ number_format($li->discount_per_month,0) }}</td>
      <td class="text-right">{{ $li->billed_days }}</td>
      <td class="text-mono text-right">₹{{ number_format($li->subtotal,0) }}</td>
      <td class="text-mono text-right">₹{{ number_format($li->tax_amount,0) }}</td>
      <td class="text-mono text-right" style="font-weight:600">₹{{ number_format($li->total_with_tax,0) }}</td></tr>
    @endforeach</tbody></table>
  </div>
  @endif

  @if($proforma->payments->count())
  <div class="card mb-4">
    <h3 style="font-size:16px;font-weight:700;margin:0 0 12px">Payments ({{ $proforma->payments->count() }})</h3>
    <table><thead><tr><th>Date</th><th>Amount</th><th>Mode</th><th>Reference</th><th>Remarks</th></tr></thead>
    <tbody>@foreach($proforma->payments as $p)
      <tr><td>{{ $p->payment_date->format('d M Y') }}</td><td class="text-mono" style="font-weight:600;color:#16a34a">₹{{ number_format($p->amount,0) }}</td>
      <td style="text-transform:uppercase">{{ $p->mode }}</td><td class="text-mono">{{ $p->reference_number ?: '—' }}</td><td>{{ $p->remarks ?: '—' }}</td></tr>
    @endforeach</tbody></table>
  </div>
  @endif

  @if($proforma->notes)<div class="card"><h3 style="font-size:14px;font-weight:700;margin:0 0 8px">Notes</h3><p style="margin:0;color:#5b606b">{{ $proforma->notes }}</p></div>@endif
</div>
</body></html>
