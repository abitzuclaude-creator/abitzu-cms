<?php

namespace App\Http\Controllers;

use App\Models\ProformaInvoice;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;

class WhatsAppController extends Controller
{
    public function __construct(private WhatsAppService $svc) {}

    public function compose(ProformaInvoice $pi)
    {
        $pi->load('client', 'lineItems');
        return response()->json($this->svc->compose($pi));
    }

    public function log(Request $request)
    {
        $request->validate(['proforma_invoice_id' => 'required|exists:proforma_invoices,id', 'template_type' => 'required|in:pre_due,post_due']);

        $pi = ProformaInvoice::findOrFail($request->proforma_invoice_id);

        \App\Models\Interaction::create([
            'client_id'          => $pi->client_id,
            'proforma_invoice_id'=> $pi->id,
            'user_id'            => auth()->id(),
            'type'               => 'whatsapp',
            'interaction_date'   => now(),
            'whatsapp_template'  => $request->template_type,
            'notes'              => 'WhatsApp ' . $request->template_type . ' reminder sent',
        ]);

        if ($pi->collection_stage === 'new') {
            $pi->update(['collection_stage' => 'called']);
        }

        return response()->json(['ok' => true]);
    }
}
