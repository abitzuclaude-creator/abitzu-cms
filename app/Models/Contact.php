<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    protected $fillable = [
        'client_id', 'location_id', 'contact_type', 'name',
        'email', 'phone1', 'phone2', 'is_editable_by_agent', 'added_by',
    ];

    protected $casts = ['is_editable_by_agent' => 'boolean'];

    public function client() { return $this->belongsTo(Client::class); }
    public function location() { return $this->belongsTo(Location::class); }
    public function addedBy() { return $this->belongsTo(User::class, 'added_by'); }
}
