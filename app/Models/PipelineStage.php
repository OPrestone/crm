<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PipelineStage extends Model
{
    protected $fillable = ['tenant_id', 'name', 'type', 'color', 'position', 'is_won', 'is_lost'];
    protected $casts = ['is_won' => 'boolean', 'is_lost' => 'boolean', 'position' => 'integer'];

    public function tenant() { return $this->belongsTo(Tenant::class); }
    public function deals() { return $this->hasMany(Deal::class, 'stage_id'); }
    public function leads() { return $this->hasMany(Lead::class, 'stage_id'); }
}
