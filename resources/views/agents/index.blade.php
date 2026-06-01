<!DOCTYPE html>
<html lang="en"><head><meta charset="UTF-8"/><meta name="viewport" content="width=device-width, initial-scale=1.0"/><title>Agents – Abitzu CMS</title>
@include('partials.styles')
</head><body>
@include('partials.topbar')
<div class="main">
  @if(session('success'))<div class="alert-success">{{ session('success') }}</div>@endif
  <div class="flex items-center justify-between mb-4">
    <h2 style="margin:0;font-size:22px;font-weight:700">Team Members</h2>
    <a href="{{ route('agents.create') }}" class="btn btn-primary">+ New Agent</a>
  </div>
  <div class="card" style="padding:0;overflow:hidden">
    <table>
      <thead><tr><th>Name</th><th>Email</th><th>Phone</th><th>Role</th><th>Clients</th><th>Open PIs</th><th>Status</th><th>Last Login</th><th></th></tr></thead>
      <tbody>
        @foreach($agents as $a)
        <tr @if(!$a->is_active)style="opacity:.5"@endif>
          <td style="font-weight:600">{{ $a->name }}</td>
          <td>{{ $a->email }}</td>
          <td>{{ $a->phone ?: '—' }}</td>
          <td><span class="badge badge-blue" style="text-transform:capitalize">{{ $a->role }}</span></td>
          <td class="text-right text-mono">{{ $a->client_count }}</td>
          <td class="text-right text-mono" style="font-weight:600">{{ $a->open_pi }}</td>
          <td>@if($a->is_active)<span class="badge badge-green">Active</span>@else<span class="badge badge-red">Inactive</span>@endif</td>
          <td class="text-muted">{{ $a->last_login_at ? $a->last_login_at->diffForHumans() : 'Never' }}</td>
          <td style="white-space:nowrap">
            <a href="{{ route('agents.edit', $a) }}" class="btn btn-outline btn-sm">Edit</a>
            @if($a->id !== auth()->id())
            <button onclick="toggleAgent({{ $a->id }}, this)" class="btn btn-sm @if($a->is_active) btn-outline" style="color:#dc2626;border-color:#fca5a5 @else btn-success @endif">{{ $a->is_active ? 'Deactivate' : 'Activate' }}</button>
            @endif
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
<script>
function toggleAgent(id, btn) {
  fetch('/api/agents/' + id + '/toggle', {method:'PATCH', headers:{'X-CSRF-TOKEN':document.querySelector('meta[name=csrf-token]')?.content || '{{ csrf_token() }}','Accept':'application/json'}})
    .then(r => r.json()).then(() => location.reload());
}
</script>
</body></html>
