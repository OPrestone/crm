<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $fillable = [
        'tenant_id','user_id','event','auditable_type','auditable_id',
        'old_values','new_values','ip_address','user_agent','url','method',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function auditable()
    {
        return $this->morphTo();
    }

    public static function record(string $event, mixed $model = null, array $old = [], array $new = []): void
    {
        try {
            $user = auth()->user();
            if (!$user) return;

            static::create([
                'tenant_id'      => $user->tenant_id,
                'user_id'        => $user->id,
                'event'          => $event,
                'auditable_type' => $model ? get_class($model) : null,
                'auditable_id'   => $model?->id,
                'old_values'     => empty($old) ? null : $old,
                'new_values'     => empty($new) ? null : $new,
                'ip_address'     => request()->ip(),
                'user_agent'     => substr(request()->userAgent() ?? '', 0, 500),
                'url'            => request()->fullUrl(),
                'method'         => request()->method(),
            ]);
        } catch (\Throwable) {
        }
    }

    public function getEventLabelAttribute(): string
    {
        return match(true) {
            str_ends_with($this->event, '.created') => 'Created',
            str_ends_with($this->event, '.updated') => 'Updated',
            str_ends_with($this->event, '.deleted') => 'Deleted',
            str_ends_with($this->event, '.login')   => 'Login',
            str_ends_with($this->event, '.logout')  => 'Logout',
            str_ends_with($this->event, '.exported')=> 'Exported',
            str_ends_with($this->event, '.viewed')  => 'Viewed',
            default => ucwords(str_replace('.', ' ', $this->event)),
        };
    }

    public function getEventColorAttribute(): string
    {
        return match(true) {
            str_ends_with($this->event, '.created') => 'success',
            str_ends_with($this->event, '.updated') => 'info',
            str_ends_with($this->event, '.deleted') => 'danger',
            str_ends_with($this->event, '.login')   => 'primary',
            str_ends_with($this->event, '.logout')  => 'secondary',
            default => 'warning',
        };
    }

    public function getResourceTypeAttribute(): string
    {
        if (!$this->auditable_type) return 'System';
        $parts = explode('\\', $this->auditable_type);
        return end($parts);
    }
}
