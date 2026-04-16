<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = ['name', 'email', 'password', 'tenant_id', 'job_title', 'phone', 'avatar', 'is_active'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function tenant() { return $this->belongsTo(Tenant::class); }
    public function tasks() { return $this->hasMany(Task::class, 'assigned_to'); }
    public function activities() { return $this->hasMany(Activity::class); }
    public function crmNotifications() { return $this->hasMany(CrmNotification::class); }
    public function unreadNotifications() { return $this->crmNotifications()->whereNull('read_at'); }

    public function isSuperAdmin(): bool { return $this->hasRole('super_admin'); }
    public function isTenantAdmin(): bool { return $this->hasRole(['tenant_admin', 'super_admin']); }

    public function getInitialsAttribute(): string
    {
        $words = explode(' ', trim($this->name));
        return strtoupper(implode('', array_map(fn($w) => substr($w, 0, 1), array_slice($words, 0, 2))));
    }
}
