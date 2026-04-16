<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Contact;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    private function tid() { return Auth::user()->tenant_id; }

    private function nextNumber()
    {
        $last = Invoice::where('tenant_id', $this->tid())->max('id') ?? 0;
        return 'INV-' . str_pad($last + 1, 5, '0', STR_PAD_LEFT);
    }

    public function index(Request $request)
    {
        $query = Invoice::where('tenant_id', $this->tid())->with(['contact', 'company']);
        if ($request->search) {
            $q = $request->search;
            $query->where(fn($qb) => $qb->where('invoice_number', 'like', "%$q%"));
        }
        if ($request->status) $query->where('status', $request->status);
        $invoices = $query->latest()->paginate(15)->withQueryString();
        $totals = Invoice::where('tenant_id', $this->tid())->selectRaw('status, SUM(total) as total')->groupBy('status')->pluck('total', 'status');
        return view('invoices.index', compact('invoices', 'totals'));
    }

    public function create()
    {
        $contacts = Contact::where('tenant_id', $this->tid())->orderBy('first_name')->get();
        $companies = Company::where('tenant_id', $this->tid())->orderBy('name')->get();
        $nextNumber = $this->nextNumber();
        return view('invoices.create', compact('contacts', 'companies', 'nextNumber'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'invoice_number' => 'required|string|unique:invoices,invoice_number',
            'contact_id' => 'nullable|exists:contacts,id',
            'company_id' => 'nullable|exists:companies,id',
            'status' => 'required|in:draft,sent,paid,overdue,cancelled',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'discount' => 'nullable|numeric|min:0',
            'currency' => 'required|string|max:3',
            'issue_date' => 'required|date',
            'due_date' => 'required|date',
            'notes' => 'nullable|string',
            'terms' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        DB::transaction(function() use ($data, $request) {
            $invoice = Invoice::create([
                'tenant_id' => $this->tid(),
                'created_by' => Auth::id(),
                'invoice_number' => $data['invoice_number'],
                'contact_id' => $data['contact_id'] ?? null,
                'company_id' => $data['company_id'] ?? null,
                'status' => $data['status'],
                'tax_rate' => $data['tax_rate'] ?? 0,
                'discount' => $data['discount'] ?? 0,
                'currency' => $data['currency'],
                'issue_date' => $data['issue_date'],
                'due_date' => $data['due_date'],
                'notes' => $data['notes'] ?? null,
                'terms' => $data['terms'] ?? null,
                'subtotal' => 0, 'tax_amount' => 0, 'total' => 0,
            ]);
            foreach ($data['items'] as $item) {
                $invoice->items()->create([
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total' => $item['quantity'] * $item['unit_price'],
                ]);
            }
            $invoice->recalculate();
        });

        return redirect()->route('invoices.index')->with('success', 'Invoice created!');
    }

    public function show(Invoice $invoice)
    {
        abort_if($invoice->tenant_id !== $this->tid(), 403);
        $invoice->load(['contact', 'company', 'items', 'creator']);
        return view('invoices.show', compact('invoice'));
    }

    public function edit(Invoice $invoice)
    {
        abort_if($invoice->tenant_id !== $this->tid(), 403);
        $contacts = Contact::where('tenant_id', $this->tid())->orderBy('first_name')->get();
        $companies = Company::where('tenant_id', $this->tid())->orderBy('name')->get();
        $invoice->load('items');
        return view('invoices.edit', compact('invoice', 'contacts', 'companies'));
    }

    public function update(Request $request, Invoice $invoice)
    {
        abort_if($invoice->tenant_id !== $this->tid(), 403);
        $data = $request->validate([
            'contact_id' => 'nullable|exists:contacts,id',
            'company_id' => 'nullable|exists:companies,id',
            'status' => 'required|in:draft,sent,paid,overdue,cancelled',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'discount' => 'nullable|numeric|min:0',
            'currency' => 'required|string|max:3',
            'issue_date' => 'required|date',
            'due_date' => 'required|date',
            'notes' => 'nullable|string',
            'terms' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);
        DB::transaction(function() use ($data, $invoice) {
            $invoice->update([
                'contact_id' => $data['contact_id'] ?? null,
                'company_id' => $data['company_id'] ?? null,
                'status' => $data['status'],
                'tax_rate' => $data['tax_rate'] ?? 0,
                'discount' => $data['discount'] ?? 0,
                'currency' => $data['currency'],
                'issue_date' => $data['issue_date'],
                'due_date' => $data['due_date'],
                'notes' => $data['notes'] ?? null,
                'terms' => $data['terms'] ?? null,
                'paid_at' => $data['status'] === 'paid' ? now() : null,
            ]);
            $invoice->items()->delete();
            foreach ($data['items'] as $item) {
                $invoice->items()->create([
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total' => $item['quantity'] * $item['unit_price'],
                ]);
            }
            $invoice->recalculate();
        });
        return redirect()->route('invoices.show', $invoice)->with('success', 'Invoice updated!');
    }

    public function destroy(Invoice $invoice)
    {
        abort_if($invoice->tenant_id !== $this->tid(), 403);
        $invoice->delete();
        return redirect()->route('invoices.index')->with('success', 'Invoice deleted.');
    }

    public function pdf(Invoice $invoice)
    {
        abort_if($invoice->tenant_id !== $this->tid(), 403);
        $invoice->load(['contact', 'company', 'items', 'creator']);
        $tenant = Auth::user()->tenant;
        $pdf = Pdf::loadView('invoices.pdf', compact('invoice', 'tenant'));
        return $pdf->stream("invoice-{$invoice->invoice_number}.pdf");
    }
}
