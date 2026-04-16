<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Goal extends Model
{
    protected $fillable = [
        'tenant_id', 'user_id', 'title', 'description', 'type', 'period',
        'target_value', 'current_value', 'start_date', 'end_date', 'status', 'created_by',
    ];

    protected $casts = [
        'target_value'  => 'decimal:2',
        'current_value' => 'decimal:2',
        'start_date'    => 'date',
        'end_date'      => 'date',
    ];

    public function tenant()  { return $this->belongsTo(Tenant::class); }
    public function user()    { return $this->belongsTo(User::class); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }

    public function getProgressPercentAttribute(): int
    {
        if ($this->target_value <= 0) return 0;
        return min(100, (int) round(($this->current_value / $this->target_value) * 100));
    }

    public function getProgressColorAttribute(): string
    {
        $p = $this->progress_percent;
        if ($p >= 100) return 'success';
        if ($p >= 66)  return 'info';
        if ($p >= 33)  return 'warning';
        return 'danger';
    }

    public function getTypeIconAttribute(): string
    {
        return match($this->type) {
            'revenue'          => 'bi-currency-dollar',
            'deals_won'        => 'bi-trophy-fill',
            'leads_created'    => 'bi-funnel-fill',
            'contacts_added'   => 'bi-person-plus-fill',
            'calls_made'       => 'bi-telephone-fill',
            'demos_scheduled'  => 'bi-calendar-check-fill',
            default            => 'bi-bullseye',
        };
    }
}
