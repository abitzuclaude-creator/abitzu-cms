<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AgentController extends Controller
{
    public function index()
    {
        $this->authorize('manage');
        $agents = User::withCount([
            'assignedClients as client_count',
            'assignedInvoices as open_pi' => fn($q) => $q->where('status', '!=', 'paid'),
        ])->orderBy('name')->get();
        return view('agents.index', compact('agents'));
    }

    public function create()
    {
        $this->authorize('manage');
        return view('agents.create');
    }

    public function store(Request $request)
    {
        $this->authorize('manage');
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|max:255|unique:users',
            'phone'    => 'nullable|string|max:20',
            'role'     => 'required|in:agent,manager,admin',
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $data['password'] = Hash::make($data['password']);
        User::create($data);

        return redirect()->route('agents.index')->with('success', 'Agent created.');
    }

    public function edit(User $agent)
    {
        $this->authorize('manage');
        return view('agents.edit', compact('agent'));
    }

    public function update(Request $request, User $agent)
    {
        $this->authorize('manage');
        $data = $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $agent->id,
            'phone' => 'nullable|string|max:20',
            'role'  => 'required|in:agent,manager,admin',
        ]);

        if ($request->filled('password')) {
            $request->validate(['password' => ['confirmed', Password::defaults()]]);
            $data['password'] = Hash::make($request->password);
        }

        $agent->update($data);
        return redirect()->route('agents.index')->with('success', 'Agent updated.');
    }

    public function toggle(User $agent)
    {
        $this->authorize('manage');
        $agent->update(['is_active' => !$agent->is_active, 'deactivated_at' => $agent->is_active ? now() : null]);
        return response()->json(['ok' => true, 'is_active' => $agent->is_active]);
    }

    private function authorize(string $ability): void
    {
        if (!auth()->user()->canManage()) {
            abort(403, 'Insufficient permissions.');
        }
    }
}
