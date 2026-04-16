<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TenantPlugin extends Model
{
    protected $fillable = ['tenant_id', 'plugin_id', 'enabled', 'is_override'];
    protected $casts    = ['enabled' => 'boolean', 'is_override' => 'boolean'];

    public function tenant() { return $this->belongsTo(Tenant::class); }
    public function plugin() { return $this->belongsTo(Plugin::class); }
}
