<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model
{
    protected $fillable = ['label', 'bank_name', 'account_number_last4', 'branch', 'ifsc', 'is_active'];
    protected $casts = ['is_active' => 'boolean'];

    public function payments() { return $this->hasMany(Payment::class); }
}
