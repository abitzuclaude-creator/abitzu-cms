<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $fillable = ['client_id', 'location_name', 'location_id', 'address', 'gstin', 'is_active'];
    protected $casts = ['is_active' => 'boolean'];

    public function client() { return $this->belongsTo(Client::class); }
    public function lineItems() { return $this->hasMany(LineItem::class); }
    public function contacts() { return $this->hasMany(Contact::class); }
}
