<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contact extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'tenant_id', 'first_name', 'last_name', 'email', 'phone', 'mobile',
        'company_id', 'job_title', 'source', 'status', 'lead_score',
        'country', 'city', 'notes', 'created_by', 'assigned_to', 'avatar',
    ];

    protected $casts = ['lead_score' => 'integer'];

    public function tenant() { return $this->belongsTo(Tenant::class); }
    public function company() { return $this->belongsTo(Company::class); }
    public function assignedTo() { return $this->belongsTo(User::class, 'assigned_to'); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
    public function deals() { return $this->hasMany(Deal::class); }
    public function leads() { return $this->hasMany(Lead::class); }
    public function tasks() { return $this->hasMany(Task::class); }
    public function invoices() { return $this->hasMany(Invoice::class); }
    public function activities() { return $this->morphMany(Activity::class, 'activityable'); }

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    public function getInitialsAttribute(): string
    {
        $parts = array_filter([$this->first_name, $this->last_name]);
        return strtoupper(implode('', array_map(fn($p) => substr($p, 0, 1), $parts))) ?: '?';
    }

    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'active' => 'success',
            'inactive' => 'secondary',
            'blocked' => 'danger',
            default => 'secondary',
        };
    }

    public function getScoreColorAttribute(): string
    {
        if ($this->lead_score >= 70) return 'success';
        if ($this->lead_score >= 40) return 'warning';
        return 'danger';
    }

    public function getScoreLevelAttribute(): string
    {
        if ($this->lead_score >= 70) return 'hot';
        if ($this->lead_score >= 40) return 'warm';
        return 'cold';
    }
}
