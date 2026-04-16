<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class DeveloperApp extends Model
{
    protected $fillable = [
        'tenant_id', 'created_by', 'name', 'description',
        'client_id', 'client_secret',
        'webhook_url', 'webhook_events', 'allowed_ips',
        'rate_limit', 'is_active', 'last_used_at', 'total_requests',
    ];

    protected $casts = [
        'webhook_events' => 'array',
        'allowed_ips'    => 'array',
        'is_active'      => 'boolean',
        'last_used_at'   => 'datetime',
    ];

    protected $hidden = ['client_secret'];

    public static function generateClientId(): string
    {
        return 'crm_' . Str::random(32);
    }

    public static function generateSecret(): string
    {
        return 'sk_' . Str::random(48);
    }

    public function tenant() { return $this->belongsTo(Tenant::class); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
    public function logs() { return $this->hasMany(ApiLog::class); }
    public function webhookDeliveries() { return $this->hasMany(WebhookDelivery::class); }

    public function getMaskedSecretAttribute(): string
    {
        return substr($this->client_secret, 0, 7) . str_repeat('•', 20);
    }

    public function getStatusBadgeAttribute(): string
    {
        return $this->is_active
            ? '<span class="badge bg-success-subtle text-success">Active</span>'
            : '<span class="badge bg-secondary-subtle text-secondary">Inactive</span>';
    }

    public static function allEvents(): array
    {
        return [
            'contact.created'  => 'Contact Created',
            'contact.updated'  => 'Contact Updated',
            'contact.deleted'  => 'Contact Deleted',
            'deal.created'     => 'Deal Created',
            'deal.updated'     => 'Deal Updated',
            'deal.won'         => 'Deal Won',
            'deal.lost'        => 'Deal Lost',
            'lead.created'     => 'Lead Created',
            'lead.converted'   => 'Lead Converted',
            'invoice.created'  => 'Invoice Created',
            'invoice.paid'     => 'Invoice Paid',
            'invoice.overdue'  => 'Invoice Overdue',
            'ticket.created'   => 'Ticket Created',
            'ticket.resolved'  => 'Ticket Resolved',
            'task.completed'   => 'Task Completed',
            'quote.accepted'   => 'Quote Accepted',
        ];
    }
}
