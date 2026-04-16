<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IdVerification extends Model
{

    protected $fillable = [
        'tenant_id','contact_id','created_by','reviewed_by',
        'full_name','id_type','id_number','date_of_birth',
        'issue_date','expiry_date','issuing_country','nationality',
        'gender','address','document_front','document_back','selfie',
        'status','risk_level','confidence_score','notes','rejection_reason','verified_at',
    ];

    protected $casts = [
        'date_of_birth'  => 'date',
        'issue_date'     => 'date',
        'expiry_date'    => 'date',
        'verified_at'    => 'datetime',
    ];

    public function tenant()    { return $this->belongsTo(Tenant::class); }
    public function contact()   { return $this->belongsTo(Contact::class); }
    public function creator()   { return $this->belongsTo(User::class, 'created_by'); }
    public function reviewer()  { return $this->belongsTo(User::class, 'reviewed_by'); }

    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'verified'     => 'success',
            'rejected'     => 'danger',
            'under_review' => 'warning',
            'expired'      => 'secondary',
            default        => 'info',
        };
    }

    public function getRiskBadgeAttribute(): string
    {
        return match($this->risk_level) {
            'high'   => 'danger',
            'medium' => 'warning',
            default  => 'success',
        };
    }

    public function isExpired(): bool
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }

    public function getIdTypeLabelAttribute(): string
    {
        return match($this->id_type) {
            'passport'          => 'Passport',
            'national_id'       => 'National ID',
            'driver_license'    => "Driver's License",
            'residence_permit'  => 'Residence Permit',
            default             => ucfirst(str_replace('_', ' ', $this->id_type)),
        };
    }
}
