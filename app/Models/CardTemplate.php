<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CardTemplate extends Model
{
    protected $fillable = ['tenant_id', 'name', 'category', 'design', 'fields', 'created_by'];
    protected $casts = ['design' => 'array', 'fields' => 'array'];

    public function tenant() { return $this->belongsTo(Tenant::class); }
    public function cards() { return $this->hasMany(Card::class, 'template_id'); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
}
