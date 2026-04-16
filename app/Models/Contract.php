<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contract extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'tenant_id','title','contract_number','contact_id','deal_id','template_id',
        'content','value','status','start_date','end_date','signed_at','signed_by',
        'notes','created_by',
    ];

    protected $casts = [
        'value'      => 'decimal:2',
        'start_date' => 'date',
        'end_date'   => 'date',
        'signed_at'  => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function ($contract) {
            if (!$contract->contract_number) {
                $contract->contract_number = 'CNT-' . strtoupper(uniqid());
            }
        });
    }

    public function tenant()   { return $this->belongsTo(Tenant::class); }
    public function contact()  { return $this->belongsTo(Contact::class); }
    public function deal()     { return $this->belongsTo(Deal::class); }
    public function template() { return $this->belongsTo(ContractTemplate::class, 'template_id'); }
    public function creator()  { return $this->belongsTo(User::class, 'created_by'); }

    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'signed'            => 'success',
            'pending_signature' => 'warning',
            'expired'           => 'danger',
            'cancelled'         => 'secondary',
            default             => 'primary',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending_signature' => 'Pending Signature',
            default             => ucfirst($this->status),
        };
    }
}
