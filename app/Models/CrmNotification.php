<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CrmNotification extends Model
{
    protected $table = 'crm_notifications';

    protected $fillable = [
        'tenant_id', 'user_id', 'type', 'title', 'body', 'icon', 'color', 'url', 'read_at',
    ];

    protected $casts = ['read_at' => 'datetime'];

    public function user() { return $this->belongsTo(User::class); }

    public function isRead(): bool { return !is_null($this->read_at); }
    public function markAsRead(): void { $this->update(['read_at' => now()]); }

    public function getIconAttribute($value): string { return $value ?? 'bell-fill'; }
    public function getColorAttribute($value): string { return $value ?? 'primary'; }
}
