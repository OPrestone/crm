<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Appointment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'tenant_id', 'user_id', 'contact_id', 'company_id',
        'title', 'description', 'start_at', 'end_at',
        'location', 'type', 'status', 'color', 'created_by',
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at'   => 'datetime',
    ];

    public function tenant()  { return $this->belongsTo(Tenant::class); }
    public function user()    { return $this->belongsTo(User::class); }
    public function contact() { return $this->belongsTo(Contact::class); }
    public function company() { return $this->belongsTo(Company::class); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }

    public function getDurationMinutesAttribute(): int
    {
        return (int) $this->start_at->diffInMinutes($this->end_at);
    }

    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'scheduled'  => 'primary',
            'completed'  => 'success',
            'cancelled'  => 'danger',
            'no_show'    => 'warning',
            default      => 'secondary',
        };
    }
}
