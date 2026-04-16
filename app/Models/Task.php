<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = [
        'tenant_id', 'title', 'description', 'type', 'status', 'priority',
        'due_date', 'completed_at', 'assigned_to', 'created_by',
        'taskable_id', 'taskable_type',
    ];

    protected $casts = ['due_date' => 'datetime', 'completed_at' => 'datetime'];

    public function tenant() { return $this->belongsTo(Tenant::class); }
    public function assignedTo() { return $this->belongsTo(User::class, 'assigned_to'); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
    public function taskable() { return $this->morphTo(); }

    public function isOverdue(): bool
    {
        return $this->due_date && $this->due_date->isPast() && !in_array($this->status, ['completed', 'cancelled']);
    }

    public function getTypeIconAttribute(): string
    {
        return match($this->type) {
            'call' => 'telephone-fill',
            'email' => 'envelope-fill',
            'meeting' => 'calendar-event-fill',
            default => 'check2-square',
        };
    }

    public function getPriorityBadgeAttribute(): string
    {
        return match($this->priority) {
            'urgent' => 'danger',
            'high' => 'warning',
            'medium' => 'info',
            default => 'secondary',
        };
    }

    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'completed' => 'success',
            'in_progress' => 'primary',
            'cancelled' => 'secondary',
            default => 'warning',
        };
    }
}
