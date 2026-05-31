<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CreditNote extends Model
{
    protected $fillable = ['cn_number', 'proforma_invoice_id', 'client_id', 'amount', 'reason', 'cn_date', 'issued_by'];
    protected $casts = ['cn_date' => 'date', 'amount' => 'decimal:2'];

    public function proformaInvoice() { return $this->belongsTo(ProformaInvoice::class); }
    public function client() { return $this->belongsTo(Client::class); }
    public function issuedBy() { return $this->belongsTo(User::class, 'issued_by'); }
}
