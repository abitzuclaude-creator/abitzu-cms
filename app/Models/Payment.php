<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'proforma_invoice_id', 'client_id', 'amount', 'payment_date',
        'mode', 'bank_account_id', 'reference_number', 'remarks', 'recorded_by',
    ];

    protected $casts = ['payment_date' => 'date', 'amount' => 'decimal:2'];

    public function proformaInvoice() { return $this->belongsTo(ProformaInvoice::class); }
    public function client() { return $this->belongsTo(Client::class); }
    public function bankAccount() { return $this->belongsTo(BankAccount::class); }
    public function recordedBy() { return $this->belongsTo(User::class, 'recorded_by'); }
}
