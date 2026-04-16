@extends('layouts.app')
@section('title', $product->name)
@section('page-title', 'Product Details')

@section('content')
<div class="page-header">
    <div>
        <h1>{{ $product->name }}</h1>
        <nav aria-label="breadcrumb"><ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Products</a></li>
            <li class="breadcrumb-item active">{{ Str::limit($product->name, 30) }}</li>
        </ol></nav>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('products.edit', $product) }}" class="btn btn-primary"><i class="bi bi-pencil me-1"></i>Edit</a>
        <form method="POST" action="{{ route('products.destroy', $product) }}" onsubmit="return confirm('Delete this product?')">
            @csrf @method('DELETE')
            <button class="btn btn-outline-danger"><i class="bi bi-trash me-1"></i>Delete</button>
        </form>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header fw-600"><i class="bi bi-box-seam-fill me-2 text-info"></i>Product Information</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="text-muted small">SKU</div>
                        <div class="fw-600">{{ $product->sku ?? '—' }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">Category</div>
                        <div class="fw-600">{{ $product->category ?? '—' }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">Unit</div>
                        <div class="fw-600">{{ ucfirst($product->unit) }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">Status</div>
                        <span class="badge bg-{{ $product->is_active ? 'success' : 'secondary' }}">{{ $product->is_active ? 'Active' : 'Inactive' }}</span>
                    </div>
                    @if($product->description)
                    <div class="col-12">
                        <div class="text-muted small">Description</div>
                        <div>{{ $product->description }}</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header fw-600">Pricing</div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="text-muted small">Unit Price</div>
                    <div class="fw-700 fs-4 text-primary">${{ number_format($product->unit_price, 2) }}</div>
                    <div class="text-muted small">per {{ $product->unit }}</div>
                </div>
                <hr>
                <div class="mb-2 d-flex justify-content-between">
                    <span class="text-muted small">Cost Price</span>
                    <span class="fw-600">${{ number_format($product->cost_price, 2) }}</span>
                </div>
                <div class="mb-2 d-flex justify-content-between">
                    <span class="text-muted small">Tax Rate</span>
                    <span class="fw-600">{{ $product->tax_rate }}%</span>
                </div>
                @if($product->cost_price > 0)
                <div class="d-flex justify-content-between">
                    <span class="text-muted small">Gross Margin</span>
                    <span class="fw-600 text-{{ $product->margin >= 50 ? 'success' : ($product->margin >= 25 ? 'warning' : 'danger') }}">{{ $product->margin }}%</span>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
