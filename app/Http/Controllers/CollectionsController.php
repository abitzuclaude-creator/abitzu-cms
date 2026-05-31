<?php

namespace App\Http\Controllers;

use App\Models\ProformaInvoice;
use App\Models\User;
use Illuminate\Http\Request;

class CollectionsController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = ProformaInvoice::with(['client', 'assignedAgent', 'interactions'])
            ->orderBy('pi_number', 'desc');

        if ($user->isAgent()) {
            $query->where('assigned_agent_id', $user->id);
        } elseif ($request->filled('agent')) {
            $query->where('assigned_agent_id', $request->agent);
        }

        if ($request->filled('q')) {
            $search = $request->q;
            $query->whereHas('client', function ($q) use ($search) {
                $q->where('business_name', 'like', "%$search%")
                  ->orWhere('invoice_name', 'like', "%$search%")
                  ->orWhere('gstin', 'like', "%$search%");
            })->orWhere('pi_number', 'like', "%$search%");
        }

        $invoices = $query->get()->map(fn($pi) => $this->formatInvoice($pi));
        $agents = User::where('is_active', true)->whereIn('role', ['agent', 'admin', 'manager', 'owner'])->get();

        if ($request->expectsJson()) {
            return response()->json(['invoices' => $invoices, 'agents' => $agents]);
        }

        return view('collections.index', compact('invoices', 'agents'));
    }

    public function updateStage(Request $request, ProformaInvoice $pi)
    {
        $request->validate(['stage' => 'required|in:new,called,promised,partial,overdue,disputed,paid']);
        $pi->update(['collection_stage' => $request->stage]);
        return response()->json(['ok' => true, 'invoice' => $this->formatInvoice($pi->fresh(['client', 'assignedAgent', 'interactions']))]);
    }

    public function updateAssignee(Request $request, ProformaInvoice $pi)
    {
        $request->validate(['assignee_id' => 'required|exists:users,id']);
        $pi->update(['assigned_agent_id' => $request->assignee_id]);
        return response()->json(['ok' => true]);
    }

    public function updatePromise(Request $request, ProformaInvoice $pi)
    {
        $request->validate(['promise_date' => 'nullable|date']);
        $pi->update(['promise_date' => $request->promise_date]);
        if ($request->promise_date && $pi->collection_stage === 'called') {
            $pi->update(['collection_stage' => 'promised']);
        }
        return response()->json(['ok' => true]);
    }

    private function formatInvoice(ProformaInvoice $pi): array
    {
        return [
            'id'             => $pi->id,
            'pi'             => 'PI-' . str_pad($pi->pi_number, 4, '0', STR_PAD_LEFT),
            'pi_number'      => $pi->pi_number,
            'piDate'         => $pi->pi_date?->toDateString(),
            'dueDate'        => $pi->due_date?->toDateString(),
            'brand'          => $pi->client->business_name,
            'business'       => $pi->client->invoice_name,
            'address'        => $pi->client->address,
            'gst'            => $pi->gstin ?? ($pi->client->gstin ?? 'A/f'),
            'contact'        => $pi->client->owner_name,
            'ownerEmail'     => $pi->client->owner_email,
            'amount'         => (float) $pi->grand_total,
            'paidAmount'     => (float) ($pi->grand_total - $pi->balance_due),
            'paidDate'       => null,
            'stage'          => $pi->collection_stage,
            'status'         => $pi->status,
            'assignee'       => $pi->assignedAgent?->id ? $this->formatMember($pi->assignedAgent) : null,
            'promiseDate'    => $pi->promise_date?->toDateString(),
            'subPeriod'      => $pi->usage_period_start->format('M Y') . ' – ' . $pi->usage_period_end->format('M Y'),
            'calls'          => $pi->interactions->where('type', 'phone_call')->map(fn($i) => [
                'date' => $i->interaction_date->toDateString(),
                'note' => $i->notes,
            ])->values(),
            'activity'       => $pi->interactions->map(fn($i) => [
                'type' => $i->type,
                'by'   => $i->user_id,
                'at'   => $i->interaction_date->toDateString(),
                'text' => $i->notes,
            ])->values(),
            'reply'          => $pi->notes,
        ];
    }

    private function formatMember(User $u): array
    {
        return ['id' => $u->id, 'name' => $u->name, 'email' => $u->email, 'role' => $u->role];
    }
}
