<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Quote extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'tenant_id', 'quote_number', 'contact_id', 'company_id', 'deal_id',
        'title', 'status', 'issue_date', 'valid_until',
        'subtotal', 'tax_rate', 'tax_amount', 'discount', 'total',
        'currency', 'notes', 'terms', 'created_by',
    ];

    protected $casts = [
        'issue_date'  => 'date',
        'valid_until' => 'date',
        'subtotal'    => 'decimal:2',
        'tax_rate'    => 'decimal:2',
        'tax_amount'  => 'decimal:2',
        'discount'    => 'decimal:2',
        'total'       => 'decimal:2',
    ];

    public function tenant()  { return $this->belongsTo(Tenant::class); }
    public function contact() { return $this->belongsTo(Contact::class); }
    public function company() { return $this->belongsTo(Company::class); }
    public function deal()    { return $this->belongsTo(Deal::class); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
    public function items()   { return $this->hasMany(QuoteItem::class)->orderBy('sort_order'); }

    public function recalculate(): void
    {
        $subtotal = $this->items->sum('total');
        $discount = $this->discount;
        $taxable  = $subtotal - $discount;
        $taxAmt   = round($taxable * ($this->tax_rate / 100), 2);
        $this->update([
            'subtotal'   => $subtotal,
            'tax_amount' => $taxAmt,
            'total'      => $taxable + $taxAmt,
        ]);
    }

    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'draft'    => 'secondary',
            'sent'     => 'info',
            'accepted' => 'success',
            'rejected' => 'danger',
            'expired'  => 'warning',
            default    => 'secondary',
        };
    }

    public function isExpired(): bool
    {
        return $this->valid_until && $this->valid_until->isPast() && $this->status !== 'accepted';
    }
}
