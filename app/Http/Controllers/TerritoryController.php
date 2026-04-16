<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Territory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TerritoryController extends Controller
{
    private function tid() { return Auth::user()->tenant_id; }

    public function index(Request $request)
    {
        $query = Territory::where('tenant_id', $this->tid())
            ->with(['users','creator']);
        if ($s = $request->search) {
            $query->where(fn($q) => $q->where('name','like',"%$s%")->orWhere('description','like',"%$s%"));
        }
        if ($type = $request->type) $query->where('type', $type);
        $territories = $query->latest()->paginate(15)->withQueryString();
        return view('territories.index', compact('territories'));
    }

    public function create()
    {
        $users = User::where('tenant_id', $this->tid())->get();
        return view('territories.create', compact('users'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:200',
            'description' => 'nullable|string|max:500',
            'type'        => 'required|in:geographic,account,industry,custom',
            'color'       => 'nullable|string|max:7',
            'user_ids'    => 'nullable|array',
            'user_ids.*'  => 'exists:users,id',
        ]);

        $userIds = $data['user_ids'] ?? [];
        unset($data['user_ids']);

        $data['tenant_id']  = $this->tid();
        $data['created_by'] = Auth::id();
        $data['color']      = $data['color'] ?? '#6c757d';

        $territory = Territory::create($data);
        $territory->users()->sync($userIds);

        return redirect()->route('territories.show', $territory)
            ->with('success', 'Territory created!');
    }

    public function show(Territory $territory)
    {
        abort_if($territory->tenant_id !== $this->tid(), 403);
        $territory->load(['users','creator']);

        // Contacts in this territory (simplified: contacts assigned to reps in territory)
        $userIds = $territory->users->pluck('id');
        $contacts = Contact::where('tenant_id', $this->tid())
            ->whereIn('assigned_to', $userIds)
            ->with('assignedTo')
            ->paginate(15);

        return view('territories.show', compact('territory','contacts'));
    }

    public function edit(Territory $territory)
    {
        abort_if($territory->tenant_id !== $this->tid(), 403);
        $users = User::where('tenant_id', $this->tid())->get();
        return view('territories.edit', compact('territory','users'));
    }

    public function update(Request $request, Territory $territory)
    {
        abort_if($territory->tenant_id !== $this->tid(), 403);

        $data = $request->validate([
            'name'        => 'required|string|max:200',
            'description' => 'nullable|string|max:500',
            'type'        => 'required|in:geographic,account,industry,custom',
            'color'       => 'nullable|string|max:7',
            'user_ids'    => 'nullable|array',
            'user_ids.*'  => 'exists:users,id',
        ]);

        $userIds = $data['user_ids'] ?? [];
        unset($data['user_ids']);

        $territory->update($data);
        $territory->users()->sync($userIds);

        return redirect()->route('territories.show', $territory)
            ->with('success', 'Territory updated!');
    }

    public function destroy(Territory $territory)
    {
        abort_if($territory->tenant_id !== $this->tid(), 403);
        $territory->users()->detach();
        $territory->delete();
        return redirect()->route('territories.index')->with('success', 'Territory deleted.');
    }
}
