<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebForm extends Model
{
    protected $fillable = [
        'tenant_id','name','description','fields','submit_action',
        'success_message','redirect_url','is_active','created_by',
    ];

    protected $casts = [
        'fields'    => 'array',
        'is_active' => 'boolean',
    ];

    public function tenant()      { return $this->belongsTo(Tenant::class); }
    public function creator()     { return $this->belongsTo(User::class, 'created_by'); }
    public function submissions() { return $this->hasMany(WebFormSubmission::class, 'form_id'); }

    public function getEmbedCodeAttribute(): string
    {
        $url = url("/forms/{$this->id}");
        return '<iframe src="' . $url . '" width="100%" height="600" frameborder="0" style="border:none;"></iframe>';
    }
}
