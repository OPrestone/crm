<div class="row g-4">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header fw-600">Quote Details</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-600">Quote Number <span class="text-danger">*</span></label>
                        <input type="text" name="quote_number" class="form-control" value="{{ old('quote_number', $quote->quote_number ?? $nextNum ?? '') }}" {{ isset($quote) ? 'readonly' : '' }} required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-600">Status</label>
                        <select name="status" class="form-select">
                            @foreach(['draft','sent','accepted','rejected','expired'] as $s)
                            <option value="{{ $s }}" {{ old('status', $quote->status ?? 'draft') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-600">Title / Subject <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" value="{{ old('title', $quote->title ?? '') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-600">Contact</label>
                        <select name="contact_id" class="form-select">
                            <option value="">— Select Contact —</option>
                            @foreach($contacts as $c)
                            <option value="{{ $c->id }}" {{ old('contact_id', $quote->contact_id ?? $preContactId ?? '') == $c->id ? 'selected' : '' }}>{{ $c->full_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-600">Company</label>
                        <select name="company_id" class="form-select">
                            <option value="">— Select Company —</option>
                            @foreach($companies as $co)
                            <option value="{{ $co->id }}" {{ old('company_id', $quote->company_id ?? '') == $co->id ? 'selected' : '' }}>{{ $co->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-600">Issue Date <span class="text-danger">*</span></label>
                        <input type="date" name="issue_date" class="form-control" value="{{ old('issue_date', isset($quote) ? $quote->issue_date->format('Y-m-d') : now()->format('Y-m-d')) }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-600">Valid Until</label>
                        <input type="date" name="valid_until" class="form-control" value="{{ old('valid_until', isset($quote) && $quote->valid_until ? $quote->valid_until->format('Y-m-d') : now()->addDays(30)->format('Y-m-d')) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-600">Currency</label>
                        <select name="currency" class="form-select">
                            @foreach(['USD','EUR','GBP','CAD','AUD','JPY','AED','CHF'] as $cur)
                            <option value="{{ $cur }}" {{ old('currency', $quote->currency ?? 'USD') === $cur ? 'selected' : '' }}>{{ $cur }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        {{-- Line Items --}}
        <div class="card mb-4">
            <div class="card-header d-flex align-items-center justify-content-between fw-600">
                Line Items
                <button type="button" class="btn btn-sm btn-outline-primary" id="addLineBtn"><i class="bi bi-plus-lg me-1"></i>Add Line</button>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0" id="lineItemsTable">
                        <thead class="table-light">
                            <tr>
                                <th style="width:35%">Description</th>
                                <th style="width:12%">Qty</th>
                                <th style="width:15%">Unit Price</th>
                                <th style="width:10%">Disc%</th>
                                <th style="width:15%">Total</th>
                                <th style="width:5%"></th>
                            </tr>
                        </thead>
                        <tbody id="lineItems">
                            @php $items = old('items', isset($quote) ? $quote->items->toArray() : [['description'=>'','quantity'=>1,'unit_price'=>0,'discount'=>0,'total'=>0,'product_id'=>null]]); @endphp
                            @foreach($items as $i => $item)
                            <tr class="line-item-row" data-index="{{ $i }}">
                                <td>
                                    <select name="items[{{ $i }}][product_id]" class="form-select form-select-sm mb-1 product-select">
                                        <option value="">— Pick product (optional) —</option>
                                        @foreach($products as $p)
                                        <option value="{{ $p->id }}" data-price="{{ $p->unit_price }}" data-name="{{ $p->name }}" {{ ($item['product_id'] ?? null) == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                                        @endforeach
                                    </select>
                                    <input type="text" name="items[{{ $i }}][description]" class="form-control form-control-sm line-desc" placeholder="Description" value="{{ $item['description'] ?? '' }}" required>
                                </td>
                                <td><input type="number" name="items[{{ $i }}][quantity]" class="form-control form-control-sm line-qty" value="{{ $item['quantity'] ?? 1 }}" min="0.01" step="0.01" required></td>
                                <td><input type="number" name="items[{{ $i }}][unit_price]" class="form-control form-control-sm line-price" value="{{ $item['unit_price'] ?? 0 }}" min="0" step="0.01" required></td>
                                <td><input type="number" name="items[{{ $i }}][discount]" class="form-control form-control-sm line-discount" value="{{ $item['discount'] ?? 0 }}" min="0" max="100" step="0.01"></td>
                                <td><input type="text" class="form-control form-control-sm line-total bg-light" value="{{ number_format(($item['quantity'] ?? 1) * ($item['unit_price'] ?? 0) * (1 - ($item['discount'] ?? 0)/100), 2) }}" readonly></td>
                                <td><button type="button" class="btn btn-sm btn-outline-danger remove-line"><i class="bi bi-x"></i></button></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Notes --}}
        <div class="card">
            <div class="card-header fw-600">Notes &amp; Terms</div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label fw-600">Notes to Client</label>
                    <textarea name="notes" rows="3" class="form-control" placeholder="Thank you for your business...">{{ old('notes', $quote->notes ?? '') }}</textarea>
                </div>
                <div>
                    <label class="form-label fw-600">Terms &amp; Conditions</label>
                    <textarea name="terms" rows="3" class="form-control" placeholder="Payment due within 30 days...">{{ old('terms', $quote->terms ?? '') }}</textarea>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card sticky-top" style="top:80px;">
            <div class="card-header fw-600">Summary</div>
            <div class="card-body">
                <div class="mb-3 d-flex justify-content-between">
                    <span class="text-muted">Subtotal</span>
                    <span id="summarySubtotal">$0.00</span>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-600 small">Discount ($)</label>
                    <input type="number" name="discount" class="form-control form-control-sm" id="discountInput" value="{{ old('discount', $quote->discount ?? 0) }}" min="0" step="0.01">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-600 small">Tax Rate (%)</label>
                    <input type="number" name="tax_rate" class="form-control form-control-sm" id="taxRateInput" value="{{ old('tax_rate', $quote->tax_rate ?? 0) }}" min="0" max="100" step="0.01">
                </div>
                <hr>
                <div class="mb-2 d-flex justify-content-between">
                    <span class="text-muted">Tax Amount</span>
                    <span id="summaryTax">$0.00</span>
                </div>
                <div class="d-flex justify-content-between fw-700 fs-5">
                    <span>Total</span>
                    <span id="summaryTotal" class="text-primary">$0.00</span>
                </div>
                <div class="mt-4 d-grid gap-2">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>{{ isset($quote) ? 'Update Quote' : 'Create Quote' }}</button>
                    <a href="{{ route('quotes.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </div>
        </div>
    </div>
</div>
