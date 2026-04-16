<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'tenant_id', 'name', 'sku', 'description', 'category', 'unit',
        'unit_price', 'cost_price', 'tax_rate', 'is_active', 'image', 'created_by',
    ];

    protected $casts = [
        'unit_price'  => 'decimal:2',
        'cost_price'  => 'decimal:2',
        'tax_rate'    => 'decimal:2',
        'is_active'   => 'boolean',
    ];

    public function tenant()  { return $this->belongsTo(Tenant::class); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }

    public function getMarginAttribute(): float
    {
        if ($this->unit_price <= 0) return 0;
        return round((($this->unit_price - $this->cost_price) / $this->unit_price) * 100, 1);
    }
}
