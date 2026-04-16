<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Invoice;
use Illuminate\Http\Request;

class InvoiceApiController extends BaseApiController
{
    public function index(Request $request)
    {
        $tid   = $this->apiTenantId($request);
        $query = Invoice::where('tenant_id', $tid)->with('contact');

        if ($request->filled('status'))     $query->where('status', $request->status);
        if ($request->filled('contact_id')) $query->where('contact_id', $request->contact_id);

        $query->orderBy('created_at', 'desc');
        return $this->paginate($query, $request, fn($inv) => [
            'id'          => $inv->id,
            'invoice_no'  => $inv->invoice_number ?? 'INV-' . str_pad($inv->id, 5, '0', STR_PAD_LEFT),
            'contact'     => $inv->contact ? ['id' => $inv->contact->id, 'name' => trim($inv->contact->first_name . ' ' . $inv->contact->last_name)] : null,
            'subtotal'    => $inv->subtotal,
            'tax'         => $inv->tax,
            'total'       => $inv->total,
            'status'      => $inv->status,
            'due_date'    => $inv->due_date?->toDateString(),
            'issued_date' => $inv->issued_date?->toDateString(),
            'created_at'  => $inv->created_at?->toIso8601String(),
        ]);
    }

    public function show(Request $request, int $id)
    {
        $inv = Invoice::where('tenant_id', $this->apiTenantId($request))->with('contact','items')->find($id);
        if (!$inv) return $this->notFound('Invoice');
        return $this->success(array_merge($inv->toArray(), [
            'items' => $inv->items->map(fn($i) => ['description' => $i->description, 'qty' => $i->quantity, 'unit_price' => $i->unit_price, 'amount' => $i->amount])->toArray(),
        ]));
    }
}
