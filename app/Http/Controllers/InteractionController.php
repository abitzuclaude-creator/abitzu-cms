<?php

namespace App\Http\Controllers;

use App\Models\Interaction;
use App\Models\ProformaInvoice;
use Illuminate\Http\Request;

class InteractionController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'proforma_invoice_id' => 'required|exists:proforma_invoices,id',
            'type'                => 'required|in:phone_call,whatsapp,note',
            'notes'               => 'nullable|string',
            'disposition'         => 'nullable|in:reached,not_reached,voicemail,busy,wrong_number,disconnected',
            'whatsapp_template'   => 'nullable|in:pre_due,post_due',
        ]);

        $pi = ProformaInvoice::with('client')->findOrFail($request->proforma_invoice_id);

        $interaction = Interaction::create([
            'client_id'          => $pi->client_id,
            'proforma_invoice_id'=> $pi->id,
            'user_id'            => auth()->id(),
            'type'               => $request->type,
            'interaction_date'   => now(),
            'disposition'        => $request->disposition,
            'whatsapp_template'  => $request->whatsapp_template,
            'notes'              => $request->notes,
        ]);

        // Auto-advance stage: new → called on first phone call
        if ($request->type === 'phone_call' && $pi->collection_stage === 'new') {
            $pi->update(['collection_stage' => 'called']);
        }
        if ($request->type === 'whatsapp' && $pi->collection_stage === 'new') {
            $pi->update(['collection_stage' => 'called']);
        }

        return response()->json(['ok' => true, 'interaction' => $interaction, 'new_stage' => $pi->fresh()->collection_stage]);
    }
}
