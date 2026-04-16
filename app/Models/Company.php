<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'tenant_id', 'name', 'email', 'phone', 'website', 'industry',
        'size', 'country', 'city', 'address', 'annual_revenue', 'notes', 'created_by',
    ];

    protected $casts = ['annual_revenue' => 'decimal:2'];

    public function tenant() { return $this->belongsTo(Tenant::class); }
    public function contacts() { return $this->hasMany(Contact::class); }
    public function deals() { return $this->hasMany(Deal::class); }
    public function leads() { return $this->hasMany(Lead::class); }
    public function invoices() { return $this->hasMany(Invoice::class); }
}
