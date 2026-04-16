<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Company;
use Illuminate\Http\Request;

class CompanyApiController extends BaseApiController
{
    public function index(Request $request)
    {
        $query = Company::where('tenant_id', $this->apiTenantId($request));
        if ($request->filled('q')) $query->where('name', 'like', '%' . $request->q . '%');
        $query->orderBy($request->get('sort', 'name'), $request->get('dir', 'asc'));
        return $this->paginate($query, $request, fn($c) => [
            'id' => $c->id, 'name' => $c->name, 'email' => $c->email,
            'phone' => $c->phone, 'website' => $c->website, 'industry' => $c->industry,
            'created_at' => $c->created_at?->toIso8601String(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:200',
            'email'    => 'nullable|email',
            'phone'    => 'nullable|string|max:30',
            'website'  => 'nullable|url',
            'industry' => 'nullable|string|max:100',
            'address'  => 'nullable|string',
        ]);
        $validated['tenant_id'] = $this->apiTenantId($request);
        return $this->success(Company::create($validated)->toArray(), 201);
    }

    public function update(Request $request, int $id)
    {
        $company = Company::where('tenant_id', $this->apiTenantId($request))->find($id);
        if (!$company) return $this->notFound('Company');
        $company->update($request->only(['name','email','phone','website','industry','address']));
        return $this->success($company->fresh()->toArray());
    }

    public function destroy(Request $request, int $id)
    {
        $company = Company::where('tenant_id', $this->apiTenantId($request))->find($id);
        if (!$company) return $this->notFound('Company');
        $company->delete();
        return response()->json(null, 204);
    }
}
