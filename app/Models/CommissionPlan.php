<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommissionPlan extends Model
{
    protected $fillable = [
        'tenant_id','name','type','rate','min_deal_value','tiers','is_active','created_by',
    ];

    protected $casts = [
        'rate'           => 'decimal:4',
        'min_deal_value' => 'decimal:2',
        'tiers'          => 'array',
        'is_active'      => 'boolean',
    ];

    public function tenant()      { return $this->belongsTo(Tenant::class); }
    public function creator()     { return $this->belongsTo(User::class, 'created_by'); }
    public function commissions() { return $this->hasMany(Commission::class, 'plan_id'); }

    public function calculate(float $dealValue): float
    {
        if ($dealValue < $this->min_deal_value) return 0;
        return match($this->type) {
            'flat'       => (float) $this->rate,
            'percentage' => round($dealValue * $this->rate / 100, 2),
            'tiered'     => $this->calculateTiered($dealValue),
            default      => 0,
        };
    }

    private function calculateTiered(float $dealValue): float
    {
        $tiers = $this->tiers ?? [];
        $commission = 0;
        foreach ($tiers as $tier) {
            $min = $tier['min'] ?? 0;
            $max = $tier['max'] ?? PHP_INT_MAX;
            $rate = $tier['rate'] ?? 0;
            if ($dealValue >= $min) {
                $applicable = min($dealValue, $max) - $min;
                $commission += $applicable * $rate / 100;
            }
        }
        return round($commission, 2);
    }
}
