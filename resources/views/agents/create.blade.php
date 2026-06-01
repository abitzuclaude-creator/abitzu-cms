<!DOCTYPE html>
<html lang="en"><head><meta charset="UTF-8"/><meta name="viewport" content="width=device-width, initial-scale=1.0"/><title>New Agent – Abitzu CMS</title>
@include('partials.styles')
</head><body>
@include('partials.topbar')
<div class="main">
  <div class="flex items-center gap-3 mb-4">
    <a href="{{ route('agents.index') }}" style="color:#5b606b;text-decoration:none;font-size:14px">← Agents</a>
    <h2 style="margin:0;font-size:22px;font-weight:700">New Agent</h2>
  </div>
  @if($errors->any())<div class="alert-error">@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>@endif
  <div class="card" style="max-width:600px">
    <form method="POST" action="{{ route('agents.store') }}">@csrf
      <div class="form-group"><label>Name *</label><input type="text" name="name" value="{{ old('name') }}" required/></div>
      <div class="form-group"><label>Email *</label><input type="email" name="email" value="{{ old('email') }}" required/></div>
      <div class="form-row">
        <div class="form-group"><label>Phone</label><input type="text" name="phone" value="{{ old('phone') }}"/></div>
        <div class="form-group"><label>Role *</label>
          <select name="role" required><option value="agent" @selected(old('role')=='agent')>Agent</option><option value="manager" @selected(old('role')=='manager')>Manager</option><option value="admin" @selected(old('role')=='admin')>Admin</option></select>
        </div>
      </div>
      <div class="form-row">
        <div class="form-group"><label>Password *</label><input type="password" name="password" required/></div>
        <div class="form-group"><label>Confirm Password *</label><input type="password" name="password_confirmation" required/></div>
      </div>
      <div style="margin-top:24px;display:flex;gap:8px">
        <button type="submit" class="btn btn-primary">Create Agent</button>
        <a href="{{ route('agents.index') }}" class="btn btn-outline">Cancel</a>
      </div>
    </form>
  </div>
</div>
</body></html>
