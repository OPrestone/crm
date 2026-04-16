<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ticket extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'tenant_id', 'ticket_number', 'contact_id', 'company_id', 'assigned_to',
        'subject', 'description', 'status', 'priority', 'category', 'channel',
        'resolved_at', 'first_response_at', 'created_by',
    ];

    protected $casts = [
        'resolved_at'       => 'datetime',
        'first_response_at' => 'datetime',
    ];

    public function tenant()     { return $this->belongsTo(Tenant::class); }
    public function contact()    { return $this->belongsTo(Contact::class); }
    public function company()    { return $this->belongsTo(Company::class); }
    public function assignedTo() { return $this->belongsTo(User::class, 'assigned_to'); }
    public function creator()    { return $this->belongsTo(User::class, 'created_by'); }
    public function replies()    { return $this->hasMany(TicketReply::class)->orderBy('created_at'); }

    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'open'        => 'primary',
            'pending'     => 'warning',
            'in_progress' => 'info',
            'resolved'    => 'success',
            'closed'      => 'secondary',
            default       => 'secondary',
        };
    }

    public function getPriorityBadgeAttribute(): string
    {
        return match($this->priority) {
            'urgent' => 'danger',
            'high'   => 'warning',
            'medium' => 'info',
            'low'    => 'secondary',
            default  => 'secondary',
        };
    }
}
