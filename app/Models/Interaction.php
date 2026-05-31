<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Interaction extends Model
{
    protected $fillable = [
        'client_id', 'proforma_invoice_id', 'contact_id', 'user_id',
        'type', 'interaction_date', 'duration', 'disposition',
        'whatsapp_template', 'notes',
    ];

    protected $casts = ['interaction_date' => 'datetime'];

    public function client() { return $this->belongsTo(Client::class); }
    public function proformaInvoice() { return $this->belongsTo(ProformaInvoice::class); }
    public function contact() { return $this->belongsTo(Contact::class); }
    public function user() { return $this->belongsTo(User::class); }
}
