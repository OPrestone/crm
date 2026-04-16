<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Contact;
use Illuminate\Http\Request;

class ContactApiController extends BaseApiController
{
    public function index(Request $request)
    {
        $tid   = $this->apiTenantId($request);
        $query = Contact::where('tenant_id', $tid)->with('company');

        if ($request->filled('q')) {
            $q = '%' . $request->q . '%';
            $query->where(fn($b) => $b->where('first_name', 'like', $q)
                ->orWhere('last_name', 'like', $q)
                ->orWhere('email', 'like', $q)
                ->orWhere('phone', 'like', $q));
        }
        if ($request->filled('company_id')) $query->where('company_id', $request->company_id);
        if ($request->filled('status'))     $query->where('status', $request->status);

        $query->orderBy($request->get('sort', 'created_at'), $request->get('dir', 'desc'));

        return $this->paginate($query, $request, fn($c) => $this->transform($c));
    }

    public function show(Request $request, int $id)
    {
        $contact = Contact::where('tenant_id', $this->apiTenantId($request))->with('company', 'deals', 'tasks')->find($id);
        if (!$contact) return $this->notFound('Contact');
        return $this->success($this->transform($contact, true));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name'  => 'nullable|string|max:100',
            'email'      => 'nullable|email|max:200',
            'phone'      => 'nullable|string|max:30',
            'company_id' => 'nullable|integer',
            'job_title'  => 'nullable|string|max:150',
            'status'     => 'nullable|in:lead,prospect,customer,churned',
            'tags'       => 'nullable|string',
            'notes'      => 'nullable|string',
        ]);
        $validated['tenant_id'] = $this->apiTenantId($request);

        $contact = Contact::create($validated);
        return $this->success($this->transform($contact), 201);
    }

    public function update(Request $request, int $id)
    {
        $contact = Contact::where('tenant_id', $this->apiTenantId($request))->find($id);
        if (!$contact) return $this->notFound('Contact');

        $validated = $request->validate([
            'first_name' => 'sometimes|string|max:100',
            'last_name'  => 'sometimes|nullable|string|max:100',
            'email'      => 'sometimes|nullable|email|max:200',
            'phone'      => 'sometimes|nullable|string|max:30',
            'company_id' => 'sometimes|nullable|integer',
            'job_title'  => 'sometimes|nullable|string|max:150',
            'status'     => 'sometimes|nullable|in:lead,prospect,customer,churned',
            'tags'       => 'sometimes|nullable|string',
            'notes'      => 'sometimes|nullable|string',
        ]);
        $contact->update($validated);
        return $this->success($this->transform($contact->fresh()));
    }

    public function destroy(Request $request, int $id)
    {
        $contact = Contact::where('tenant_id', $this->apiTenantId($request))->find($id);
        if (!$contact) return $this->notFound('Contact');
        $contact->delete();
        return response()->json(null, 204);
    }

    private function transform(Contact $c, bool $full = false): array
    {
        $base = [
            'id'         => $c->id,
            'first_name' => $c->first_name,
            'last_name'  => $c->last_name,
            'full_name'  => trim($c->first_name . ' ' . $c->last_name),
            'email'      => $c->email,
            'phone'      => $c->phone,
            'job_title'  => $c->job_title,
            'status'     => $c->status,
            'tags'       => $c->tags,
            'company'    => $c->relationLoaded('company') && $c->company ? [
                'id' => $c->company->id, 'name' => $c->company->name,
            ] : null,
            'created_at' => $c->created_at?->toIso8601String(),
            'updated_at' => $c->updated_at?->toIso8601String(),
        ];
        if ($full) {
            $base['notes']  = $c->notes;
            $base['address']= $c->address;
            $base['deals']  = $c->relationLoaded('deals')  ? $c->deals->map(fn($d)  => ['id' => $d->id, 'title' => $d->title, 'value' => $d->value])->toArray() : [];
            $base['tasks']  = $c->relationLoaded('tasks')  ? $c->tasks->map(fn($t)  => ['id' => $t->id, 'title' => $t->title, 'due_date' => $t->due_date?->toDateString()])->toArray() : [];
        }
        return $base;
    }
}
