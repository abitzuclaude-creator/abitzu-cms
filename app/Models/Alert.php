<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alert extends Model
{
    protected $fillable = ['type', 'title', 'description', 'status', 'resolved_notes', 'resolved_by', 'resolved_at'];
    protected $casts = ['resolved_at' => 'datetime'];

    public function resolvedBy() { return $this->belongsTo(User::class, 'resolved_by'); }
}
