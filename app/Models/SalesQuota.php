<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesQuota extends Model
{
    protected $fillable = ['tenant_id','user_id','period','amount'];

    protected $casts = ['amount' => 'decimal:2'];

    public function tenant() { return $this->belongsTo(Tenant::class); }
    public function user()   { return $this->belongsTo(User::class); }
}
