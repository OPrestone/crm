<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductApiController extends BaseApiController
{
    public function index(Request $request)
    {
        $query = Product::where('tenant_id', $this->apiTenantId($request));
        if ($request->filled('q'))        $query->where('name', 'like', '%' . $request->q . '%');
        if ($request->filled('category')) $query->where('category', $request->category);
        $query->orderBy('name');
        return $this->paginate($query, $request, fn($p) => [
            'id' => $p->id, 'name' => $p->name, 'description' => $p->description,
            'price' => $p->price, 'unit' => $p->unit, 'sku' => $p->sku,
            'category' => $p->category, 'active' => (bool) $p->active,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'  => 'required|string|max:200',
            'price' => 'required|numeric|min:0',
            'unit'  => 'nullable|string|max:50',
            'sku'   => 'nullable|string|max:100',
            'category' => 'nullable|string|max:100',
            'description' => 'nullable|string',
        ]);
        $validated['tenant_id'] = $this->apiTenantId($request);
        return $this->success(Product::create($validated)->toArray(), 201);
    }

    public function update(Request $request, int $id)
    {
        $product = Product::where('tenant_id', $this->apiTenantId($request))->find($id);
        if (!$product) return $this->notFound('Product');
        $product->update($request->only(['name','price','unit','sku','category','description','active']));
        return $this->success($product->fresh()->toArray());
    }

    public function destroy(Request $request, int $id)
    {
        $product = Product::where('tenant_id', $this->apiTenantId($request))->find($id);
        if (!$product) return $this->notFound('Product');
        $product->delete();
        return response()->json(null, 204);
    }
}
