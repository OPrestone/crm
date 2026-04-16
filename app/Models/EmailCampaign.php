<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailCampaign extends Model
{
    protected $fillable = [
        'tenant_id','name','subject','from_name','from_email','body',
        'status','segment','scheduled_at','sent_at','created_by',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'sent_at'      => 'datetime',
    ];

    public function tenant()    { return $this->belongsTo(Tenant::class); }
    public function creator()   { return $this->belongsTo(User::class, 'created_by'); }
    public function recipients(){ return $this->hasMany(EmailCampaignContact::class, 'campaign_id'); }

    public function getOpenRateAttribute(): float
    {
        $sent = $this->recipients()->whereNotNull('sent_at')->count();
        if (!$sent) return 0;
        return round($this->recipients()->whereNotNull('opened_at')->count() / $sent * 100, 1);
    }

    public function getClickRateAttribute(): float
    {
        $sent = $this->recipients()->whereNotNull('sent_at')->count();
        if (!$sent) return 0;
        return round($this->recipients()->whereNotNull('clicked_at')->count() / $sent * 100, 1);
    }

    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'sent'      => 'success',
            'sending'   => 'warning',
            'scheduled' => 'info',
            default     => 'secondary',
        };
    }
}
