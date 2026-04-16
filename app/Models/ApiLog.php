<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiLog extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'developer_app_id', 'tenant_id', 'method', 'endpoint',
        'status_code', 'ip_address', 'user_agent', 'response_time_ms',
        'request_headers', 'request_body', 'response_body', 'error_message', 'created_at',
    ];

    protected $casts = [
        'request_headers' => 'array',
        'created_at'      => 'datetime',
    ];

    public function app() { return $this->belongsTo(DeveloperApp::class, 'developer_app_id'); }

    public function getStatusColorAttribute(): string
    {
        return match (true) {
            $this->status_code >= 500 => 'danger',
            $this->status_code >= 400 => 'warning',
            $this->status_code >= 300 => 'info',
            default => 'success',
        };
    }

    public function getMethodColorAttribute(): string
    {
        return match ($this->method) {
            'GET'    => 'primary',
            'POST'   => 'success',
            'PUT', 'PATCH' => 'warning',
            'DELETE' => 'danger',
            default  => 'secondary',
        };
    }
}
