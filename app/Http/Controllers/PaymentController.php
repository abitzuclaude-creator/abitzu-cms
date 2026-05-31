<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\Payment;
use App\Models\ProformaInvoice;
use App\Services\PaymentService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function __construct(private PaymentService $svc) {}

    public function store(Request $request)
    {
        $request->validate([
            'proforma_invoice_id' => 'required|exists:proforma_invoices,id',
            'amount'              => 'required|numeric|min:0.01',
            'payment_date'        => 'required|date',
            'mode'                => 'required|in:neft,rtgs,imps,upi,cheque,cash_deposit',
            'bank_account_id'     => 'required|exists:bank_accounts,id',
            'reference_number'    => 'nullable|string|max:255',
            'remarks'             => 'nullable|string',
        ]);

        $pi = ProformaInvoice::findOrFail($request->proforma_invoice_id);
        $payment = $this->svc->record($pi, $request->all(), auth()->id());

        return response()->json(['ok' => true, 'payment' => $payment, 'new_balance' => $pi->fresh()->balance_due, 'new_status' => $pi->fresh()->status]);
    }

    public function bankAccounts()
    {
        return response()->json(BankAccount::where('is_active', true)->get());
    }

    public function clientProformas(int $clientId)
    {
        $pis = ProformaInvoice::where('client_id', $clientId)
            ->whereIn('status', ['unpaid', 'partially_paid', 'disputed'])
            ->orderBy('due_date')
            ->get(['id', 'pi_number', 'balance_due', 'due_date', 'status']);

        return response()->json($pis);
    }
}
