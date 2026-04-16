<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Deal extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'tenant_id', 'title', 'contact_id', 'company_id', 'stage_id',
        'value', 'probability', 'expected_close_date', 'status', 'priority',
        'notes', 'assigned_to', 'created_by',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'probability' => 'integer',
        'expected_close_date' => 'date',
    ];

    public function tenant() { return $this->belongsTo(Tenant::class); }
    public function contact() { return $this->belongsTo(Contact::class); }
    public function company() { return $this->belongsTo(Company::class); }
    public function stage() { return $this->belongsTo(PipelineStage::class, 'stage_id'); }
    public function assignedTo() { return $this->belongsTo(User::class, 'assigned_to'); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
    public function tasks() { return $this->hasMany(Task::class); }
    public function activities() { return $this->morphMany(Activity::class, 'activityable'); }

    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'open' => 'primary',
            'won' => 'success',
            'lost' => 'danger',
            default => 'secondary',
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
}
