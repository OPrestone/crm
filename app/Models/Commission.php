<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Commission extends Model
{
    protected $fillable = [
        'tenant_id','user_id','deal_id','plan_id','deal_value','amount','status','paid_at','notes',
    ];

    protected $casts = [
        'deal_value' => 'decimal:2',
        'amount'     => 'decimal:2',
        'paid_at'    => 'datetime',
    ];

    public function tenant() { return $this->belongsTo(Tenant::class); }
    public function user()   { return $this->belongsTo(User::class); }
    public function deal()   { return $this->belongsTo(Deal::class); }
    public function plan()   { return $this->belongsTo(CommissionPlan::class, 'plan_id'); }

    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'paid'     => 'success',
            'approved' => 'info',
            default    => 'warning',
        };
    }
}
