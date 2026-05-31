<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = ['name', 'email', 'password', 'phone', 'role', 'is_active', 'last_login_at'];
    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'is_active' => 'boolean',
            'password' => 'hashed',
        ];
    }

    public function isOwner(): bool { return $this->role === 'owner'; }
    public function isAdmin(): bool { return in_array($this->role, ['owner', 'admin']); }
    public function isManager(): bool { return in_array($this->role, ['owner', 'admin', 'manager']); }
    public function isAgent(): bool { return $this->role === 'agent'; }
    public function canManage(): bool { return $this->isManager(); }

    public function assignedClients() { return $this->hasMany(Client::class, 'assigned_agent_id'); }
    public function assignedInvoices() { return $this->hasMany(ProformaInvoice::class, 'assigned_agent_id'); }
    public function interactions() { return $this->hasMany(Interaction::class); }
    public function payments() { return $this->hasMany(Payment::class, 'recorded_by'); }
    public function activityLogs() { return $this->hasMany(ActivityLog::class); }
}
