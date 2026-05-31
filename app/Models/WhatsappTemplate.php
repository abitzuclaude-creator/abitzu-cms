<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsappTemplate extends Model
{
    protected $fillable = ['type', 'template_body', 'updated_by'];

    public function updatedBy() { return $this->belongsTo(User::class, 'updated_by'); }
}
