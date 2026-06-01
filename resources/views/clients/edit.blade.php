<!DOCTYPE html>
<html lang="en"><head><meta charset="UTF-8"/><meta name="viewport" content="width=device-width, initial-scale=1.0"/><title>Edit {{ $client->business_name }} – Abitzu CMS</title>
@include('partials.styles')
</head><body>
@include('partials.topbar')
<div class="main">
  <div class="flex items-center gap-3 mb-4">
    <a href="{{ route('clients.show', $client) }}" style="color:#5b606b;text-decoration:none;font-size:14px">← Back</a>
    <h2 style="margin:0;font-size:22px;font-weight:700">Edit: {{ $client->business_name }}</h2>
  </div>
  @if($errors->any())<div class="alert-error">@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>@endif
  <div class="card">
    <form method="POST" action="{{ route('clients.update', $client) }}">@csrf @method('PUT')
      <div class="form-row">
        <div class="form-group"><label>Business Name *</label><input type="text" name="business_name" value="{{ old('business_name', $client->business_name) }}" required/></div>
        <div class="form-group"><label>Business ID</label><input type="text" value="{{ $client->business_id }}" disabled style="background:#f3f4f6"/></div>
      </div>
      <div class="form-row">
        <div class="form-group"><label>Invoice Name *</label><input type="text" name="invoice_name" value="{{ old('invoice_name', $client->invoice_name) }}" required/></div>
        <div class="form-group"><label>Invoice Type *</label>
          <select name="invoice_type"><option value="ho_level" @selected(old('invoice_type', $client->invoice_type)=='ho_level')>HO Level</option><option value="branch_level" @selected(old('invoice_type', $client->invoice_type)=='branch_level')>Branch Level</option></select>
        </div>
      </div>
      <div class="form-group"><label>Address</label><textarea name="address" rows="2">{{ old('address', $client->address) }}</textarea></div>
      <div class="form-row-3">
        <div class="form-group"><label>GSTIN</label><input type="text" name="gstin" value="{{ old('gstin', $client->gstin) }}"/></div>
        <div class="form-group"><label>PAN</label><input type="text" name="pan" value="{{ old('pan', $client->pan) }}"/></div>
        <div class="form-group"><label>Assigned Agent</label>
          <select name="assigned_agent_id"><option value="">— None —</option>@foreach($agents as $a)<option value="{{ $a->id }}" @selected(old('assigned_agent_id', $client->assigned_agent_id)==$a->id)>{{ $a->name }}</option>@endforeach</select>
        </div>
      </div>
      <h3 style="font-size:16px;font-weight:700;margin:24px 0 12px;border-top:1px solid #eef0f3;padding-top:20px">Owner / Primary Contact</h3>
      <div class="form-row">
        <div class="form-group"><label>Owner Name</label><input type="text" name="owner_name" value="{{ old('owner_name', $client->owner_name) }}"/></div>
        <div class="form-group"><label>Owner Email</label><input type="email" name="owner_email" value="{{ old('owner_email', $client->owner_email) }}"/></div>
      </div>
      <div class="form-row">
        <div class="form-group"><label>Phone 1</label><input type="text" name="owner_phone1" value="{{ old('owner_phone1', $client->owner_phone1) }}"/></div>
        <div class="form-group"><label>Phone 2</label><input type="text" name="owner_phone2" value="{{ old('owner_phone2', $client->owner_phone2) }}"/></div>
      </div>
      <div style="margin-top:24px;display:flex;gap:8px">
        <button type="submit" class="btn btn-primary">Save Changes</button>
        <a href="{{ route('clients.show', $client) }}" class="btn btn-outline">Cancel</a>
      </div>
    </form>
  </div>
</div>
</body></html>
