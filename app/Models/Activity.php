<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    protected $fillable = [
        'tenant_id', 'user_id', 'type', 'subject', 'description',
        'activityable_id', 'activityable_type',
    ];

    public function tenant() { return $this->belongsTo(Tenant::class); }
    public function user() { return $this->belongsTo(User::class); }
    public function activityable() { return $this->morphTo(); }

    public function getTypeIconAttribute(): string
    {
        return match($this->type) {
            'created' => 'plus-circle-fill',
            'updated' => 'pencil-fill',
            'deleted' => 'trash-fill',
            'call' => 'telephone-fill',
            'email' => 'envelope-fill',
            'meeting' => 'calendar-event-fill',
            'note' => 'chat-dots-fill',
            default => 'circle-fill',
        };
    }

    public function getTypeColorAttribute(): string
    {
        return match($this->type) {
            'created' => 'success',
            'updated' => 'info',
            'deleted' => 'danger',
            'call' => 'primary',
            'email' => 'warning',
            default => 'secondary',
        };
    }
}
