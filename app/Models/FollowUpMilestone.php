<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FollowUpMilestone extends Model
{
    protected $fillable = ['days_before_due', 'label', 'is_active', 'daily_after_due'];
    protected $casts = ['is_active' => 'boolean', 'daily_after_due' => 'boolean'];
}
