<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebFormSubmission extends Model
{
    protected $fillable = ['form_id','tenant_id','data','ip_address','processed'];

    protected $casts = [
        'data'      => 'array',
        'processed' => 'boolean',
    ];

    public function form() { return $this->belongsTo(WebForm::class, 'form_id'); }
}
