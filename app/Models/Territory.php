<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Territory extends Model
{
    protected $fillable = ['tenant_id','name','description','type','rules','color','created_by'];

    protected $casts = ['rules' => 'array'];

    public function tenant()  { return $this->belongsTo(Tenant::class); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
    public function users()   { return $this->belongsToMany(User::class, 'territory_user'); }

    public function getTypeBadgeAttribute(): string
    {
        return match($this->type) {
            'geographic' => 'primary',
            'account'    => 'success',
            'industry'   => 'warning',
            default      => 'secondary',
        };
    }
}
