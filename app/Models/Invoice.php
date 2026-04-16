<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'tenant_id', 'invoice_number', 'contact_id', 'company_id', 'created_by',
        'status', 'subtotal', 'tax_rate', 'tax_amount', 'discount', 'total',
        'currency', 'issue_date', 'due_date', 'paid_at', 'notes', 'terms',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'due_date' => 'date',
        'paid_at' => 'date',
        'subtotal' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function tenant() { return $this->belongsTo(Tenant::class); }
    public function contact() { return $this->belongsTo(Contact::class); }
    public function company() { return $this->belongsTo(Company::class); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
    public function items() { return $this->hasMany(InvoiceItem::class); }

    public function recalculate(): void
    {
        $subtotal = $this->items()->sum('total');
        $taxAmount = $subtotal * ($this->tax_rate / 100);
        $total = $subtotal + $taxAmount - $this->discount;
        $this->update(['subtotal' => $subtotal, 'tax_amount' => $taxAmount, 'total' => max(0, $total)]);
    }

    public function isOverdue(): bool
    {
        return $this->due_date->isPast() && !in_array($this->status, ['paid', 'cancelled']);
    }

    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'paid' => 'success',
            'sent' => 'info',
            'draft' => 'secondary',
            'overdue' => 'danger',
            'cancelled' => 'dark',
            default => 'secondary',
        };
    }
}
