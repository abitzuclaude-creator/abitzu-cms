<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\ProformaInvoice;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    public function record(ProformaInvoice $pi, array $data, int $userId): Payment
    {
        $amount = (float) $data['amount'];

        if ($amount <= 0) {
            throw new \InvalidArgumentException('Payment amount must be greater than zero.');
        }
        if ($amount > (float) $pi->balance_due) {
            throw new \InvalidArgumentException('Payment amount exceeds outstanding balance.');
        }

        return DB::transaction(function () use ($pi, $data, $userId, $amount) {
            $payment = Payment::create([
                'proforma_invoice_id' => $pi->id,
                'client_id'           => $pi->client_id,
                'amount'              => $amount,
                'payment_date'        => $data['payment_date'],
                'mode'                => $data['mode'],
                'bank_account_id'     => $data['bank_account_id'],
                'reference_number'    => $data['reference_number'] ?? null,
                'remarks'             => $data['remarks'] ?? null,
                'recorded_by'         => $userId,
            ]);

            $newBalance = max(0, (float) $pi->balance_due - $amount);
            $pi->balance_due = $newBalance;
            $pi->status = $newBalance == 0 ? 'paid' : 'partially_paid';
            $pi->collection_stage = $newBalance == 0 ? 'paid' : 'partial';
            $pi->save();

            app(ActivityLogService::class)->log($userId, 'payment_recorded', $pi, [
                'amount' => $amount, 'new_balance' => $newBalance
            ]);

            return $payment;
        });
    }
}
