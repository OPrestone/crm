<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    private function tid(): int { return auth()->user()->tenant_id; }

    public function index(Request $request)
    {
        $query = Product::where('tenant_id', $this->tid());
        if ($request->search) $query->where('name', 'like', "%{$request->search}%")
            ->orWhere('sku', 'like', "%{$request->search}%");
        if ($request->category) $query->where('category', $request->category);
        if ($request->status === 'active')   $query->where('is_active', true);
        if ($request->status === 'inactive') $query->where('is_active', false);
        $products = $query->orderBy('name')->paginate(20)->withQueryString();
        $categories = Product::where('tenant_id', $this->tid())->whereNotNull('category')
            ->distinct()->pluck('category');
        return view('products.index', compact('products', 'categories'));
    }

    public function create()
    {
        return view('products.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:200',
            'sku'         => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'category'    => 'nullable|string|max:100',
            'unit'        => 'required|string|max:50',
            'unit_price'  => 'required|numeric|min:0',
            'cost_price'  => 'nullable|numeric|min:0',
            'tax_rate'    => 'nullable|numeric|min:0|max:100',
            'is_active'   => 'boolean',
        ]);
        $data['tenant_id']  = $this->tid();
        $data['created_by'] = auth()->id();
        $data['is_active']  = $request->boolean('is_active', true);
        $data['cost_price'] = $data['cost_price'] ?? 0;
        $data['tax_rate']   = $data['tax_rate']   ?? 0;
        Product::create($data);
        return redirect()->route('products.index')->with('success', "Product '{$data['name']}' created.");
    }

    public function show(Product $product)
    {
        abort_if($product->tenant_id !== $this->tid(), 403);
        return view('products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        abort_if($product->tenant_id !== $this->tid(), 403);
        return view('products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        abort_if($product->tenant_id !== $this->tid(), 403);
        $data = $request->validate([
            'name'        => 'required|string|max:200',
            'sku'         => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'category'    => 'nullable|string|max:100',
            'unit'        => 'required|string|max:50',
            'unit_price'  => 'required|numeric|min:0',
            'cost_price'  => 'nullable|numeric|min:0',
            'tax_rate'    => 'nullable|numeric|min:0|max:100',
            'is_active'   => 'boolean',
        ]);
        $data['is_active']  = $request->boolean('is_active');
        $data['cost_price'] = $data['cost_price'] ?? 0;
        $data['tax_rate']   = $data['tax_rate']   ?? 0;
        $product->update($data);
        return redirect()->route('products.show', $product)->with('success', 'Product updated.');
    }

    public function destroy(Product $product)
    {
        abort_if($product->tenant_id !== $this->tid(), 403);
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Product deleted.');
    }
}
