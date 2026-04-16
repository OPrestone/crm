<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Deal;
use Illuminate\Http\Request;

class DealApiController extends BaseApiController
{
    public function index(Request $request)
    {
        $tid   = $this->apiTenantId($request);
        $query = Deal::where('tenant_id', $tid)->with('stage', 'contact');

        if ($request->filled('q'))        $query->where('title', 'like', '%' . $request->q . '%');
        if ($request->filled('stage_id')) $query->where('stage_id', $request->stage_id);
        if ($request->filled('status'))   $query->where('status', $request->status);
        if ($request->filled('contact_id')) $query->where('contact_id', $request->contact_id);

        $query->orderBy($request->get('sort', 'created_at'), $request->get('dir', 'desc'));
        return $this->paginate($query, $request, fn($d) => $this->transform($d));
    }

    public function show(Request $request, int $id)
    {
        $deal = Deal::where('tenant_id', $this->apiTenantId($request))->with('stage', 'contact', 'tasks')->find($id);
        if (!$deal) return $this->notFound('Deal');
        return $this->success($this->transform($deal, true));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'      => 'required|string|max:200',
            'value'      => 'nullable|numeric|min:0',
            'stage_id'   => 'nullable|integer',
            'contact_id' => 'nullable|integer',
            'close_date' => 'nullable|date',
            'probability'=> 'nullable|integer|min:0|max:100',
            'status'     => 'nullable|in:open,won,lost',
            'notes'      => 'nullable|string',
        ]);
        $validated['tenant_id'] = $this->apiTenantId($request);
        $deal = Deal::create($validated);
        return $this->success($this->transform($deal->fresh(['stage', 'contact'])), 201);
    }

    public function update(Request $request, int $id)
    {
        $deal = Deal::where('tenant_id', $this->apiTenantId($request))->find($id);
        if (!$deal) return $this->notFound('Deal');

        $validated = $request->validate([
            'title'      => 'sometimes|string|max:200',
            'value'      => 'sometimes|nullable|numeric|min:0',
            'stage_id'   => 'sometimes|nullable|integer',
            'contact_id' => 'sometimes|nullable|integer',
            'close_date' => 'sometimes|nullable|date',
            'probability'=> 'sometimes|nullable|integer|min:0|max:100',
            'status'     => 'sometimes|nullable|in:open,won,lost',
        ]);
        $deal->update($validated);
        return $this->success($this->transform($deal->fresh(['stage', 'contact'])));
    }

    public function destroy(Request $request, int $id)
    {
        $deal = Deal::where('tenant_id', $this->apiTenantId($request))->find($id);
        if (!$deal) return $this->notFound('Deal');
        $deal->delete();
        return response()->json(null, 204);
    }

    private function transform(Deal $d, bool $full = false): array
    {
        return [
            'id'          => $d->id,
            'title'       => $d->title,
            'value'       => $d->value,
            'status'      => $d->status,
            'probability' => $d->probability,
            'close_date'  => $d->close_date?->toDateString(),
            'stage'       => $d->relationLoaded('stage') && $d->stage ? ['id' => $d->stage->id, 'name' => $d->stage->name] : null,
            'contact'     => $d->relationLoaded('contact') && $d->contact ? ['id' => $d->contact->id, 'name' => trim($d->contact->first_name . ' ' . $d->contact->last_name)] : null,
            'notes'       => $full ? $d->notes : null,
            'created_at'  => $d->created_at?->toIso8601String(),
            'updated_at'  => $d->updated_at?->toIso8601String(),
        ];
    }
}
