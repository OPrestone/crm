<div class="row g-3">
    <div class="col-md-8">
        <label class="form-label fw-600">Product Name <span class="text-danger">*</span></label>
        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $product->name ?? '') }}" required>
        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label fw-600">SKU</label>
        <input type="text" name="sku" class="form-control @error('sku') is-invalid @enderror" value="{{ old('sku', $product->sku ?? '') }}" placeholder="e.g. PROD-001">
        @error('sku')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-12">
        <label class="form-label fw-600">Description</label>
        <textarea name="description" rows="3" class="form-control">{{ old('description', $product->description ?? '') }}</textarea>
    </div>
    <div class="col-md-6">
        <label class="form-label fw-600">Category</label>
        <input type="text" name="category" class="form-control" value="{{ old('category', $product->category ?? '') }}" placeholder="e.g. Software, Consulting">
    </div>
    <div class="col-md-6">
        <label class="form-label fw-600">Unit</label>
        <select name="unit" class="form-select">
            @foreach(['unit','hour','day','month','year','license','seat','GB','project'] as $u)
            <option value="{{ $u }}" {{ old('unit', $product->unit ?? 'unit') === $u ? 'selected' : '' }}>{{ ucfirst($u) }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label fw-600">Unit Price <span class="text-danger">*</span></label>
        <div class="input-group">
            <span class="input-group-text">$</span>
            <input type="number" name="unit_price" step="0.01" min="0" class="form-control @error('unit_price') is-invalid @enderror" value="{{ old('unit_price', $product->unit_price ?? '0.00') }}" required>
        </div>
    </div>
    <div class="col-md-4">
        <label class="form-label fw-600">Cost Price</label>
        <div class="input-group">
            <span class="input-group-text">$</span>
            <input type="number" name="cost_price" step="0.01" min="0" class="form-control" value="{{ old('cost_price', $product->cost_price ?? '0.00') }}">
        </div>
    </div>
    <div class="col-md-4">
        <label class="form-label fw-600">Tax Rate (%)</label>
        <div class="input-group">
            <input type="number" name="tax_rate" step="0.01" min="0" max="100" class="form-control" value="{{ old('tax_rate', $product->tax_rate ?? '0.00') }}">
            <span class="input-group-text">%</span>
        </div>
    </div>
    <div class="col-12">
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $product->is_active ?? true) ? 'checked' : '' }}>
            <label class="form-check-label fw-600" for="is_active">Active (available for quotes & invoices)</label>
        </div>
    </div>
</div>
