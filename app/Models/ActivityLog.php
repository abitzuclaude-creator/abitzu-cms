<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $table = 'activity_log';
    public $timestamps = false;
    protected $fillable = ['user_id', 'action', 'model_type', 'model_id', 'changes', 'ip_address', 'created_at'];
    protected $casts = ['changes' => 'array', 'created_at' => 'datetime'];

    public function user() { return $this->belongsTo(User::class); }
}
