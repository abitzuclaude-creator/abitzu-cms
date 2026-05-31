<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $fillable = [
        'business_name', 'business_id', 'invoice_type', 'invoice_name', 'address',
        'gstin', 'gstin_status', 'pan', 'is_active', 'assigned_agent_id',
        'owner_name', 'owner_email', 'owner_phone1', 'owner_phone2',
        'deactivated_at', 'deactivated_by',
    ];

    protected $casts = ['is_active' => 'boolean', 'deactivated_at' => 'datetime'];

    public function assignedAgent() { return $this->belongsTo(User::class, 'assigned_agent_id'); }
    public function locations() { return $this->hasMany(Location::class); }
    public function proformaInvoices() { return $this->hasMany(ProformaInvoice::class); }
    public function contacts() { return $this->hasMany(Contact::class); }
    public function interactions() { return $this->hasMany(Interaction::class); }
    public function payments() { return $this->hasMany(Payment::class); }
}
