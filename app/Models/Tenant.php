<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    protected $fillable = [
        'name', 'slug', 'email', 'phone', 'website', 'address', 'industry',
        'plan', 'status', 'max_users', 'max_contacts', 'currency', 'timezone',
    ];

    protected $attributes = [
        'plan' => 'free',
        'status' => 'active',
        'max_users' => 5,
        'max_contacts' => 500,
        'currency' => 'USD',
    ];

    public function users() { return $this->hasMany(User::class); }
    public function contacts() { return $this->hasMany(Contact::class); }
    public function companies() { return $this->hasMany(Company::class); }
    public function leads() { return $this->hasMany(Lead::class); }
    public function deals() { return $this->hasMany(Deal::class); }
    public function invoices() { return $this->hasMany(Invoice::class); }
    public function pipelineStages() { return $this->hasMany(PipelineStage::class); }
}
