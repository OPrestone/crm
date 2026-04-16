<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lead extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'tenant_id', 'title', 'contact_id', 'company_id', 'stage_id',
        'source', 'status', 'score', 'value', 'notes', 'assigned_to', 'created_by',
    ];

    protected $casts = ['value' => 'decimal:2', 'score' => 'integer'];

    public function tenant() { return $this->belongsTo(Tenant::class); }
    public function contact() { return $this->belongsTo(Contact::class); }
    public function company() { return $this->belongsTo(Company::class); }
    public function stage() { return $this->belongsTo(PipelineStage::class, 'stage_id'); }
    public function assignedTo() { return $this->belongsTo(User::class, 'assigned_to'); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
    public function tasks() { return $this->morphMany(Task::class, 'taskable'); }
    public function activities() { return $this->morphMany(Activity::class, 'activityable'); }

    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'new' => 'secondary',
            'contacted' => 'info',
            'qualified' => 'primary',
            'converted' => 'success',
            'lost' => 'danger',
            default => 'secondary',
        };
    }
}
