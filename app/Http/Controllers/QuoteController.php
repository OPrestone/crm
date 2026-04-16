<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Company;
use App\Models\Deal;
use App\Models\Product;
use App\Models\Quote;
use App\Models\QuoteItem;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class QuoteController extends Controller
{
    private function tid(): int { return auth()->user()->tenant_id; }

    public function index(Request $request)
    {
        $query = Quote::where('tenant_id', $this->tid())->with(['contact', 'company']);
        if ($request->search) $query->where('title', 'like', "%{$request->search}%")
            ->orWhere('quote_number', 'like', "%{$request->search}%");
        if ($request->status) $query->where('status', $request->status);
        $quotes = $query->latest()->paginate(20)->withQueryString();
        $stats  = [
            'total'    => Quote::where('tenant_id', $this->tid())->count(),
            'draft'    => Quote::where('tenant_id', $this->tid())->where('status','draft')->count(),
            'sent'     => Quote::where('tenant_id', $this->tid())->where('status','sent')->count(),
            'accepted' => Quote::where('tenant_id', $this->tid())->where('status','accepted')->count(),
            'value'    => Quote::where('tenant_id', $this->tid())->where('status','accepted')->sum('total'),
        ];
        return view('quotes.index', compact('quotes', 'stats'));
    }

    public function create(Request $request)
    {
        $contacts = Contact::where('tenant_id', $this->tid())->orderBy('first_name')->get();
        $companies = Company::where('tenant_id', $this->tid())->orderBy('name')->get();
        $deals = Deal::where('tenant_id', $this->tid())->where('status','open')->orderBy('title')->get();
        $products = Product::where('tenant_id', $this->tid())->where('is_active', true)->orderBy('name')->get();
        $nextNum = 'QUO-' . str_pad(Quote::where('tenant_id', $this->tid())->count() + 1, 5, '0', STR_PAD_LEFT);
        $preContactId = $request->contact_id;
        return view('quotes.create', compact('contacts', 'companies', 'deals', 'products', 'nextNum', 'preContactId'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'quote_number' => 'required|string|unique:quotes,quote_number',
            'title'        => 'required|string|max:255',
            'contact_id'   => 'nullable|exists:contacts,id',
            'company_id'   => 'nullable|exists:companies,id',
            'deal_id'      => 'nullable|exists:deals,id',
            'status'       => 'required|in:draft,sent,accepted,rejected,expired',
            'issue_date'   => 'required|date',
            'valid_until'  => 'nullable|date',
            'tax_rate'     => 'nullable|numeric|min:0|max:100',
            'discount'     => 'nullable|numeric|min:0',
            'currency'     => 'required|string|max:3',
            'notes'        => 'nullable|string',
            'terms'        => 'nullable|string',
            'items'        => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.quantity'    => 'required|numeric|min:0.01',
            'items.*.unit_price'  => 'required|numeric|min:0',
            'items.*.discount'    => 'nullable|numeric|min:0|max:100',
            'items.*.product_id'  => 'nullable|exists:products,id',
        ]);

        $quote = Quote::create([
            'tenant_id'    => $this->tid(),
            'quote_number' => $data['quote_number'],
            'title'        => $data['title'],
            'contact_id'   => $data['contact_id'] ?? null,
            'company_id'   => $data['company_id'] ?? null,
            'deal_id'      => $data['deal_id'] ?? null,
            'status'       => $data['status'],
            'issue_date'   => $data['issue_date'],
            'valid_until'  => $data['valid_until'] ?? null,
            'tax_rate'     => $data['tax_rate'] ?? 0,
            'discount'     => $data['discount'] ?? 0,
            'currency'     => $data['currency'],
            'notes'        => $data['notes'] ?? null,
            'terms'        => $data['terms'] ?? null,
            'created_by'   => auth()->id(),
            'subtotal'     => 0, 'tax_amount' => 0, 'total' => 0,
        ]);

        foreach ($data['items'] as $i => $item) {
            $lineTotal = round($item['quantity'] * $item['unit_price'] * (1 - (($item['discount'] ?? 0) / 100)), 2);
            QuoteItem::create([
                'quote_id'    => $quote->id,
                'product_id'  => $item['product_id'] ?? null,
                'description' => $item['description'],
                'quantity'    => $item['quantity'],
                'unit_price'  => $item['unit_price'],
                'discount'    => $item['discount'] ?? 0,
                'total'       => $lineTotal,
                'sort_order'  => $i,
            ]);
        }

        $quote->load('items');
        $quote->recalculate();

        return redirect()->route('quotes.show', $quote)->with('success', "Quote {$quote->quote_number} created.");
    }

    public function show(Quote $quote)
    {
        abort_if($quote->tenant_id !== $this->tid(), 403);
        $quote->load(['contact', 'company', 'deal', 'items.product', 'creator']);
        return view('quotes.show', compact('quote'));
    }

    public function edit(Quote $quote)
    {
        abort_if($quote->tenant_id !== $this->tid(), 403);
        $quote->load('items.product');
        $contacts  = Contact::where('tenant_id', $this->tid())->orderBy('first_name')->get();
        $companies = Company::where('tenant_id', $this->tid())->orderBy('name')->get();
        $deals     = Deal::where('tenant_id', $this->tid())->where('status','open')->orderBy('title')->get();
        $products  = Product::where('tenant_id', $this->tid())->where('is_active', true)->orderBy('name')->get();
        return view('quotes.edit', compact('quote', 'contacts', 'companies', 'deals', 'products'));
    }

    public function update(Request $request, Quote $quote)
    {
        abort_if($quote->tenant_id !== $this->tid(), 403);
        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'contact_id'  => 'nullable|exists:contacts,id',
            'company_id'  => 'nullable|exists:companies,id',
            'deal_id'     => 'nullable|exists:deals,id',
            'status'      => 'required|in:draft,sent,accepted,rejected,expired',
            'issue_date'  => 'required|date',
            'valid_until' => 'nullable|date',
            'tax_rate'    => 'nullable|numeric|min:0|max:100',
            'discount'    => 'nullable|numeric|min:0',
            'currency'    => 'required|string|max:3',
            'notes'       => 'nullable|string',
            'terms'       => 'nullable|string',
            'items'       => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.quantity'    => 'required|numeric|min:0.01',
            'items.*.unit_price'  => 'required|numeric|min:0',
            'items.*.discount'    => 'nullable|numeric|min:0|max:100',
            'items.*.product_id'  => 'nullable|exists:products,id',
        ]);

        $quote->update([
            'title'       => $data['title'],
            'contact_id'  => $data['contact_id'] ?? null,
            'company_id'  => $data['company_id'] ?? null,
            'deal_id'     => $data['deal_id'] ?? null,
            'status'      => $data['status'],
            'issue_date'  => $data['issue_date'],
            'valid_until' => $data['valid_until'] ?? null,
            'tax_rate'    => $data['tax_rate'] ?? 0,
            'discount'    => $data['discount'] ?? 0,
            'currency'    => $data['currency'],
            'notes'       => $data['notes'] ?? null,
            'terms'       => $data['terms'] ?? null,
        ]);

        $quote->items()->delete();
        foreach ($data['items'] as $i => $item) {
            $lineTotal = round($item['quantity'] * $item['unit_price'] * (1 - (($item['discount'] ?? 0) / 100)), 2);
            QuoteItem::create([
                'quote_id'    => $quote->id,
                'product_id'  => $item['product_id'] ?? null,
                'description' => $item['description'],
                'quantity'    => $item['quantity'],
                'unit_price'  => $item['unit_price'],
                'discount'    => $item['discount'] ?? 0,
                'total'       => $lineTotal,
                'sort_order'  => $i,
            ]);
        }

        $quote->load('items');
        $quote->recalculate();

        return redirect()->route('quotes.show', $quote)->with('success', 'Quote updated.');
    }

    public function destroy(Quote $quote)
    {
        abort_if($quote->tenant_id !== $this->tid(), 403);
        $quote->delete();
        return redirect()->route('quotes.index')->with('success', 'Quote deleted.');
    }

    public function pdf(Quote $quote)
    {
        abort_if($quote->tenant_id !== $this->tid(), 403);
        $quote->load(['contact', 'company', 'items.product', 'creator']);
        $tenant = auth()->user()->tenant;
        $pdf = Pdf::loadView('quotes.pdf', compact('quote', 'tenant'))->setPaper('a4');
        return $pdf->stream("quote-{$quote->quote_number}.pdf");
    }

    public function updateStatus(Request $request, Quote $quote)
    {
        abort_if($quote->tenant_id !== $this->tid(), 403);
        $quote->update(['status' => $request->validate(['status' => 'required|in:draft,sent,accepted,rejected,expired'])['status']]);
        return back()->with('success', 'Quote status updated.');
    }
}
