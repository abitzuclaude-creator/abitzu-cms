<!DOCTYPE html>
<html lang="en"><head><meta charset="UTF-8"/><meta name="viewport" content="width=device-width, initial-scale=1.0"/><title>New Client – Abitzu CMS</title>
@include('partials.styles')
</head><body>
@include('partials.topbar')
<div class="main">
  <div class="flex items-center gap-3 mb-4">
    <a href="{{ route('clients.index') }}" style="color:#5b606b;text-decoration:none;font-size:14px">← Clients</a>
    <h2 style="margin:0;font-size:22px;font-weight:700">New Client</h2>
  </div>
  @if($errors->any())<div class="alert-error">@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>@endif
  <div class="card">
    <form method="POST" action="{{ route('clients.store') }}">@csrf
      <div class="form-row">
        <div class="form-group"><label>Business Name *</label><input type="text" name="business_name" value="{{ old('business_name') }}" required/></div>
        <div class="form-group"><label>Business ID *</label><input type="text" name="business_id" value="{{ old('business_id') }}" required/></div>
      </div>
      <div class="form-row">
        <div class="form-group"><label>Invoice Name *</label><input type="text" name="invoice_name" value="{{ old('invoice_name') }}" required/></div>
        <div class="form-group"><label>Invoice Type *</label>
          <select name="invoice_type"><option value="ho_level" @selected(old('invoice_type')=='ho_level')>HO Level</option><option value="branch_level" @selected(old('invoice_type')=='branch_level')>Branch Level</option></select>
        </div>
      </div>
      <div class="form-group"><label>Address</label><textarea name="address" rows="2">{{ old('address') }}</textarea></div>
      <div class="form-row-3">
        <div class="form-group"><label>GSTIN</label><input type="text" name="gstin" value="{{ old('gstin') }}" maxlength="20"/></div>
        <div class="form-group"><label>PAN</label><input type="text" name="pan" value="{{ old('pan') }}" maxlength="15"/></div>
        <div class="form-group"><label>Assigned Agent</label>
          <select name="assigned_agent_id"><option value="">— None —</option>@foreach($agents as $a)<option value="{{ $a->id }}" @selected(old('assigned_agent_id')==$a->id)>{{ $a->name }}</option>@endforeach</select>
        </div>
      </div>
      <h3 style="font-size:16px;font-weight:700;margin:24px 0 12px;border-top:1px solid #eef0f3;padding-top:20px">Owner / Primary Contact</h3>
      <div class="form-row">
        <div class="form-group"><label>Owner Name</label><input type="text" name="owner_name" value="{{ old('owner_name') }}"/></div>
        <div class="form-group"><label>Owner Email</label><input type="email" name="owner_email" value="{{ old('owner_email') }}"/></div>
      </div>
      <div class="form-row">
        <div class="form-group"><label>Phone 1</label><input type="text" name="owner_phone1" value="{{ old('owner_phone1') }}"/></div>
        <div class="form-group"><label>Phone 2</label><input type="text" name="owner_phone2" value="{{ old('owner_phone2') }}"/></div>
      </div>
      <div style="margin-top:24px;display:flex;gap:8px">
        <button type="submit" class="btn btn-primary">Create Client</button>
        <a href="{{ route('clients.index') }}" class="btn btn-outline">Cancel</a>
      </div>
    </form>
  </div>
</div>
</body></html>
