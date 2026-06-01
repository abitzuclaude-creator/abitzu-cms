<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\LineItem;
use App\Models\ProformaInvoice;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProformaController extends Controller
{
    public function index(Request $request)
    {
        $query = ProformaInvoice::with(['client', 'assignedAgent']);

        if ($request->filled('q')) {
            $s = $request->q;
            $query->where(fn($q) => $q->where('pi_number', 'like', "%$s%")->orWhereHas('client', fn($c) => $c->where('business_name', 'like', "%$s%")->orWhere('invoice_name', 'like', "%$s%")));
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $proformas = $query->orderByDesc('pi_number')->paginate(25)->withQueryString();
        return view('proformas.index', compact('proformas'));
    }

    public function create()
    {
        $clients = Client::where('is_active', true)->orderBy('business_name')->get();
        $agents = User::where('is_active', true)->whereIn('role', ['agent','admin','manager','owner'])->orderBy('name')->get();
        $nextPi = (ProformaInvoice::max('pi_number') ?? 0) + 1;
        return view('proformas.create', compact('clients', 'agents', 'nextPi'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'client_id'          => 'required|exists:clients,id',
            'pi_number'          => 'required|integer|unique:proforma_invoices',
            'pi_date'            => 'required|date',
            'due_date'           => 'required|date|after_or_equal:pi_date',
            'billing_cycle'      => 'required|in:monthly,quarterly,half_yearly,yearly',
            'usage_period_start' => 'required|date',
            'usage_period_end'   => 'required|date|after_or_equal:usage_period_start',
            'invoice_name'       => 'required|string|max:255',
            'address'            => 'nullable|string',
            'gstin'              => 'nullable|string|max:20',
            'hsn_sac'            => 'nullable|string|max:20',
            'subtotal'           => 'required|numeric|min:0',
            'tax_type'           => 'required|in:igst,cgst_sgst',
            'tax_rate'           => 'required|numeric|min:0|max:100',
            'tax_amount'         => 'required|numeric|min:0',
            'grand_total'        => 'required|numeric|min:0',
            'assigned_agent_id'  => 'nullable|exists:users,id',
            'notes'              => 'nullable|string',
            'items'              => 'nullable|array',
            'items.*.description'       => 'required|string',
            'items.*.location_name'     => 'required|string',
            'items.*.price_per_month'   => 'required|numeric|min:0',
            'items.*.discount_per_month'=> 'nullable|numeric|min:0',
            'items.*.billed_days'       => 'required|integer|min:1',
            'items.*.subtotal'          => 'required|numeric|min:0',
            'items.*.tax_amount'        => 'required|numeric|min:0',
            'items.*.total_with_tax'    => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($data, $request) {
            $pi = ProformaInvoice::create(array_merge(
                collect($data)->except('items')->toArray(),
                ['balance_due' => $data['grand_total'], 'status' => 'unpaid', 'collection_stage' => 'new']
            ));

            if ($request->has('items')) {
                foreach ($data['items'] as $i => $item) {
                    $pi->lineItems()->create(array_merge($item, [
                        'line_number' => $i + 1,
                        'hsn_sac' => $data['hsn_sac'] ?? null,
                        'billing_cycle' => $data['billing_cycle'],
                        'net_price_per_month' => ($item['price_per_month'] ?? 0) - ($item['discount_per_month'] ?? 0),
                    ]));
                }
            }
        });

        return redirect()->route('proformas.index')->with('success', 'Invoice created.');
    }

    public function show(ProformaInvoice $proforma)
    {
        $proforma->load(['client', 'assignedAgent', 'lineItems', 'payments', 'interactions']);
        return view('proformas.show', compact('proforma'));
    }

    public function edit(ProformaInvoice $proforma)
    {
        $proforma->load('lineItems');
        $clients = Client::where('is_active', true)->orderBy('business_name')->get();
        $agents = User::where('is_active', true)->whereIn('role', ['agent','admin','manager','owner'])->orderBy('name')->get();
        return view('proformas.edit', compact('proforma', 'clients', 'agents'));
    }

    public function update(Request $request, ProformaInvoice $proforma)
    {
        $data = $request->validate([
            'pi_date'            => 'required|date',
            'due_date'           => 'required|date',
            'billing_cycle'      => 'required|in:monthly,quarterly,half_yearly,yearly',
            'usage_period_start' => 'required|date',
            'usage_period_end'   => 'required|date',
            'invoice_name'       => 'required|string|max:255',
            'address'            => 'nullable|string',
            'gstin'              => 'nullable|string|max:20',
            'hsn_sac'            => 'nullable|string|max:20',
            'subtotal'           => 'required|numeric|min:0',
            'tax_type'           => 'required|in:igst,cgst_sgst',
            'tax_rate'           => 'required|numeric|min:0|max:100',
            'tax_amount'         => 'required|numeric|min:0',
            'grand_total'        => 'required|numeric|min:0',
            'assigned_agent_id'  => 'nullable|exists:users,id',
            'notes'              => 'nullable|string',
        ]);

        $paidSoFar = $proforma->grand_total - $proforma->balance_due;
        $data['balance_due'] = max(0, $data['grand_total'] - $paidSoFar);

        $proforma->update($data);
        return redirect()->route('proformas.show', $proforma)->with('success', 'Invoice updated.');
    }
}
