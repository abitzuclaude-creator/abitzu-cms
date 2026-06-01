<!DOCTYPE html>
<html lang="en"><head><meta charset="UTF-8"/><meta name="viewport" content="width=device-width, initial-scale=1.0"/><title>New Invoice – Abitzu CMS</title>
@include('partials.styles')
</head><body>
@include('partials.topbar')
<div class="main">
  <div class="flex items-center gap-3 mb-4">
    <a href="{{ route('proformas.index') }}" style="color:#5b606b;text-decoration:none;font-size:14px">← Invoices</a>
    <h2 style="margin:0;font-size:22px;font-weight:700">New Proforma Invoice</h2>
  </div>
  @if($errors->any())<div class="alert-error">@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>@endif
  <div class="card">
    <form method="POST" action="{{ route('proformas.store') }}">@csrf
      <div class="form-row-3">
        <div class="form-group"><label>Client *</label>
          <select name="client_id" required>
            <option value="">Select client...</option>
            @foreach($clients as $c)<option value="{{ $c->id }}" @selected(old('client_id', request('client_id'))==$c->id)>{{ $c->business_name }}</option>@endforeach
          </select>
        </div>
        <div class="form-group"><label>PI Number *</label><input type="number" name="pi_number" value="{{ old('pi_number', $nextPi) }}" required/></div>
        <div class="form-group"><label>Billing Cycle *</label>
          <select name="billing_cycle" required>
            @foreach(['monthly','quarterly','half_yearly','yearly'] as $bc)<option value="{{ $bc }}" @selected(old('billing_cycle')==$bc)>{{ ucfirst(str_replace('_',' ',$bc)) }}</option>@endforeach
          </select>
        </div>
      </div>
      <div class="form-row-3">
        <div class="form-group"><label>PI Date *</label><input type="date" name="pi_date" value="{{ old('pi_date', date('Y-m-d')) }}" required/></div>
        <div class="form-group"><label>Due Date *</label><input type="date" name="due_date" value="{{ old('due_date') }}" required/></div>
        <div class="form-group"><label>Assigned Agent</label>
          <select name="assigned_agent_id"><option value="">— None —</option>@foreach($agents as $a)<option value="{{ $a->id }}" @selected(old('assigned_agent_id')==$a->id)>{{ $a->name }}</option>@endforeach</select>
        </div>
      </div>
      <div class="form-row">
        <div class="form-group"><label>Usage Period Start *</label><input type="date" name="usage_period_start" value="{{ old('usage_period_start') }}" required/></div>
        <div class="form-group"><label>Usage Period End *</label><input type="date" name="usage_period_end" value="{{ old('usage_period_end') }}" required/></div>
      </div>
      <div class="form-row">
        <div class="form-group"><label>Invoice Name *</label><input type="text" name="invoice_name" value="{{ old('invoice_name') }}" required/></div>
        <div class="form-group"><label>HSN/SAC</label><input type="text" name="hsn_sac" value="{{ old('hsn_sac') }}"/></div>
      </div>
      <div class="form-row">
        <div class="form-group"><label>Address</label><textarea name="address" rows="2">{{ old('address') }}</textarea></div>
        <div class="form-group"><label>GSTIN</label><input type="text" name="gstin" value="{{ old('gstin') }}"/></div>
      </div>
      <h3 style="font-size:16px;font-weight:700;margin:24px 0 12px;border-top:1px solid #eef0f3;padding-top:20px">Amounts</h3>
      <div class="form-row-3">
        <div class="form-group"><label>Subtotal *</label><input type="number" step="0.01" name="subtotal" value="{{ old('subtotal') }}" required/></div>
        <div class="form-group"><label>Tax Type *</label>
          <select name="tax_type" required><option value="cgst_sgst" @selected(old('tax_type')=='cgst_sgst')>CGST+SGST</option><option value="igst" @selected(old('tax_type')=='igst')>IGST</option></select>
        </div>
        <div class="form-group"><label>Tax Rate % *</label><input type="number" step="0.01" name="tax_rate" value="{{ old('tax_rate', 18) }}" required/></div>
      </div>
      <div class="form-row">
        <div class="form-group"><label>Tax Amount *</label><input type="number" step="0.01" name="tax_amount" value="{{ old('tax_amount') }}" required/></div>
        <div class="form-group"><label>Grand Total *</label><input type="number" step="0.01" name="grand_total" value="{{ old('grand_total') }}" required/></div>
      </div>
      <div class="form-group"><label>Notes</label><textarea name="notes" rows="2">{{ old('notes') }}</textarea></div>
      <div style="margin-top:24px;display:flex;gap:8px">
        <button type="submit" class="btn btn-primary">Create Invoice</button>
        <a href="{{ route('proformas.index') }}" class="btn btn-outline">Cancel</a>
      </div>
    </form>
  </div>
</div>
</body></html>
