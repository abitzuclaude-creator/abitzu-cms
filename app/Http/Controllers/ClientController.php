<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\User;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $query = Client::with('assignedAgent')->withCount(['proformaInvoices as open_pi' => fn($q) => $q->where('status', '!=', 'paid')]);

        if ($request->filled('q')) {
            $s = $request->q;
            $query->where(fn($q) => $q->where('business_name', 'like', "%$s%")->orWhere('invoice_name', 'like', "%$s%")->orWhere('gstin', 'like', "%$s%")->orWhere('business_id', 'like', "%$s%"));
        }

        $clients = $query->orderBy('business_name')->paginate(25)->withQueryString();
        return view('clients.index', compact('clients'));
    }

    public function create()
    {
        $agents = User::where('is_active', true)->whereIn('role', ['agent','admin','manager','owner'])->orderBy('name')->get();
        return view('clients.create', compact('agents'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'business_name' => 'required|string|max:255',
            'business_id'   => 'required|string|max:100|unique:clients',
            'invoice_type'  => 'required|in:ho_level,branch_level',
            'invoice_name'  => 'required|string|max:255',
            'address'       => 'nullable|string',
            'gstin'         => 'nullable|string|max:20',
            'pan'           => 'nullable|string|max:15',
            'assigned_agent_id' => 'nullable|exists:users,id',
            'owner_name'    => 'nullable|string|max:255',
            'owner_email'   => 'nullable|email|max:255',
            'owner_phone1'  => 'nullable|string|max:20',
            'owner_phone2'  => 'nullable|string|max:20',
        ]);

        $client = Client::create($data);
        return redirect()->route('clients.show', $client)->with('success', 'Client created.');
    }

    public function show(Client $client)
    {
        $client->load(['assignedAgent', 'locations', 'contacts', 'proformaInvoices' => fn($q) => $q->orderByDesc('pi_number')]);
        return view('clients.show', compact('client'));
    }

    public function edit(Client $client)
    {
        $agents = User::where('is_active', true)->whereIn('role', ['agent','admin','manager','owner'])->orderBy('name')->get();
        return view('clients.edit', compact('client', 'agents'));
    }

    public function update(Request $request, Client $client)
    {
        $data = $request->validate([
            'business_name' => 'required|string|max:255',
            'invoice_type'  => 'required|in:ho_level,branch_level',
            'invoice_name'  => 'required|string|max:255',
            'address'       => 'nullable|string',
            'gstin'         => 'nullable|string|max:20',
            'pan'           => 'nullable|string|max:15',
            'assigned_agent_id' => 'nullable|exists:users,id',
            'owner_name'    => 'nullable|string|max:255',
            'owner_email'   => 'nullable|email|max:255',
            'owner_phone1'  => 'nullable|string|max:20',
            'owner_phone2'  => 'nullable|string|max:20',
        ]);

        $client->update($data);
        return redirect()->route('clients.show', $client)->with('success', 'Client updated.');
    }
}
