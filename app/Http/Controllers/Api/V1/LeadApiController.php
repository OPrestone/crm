<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Lead;
use Illuminate\Http\Request;

class LeadApiController extends BaseApiController
{
    public function index(Request $request)
    {
        $tid   = $this->apiTenantId($request);
        $query = Lead::where('tenant_id', $tid)->with('stage');

        if ($request->filled('q'))      $query->where('title', 'like', '%' . $request->q . '%');
        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('source')) $query->where('source', $request->source);

        $query->orderBy($request->get('sort', 'created_at'), $request->get('dir', 'desc'));
        return $this->paginate($query, $request, fn($l) => [
            'id'         => $l->id,
            'title'      => $l->title,
            'first_name' => $l->first_name,
            'last_name'  => $l->last_name,
            'email'      => $l->email,
            'phone'      => $l->phone,
            'source'     => $l->source,
            'status'     => $l->status,
            'value'      => $l->value,
            'stage'      => $l->stage ? ['id' => $l->stage->id, 'name' => $l->stage->name] : null,
            'created_at' => $l->created_at?->toIso8601String(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'      => 'required|string|max:200',
            'first_name' => 'nullable|string|max:100',
            'last_name'  => 'nullable|string|max:100',
            'email'      => 'nullable|email',
            'phone'      => 'nullable|string|max:30',
            'source'     => 'nullable|string|max:100',
            'value'      => 'nullable|numeric',
            'notes'      => 'nullable|string',
        ]);
        $validated['tenant_id'] = $this->apiTenantId($request);
        return $this->success(Lead::create($validated)->toArray(), 201);
    }

    public function update(Request $request, int $id)
    {
        $lead = Lead::where('tenant_id', $this->apiTenantId($request))->find($id);
        if (!$lead) return $this->notFound('Lead');
        $lead->update($request->only(['title','first_name','last_name','email','phone','source','value','status','stage_id','notes']));
        return $this->success($lead->fresh()->toArray());
    }

    public function destroy(Request $request, int $id)
    {
        $lead = Lead::where('tenant_id', $this->apiTenantId($request))->find($id);
        if (!$lead) return $this->notFound('Lead');
        $lead->delete();
        return response()->json(null, 204);
    }
}
