<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    private function tid() { return Auth::user()->tenant_id; }

    public function index(Request $request)
    {
        $query = Task::where('tenant_id', $this->tid())->with(['assignedTo', 'creator']);
        if ($request->search) $query->where('title', 'like', '%'.$request->search.'%');
        if ($request->status) $query->where('status', $request->status);
        if ($request->priority) $query->where('priority', $request->priority);
        if ($request->type) $query->where('type', $request->type);
        $tasks = $query->orderBy('due_date')->paginate(15)->withQueryString();
        $overdueTasks = Task::where('tenant_id', $this->tid())
            ->where('due_date', '<', now())->whereNotIn('status', ['completed','cancelled'])->count();
        return view('tasks.index', compact('tasks', 'overdueTasks'));
    }

    public function create()
    {
        $users = User::where('tenant_id', $this->tid())->get();
        return view('tasks.create', compact('users'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:200',
            'description' => 'nullable|string',
            'type' => 'required|in:task,call,email,meeting',
            'status' => 'required|in:pending,in_progress,completed,cancelled',
            'priority' => 'required|in:low,medium,high,urgent',
            'due_date' => 'nullable|date',
            'assigned_to' => 'nullable|exists:users,id',
        ]);
        $data['tenant_id'] = $this->tid();
        $data['created_by'] = Auth::id();
        Task::create($data);
        return redirect()->route('tasks.index')->with('success', 'Task created!');
    }

    public function show(Task $task)
    {
        abort_if($task->tenant_id !== $this->tid(), 403);
        return view('tasks.show', compact('task'));
    }

    public function edit(Task $task)
    {
        abort_if($task->tenant_id !== $this->tid(), 403);
        $users = User::where('tenant_id', $this->tid())->get();
        return view('tasks.edit', compact('task', 'users'));
    }

    public function update(Request $request, Task $task)
    {
        abort_if($task->tenant_id !== $this->tid(), 403);
        $data = $request->validate([
            'title' => 'required|string|max:200',
            'description' => 'nullable|string',
            'type' => 'required|in:task,call,email,meeting',
            'status' => 'required|in:pending,in_progress,completed,cancelled',
            'priority' => 'required|in:low,medium,high,urgent',
            'due_date' => 'nullable|date',
            'assigned_to' => 'nullable|exists:users,id',
        ]);
        if ($data['status'] === 'completed' && $task->status !== 'completed') {
            $data['completed_at'] = now();
        }
        $task->update($data);
        return redirect()->route('tasks.index')->with('success', 'Task updated!');
    }

    public function destroy(Task $task)
    {
        abort_if($task->tenant_id !== $this->tid(), 403);
        $task->delete();
        return redirect()->route('tasks.index')->with('success', 'Task deleted.');
    }
}
