<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContractTemplate extends Model
{
    protected $fillable = ['tenant_id','name','content','variables','created_by'];

    protected $casts = ['variables' => 'array'];

    public function tenant()    { return $this->belongsTo(Tenant::class); }
    public function creator()   { return $this->belongsTo(User::class, 'created_by'); }
    public function contracts() { return $this->hasMany(Contract::class, 'template_id'); }
}
