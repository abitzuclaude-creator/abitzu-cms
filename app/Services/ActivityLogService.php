<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;

class ActivityLogService
{
    public function log(int $userId, string $action, Model $model, array $changes = []): void
    {
        ActivityLog::create([
            'user_id'    => $userId,
            'action'     => $action,
            'model_type' => get_class($model),
            'model_id'   => $model->getKey(),
            'changes'    => $changes ?: null,
            'ip_address' => Request::ip(),
            'created_at' => now(),
        ]);
    }
}
