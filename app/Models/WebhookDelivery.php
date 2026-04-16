<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebhookDelivery extends Model
{
    protected $fillable = [
        'developer_app_id', 'tenant_id', 'event', 'payload', 'endpoint_url',
        'status', 'attempts', 'response_code', 'response_body',
        'error_message', 'delivered_at', 'next_retry_at',
    ];

    protected $casts = [
        'payload'      => 'array',
        'delivered_at' => 'datetime',
        'next_retry_at'=> 'datetime',
    ];

    public function app() { return $this->belongsTo(DeveloperApp::class, 'developer_app_id'); }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'delivered' => '<span class="badge bg-success-subtle text-success"><i class="bi bi-check-circle me-1"></i>Delivered</span>',
            'failed'    => '<span class="badge bg-danger-subtle text-danger"><i class="bi bi-x-circle me-1"></i>Failed</span>',
            'pending'   => '<span class="badge bg-warning-subtle text-warning"><i class="bi bi-clock me-1"></i>Pending</span>',
            default     => '<span class="badge bg-secondary-subtle text-secondary">' . ucfirst($this->status) . '</span>',
        };
    }
}
