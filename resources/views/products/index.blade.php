@extends('layouts.app')
@section('title', 'Products')
@section('page-title', 'Products Catalog')

@section('content')
<div class="page-header">
    <div>
        <h1>Products &amp; Services</h1>
        <nav aria-label="breadcrumb"><ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Products</li>
        </ol></nav>
    </div>
    <a href="{{ route('products.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Add Product</a>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-2">
            <div class="col-md-5"><div class="search-box"><i class="bi bi-search search-icon"></i>
                <input type="text" name="search" class="form-control" placeholder="Search by name or SKU..." value="{{ request('search') }}">
            </div></div>
            <div class="col-md-2">
                <select name="category" class="form-select">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)<option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>{{ $cat }}</option>@endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-outline-primary me-2"><i class="bi bi-filter"></i> Filter</button>
                <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">Clear</a>
            </div>
        </form>
    </div>
</div>

<div class="card table-card">
    <div class="card-body p-0">
        @if($products->count())
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Product</th>
                        <th>SKU</th>
                        <th>Category</th>
                        <th>Unit Price</th>
                        <th>Margin</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($products as $product)
                    <tr>
                        <td>
                            <a href="{{ route('products.show', $product) }}" class="fw-600 text-decoration-none">{{ $product->name }}</a>
                            @if($product->description)<div class="text-muted small">{{ Str::limit($product->description, 60) }}</div>@endif
                        </td>
                        <td><code>{{ $product->sku ?? '—' }}</code></td>
                        <td>{{ $product->category ?? '—' }}</td>
                        <td class="fw-600">${{ number_format($product->unit_price, 2) }}</td>
                        <td>
                            @if($product->cost_price > 0)
                            <span class="badge bg-{{ $product->margin >= 50 ? 'success' : ($product->margin >= 25 ? 'warning' : 'danger') }}-subtle text-{{ $product->margin >= 50 ? 'success' : ($product->margin >= 25 ? 'warning' : 'danger') }}">{{ $product->margin }}%</span>
                            @else<span class="text-muted">—</span>@endif
                        </td>
                        <td>
                            <span class="badge bg-{{ $product->is_active ? 'success' : 'secondary' }}-subtle text-{{ $product->is_active ? 'success' : 'secondary' }}">
                                {{ $product->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="text-end">
                            <a href="{{ route('products.edit', $product) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i></a>
                            <form method="POST" action="{{ route('products.destroy', $product) }}" class="d-inline" onsubmit="return confirm('Delete this product?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3">{{ $products->links() }}</div>
        @else
        <div class="text-center py-5">
            <i class="bi bi-box-seam text-muted" style="font-size:3rem;"></i>
            <h5 class="mt-3 text-muted">No products yet</h5>
            <p class="text-muted">Add your first product or service to use in quotes and invoices.</p>
            <a href="{{ route('products.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Add Product</a>
        </div>
        @endif
    </div>
</div>
@endsection
