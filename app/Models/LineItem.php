<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LineItem extends Model
{
    protected $fillable = [
        'proforma_invoice_id', 'location_id', 'line_number', 'description',
        'location_name', 'hsn_sac', 'billing_cycle', 'price_per_month',
        'discount_per_month', 'net_price_per_month', 'billed_days',
        'subtotal', 'tax_amount', 'total_with_tax',
    ];

    public function proformaInvoice() { return $this->belongsTo(ProformaInvoice::class); }
    public function location() { return $this->belongsTo(Location::class); }
}
