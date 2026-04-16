<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plugin extends Model
{
    protected $fillable = [
        'name', 'slug', 'description', 'icon', 'color',
        'route_prefix', 'min_plan', 'is_core', 'sort_order', 'active',
    ];

    protected $casts = ['is_core' => 'boolean', 'active' => 'boolean'];

    public static $planHierarchy = ['free' => 0, 'starter' => 1, 'pro' => 2, 'enterprise' => 3];

    public function tenantPlugins()
    {
        return $this->hasMany(TenantPlugin::class);
    }

    public function isIncludedInPlan(string $plan): bool
    {
        $planLevel = self::$planHierarchy[$plan] ?? 0;
        $minLevel  = self::$planHierarchy[$this->min_plan] ?? 0;
        return $planLevel >= $minLevel;
    }

    public static function allPlugins(): \Illuminate\Database\Eloquent\Collection
    {
        return static::orderBy('sort_order')->get();
    }
}
