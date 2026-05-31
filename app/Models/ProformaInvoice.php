<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProformaInvoice extends Model
{
    protected $fillable = [
        'client_id', 'pi_number', 'pi_date', 'due_date', 'billing_cycle',
        'usage_period_start', 'usage_period_end', 'invoice_name', 'address', 'gstin', 'hsn_sac',
        'subtotal', 'tax_type', 'tax_rate', 'tax_amount', 'grand_total',
        'amount_in_words', 'balance_due', 'status', 'collection_stage',
        'assigned_agent_id', 'pdf_path', 'imported_by', 'imported_at',
        'promise_date', 'notes',
    ];

    protected $casts = [
        'pi_date' => 'date', 'due_date' => 'date',
        'usage_period_start' => 'date', 'usage_period_end' => 'date',
        'promise_date' => 'date', 'imported_at' => 'datetime',
        'subtotal' => 'decimal:2', 'tax_amount' => 'decimal:2',
        'grand_total' => 'decimal:2', 'balance_due' => 'decimal:2',
    ];

    public function client() { return $this->belongsTo(Client::class); }
    public function assignedAgent() { return $this->belongsTo(User::class, 'assigned_agent_id'); }
    public function lineItems() { return $this->hasMany(LineItem::class); }
    public function payments() { return $this->hasMany(Payment::class); }
    public function creditNotes() { return $this->hasMany(CreditNote::class); }
    public function interactions() { return $this->hasMany(Interaction::class); }

    public function getOutstandingAttribute(): float
    {
        return max(0, (float) $this->balance_due);
    }

    public function isOverdue(): bool
    {
        return $this->due_date->isPast() && $this->status !== 'paid';
    }
}
