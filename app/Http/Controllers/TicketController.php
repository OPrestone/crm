<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Company;
use App\Models\Ticket;
use App\Models\TicketReply;
use App\Models\User;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    private function tid(): int { return auth()->user()->tenant_id; }

    public function index(Request $request)
    {
        $query = Ticket::where('tenant_id', $this->tid())->with(['contact', 'assignedTo']);
        if ($request->search)   $query->where('subject', 'like', "%{$request->search}%")
            ->orWhere('ticket_number', 'like', "%{$request->search}%");
        if ($request->status)   $query->where('status', $request->status);
        if ($request->priority) $query->where('priority', $request->priority);
        $tickets = $query->latest()->paginate(20)->withQueryString();
        $stats   = [
            'open'        => Ticket::where('tenant_id', $this->tid())->where('status', 'open')->count(),
            'in_progress' => Ticket::where('tenant_id', $this->tid())->where('status', 'in_progress')->count(),
            'resolved'    => Ticket::where('tenant_id', $this->tid())->where('status', 'resolved')->count(),
            'urgent'      => Ticket::where('tenant_id', $this->tid())->where('priority', 'urgent')->whereNotIn('status',['resolved','closed'])->count(),
        ];
        return view('tickets.index', compact('tickets', 'stats'));
    }

    public function create()
    {
        $contacts  = Contact::where('tenant_id', $this->tid())->orderBy('first_name')->get();
        $companies = Company::where('tenant_id', $this->tid())->orderBy('name')->get();
        $agents    = User::where('tenant_id', $this->tid())->orderBy('name')->get();
        $nextNum   = 'TKT-' . str_pad(Ticket::where('tenant_id', $this->tid())->withTrashed()->count() + 1, 5, '0', STR_PAD_LEFT);
        return view('tickets.create', compact('contacts', 'companies', 'agents', 'nextNum'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'ticket_number' => 'required|string|unique:tickets,ticket_number',
            'contact_id'    => 'nullable|exists:contacts,id',
            'company_id'    => 'nullable|exists:companies,id',
            'assigned_to'   => 'nullable|exists:users,id',
            'subject'       => 'required|string|max:255',
            'description'   => 'required|string',
            'status'        => 'required|in:open,pending,in_progress,resolved,closed',
            'priority'      => 'required|in:low,medium,high,urgent',
            'category'      => 'nullable|string|max:100',
            'channel'       => 'required|in:email,phone,chat,web,other',
        ]);
        $data['tenant_id']  = $this->tid();
        $data['created_by'] = auth()->id();
        $ticket = Ticket::create($data);
        return redirect()->route('tickets.show', $ticket)->with('success', "Ticket {$ticket->ticket_number} created.");
    }

    public function show(Ticket $ticket)
    {
        abort_if($ticket->tenant_id !== $this->tid(), 403);
        $ticket->load(['contact', 'company', 'assignedTo', 'creator', 'replies.user']);
        return view('tickets.show', compact('ticket'));
    }

    public function edit(Ticket $ticket)
    {
        abort_if($ticket->tenant_id !== $this->tid(), 403);
        $contacts  = Contact::where('tenant_id', $this->tid())->orderBy('first_name')->get();
        $companies = Company::where('tenant_id', $this->tid())->orderBy('name')->get();
        $agents    = User::where('tenant_id', $this->tid())->orderBy('name')->get();
        return view('tickets.edit', compact('ticket', 'contacts', 'companies', 'agents'));
    }

    public function update(Request $request, Ticket $ticket)
    {
        abort_if($ticket->tenant_id !== $this->tid(), 403);
        $data = $request->validate([
            'contact_id'  => 'nullable|exists:contacts,id',
            'company_id'  => 'nullable|exists:companies,id',
            'assigned_to' => 'nullable|exists:users,id',
            'subject'     => 'required|string|max:255',
            'description' => 'required|string',
            'status'      => 'required|in:open,pending,in_progress,resolved,closed',
            'priority'    => 'required|in:low,medium,high,urgent',
            'category'    => 'nullable|string|max:100',
            'channel'     => 'required|in:email,phone,chat,web,other',
        ]);
        if (in_array($data['status'], ['resolved','closed']) && !$ticket->resolved_at) {
            $data['resolved_at'] = now();
        }
        $ticket->update($data);
        return redirect()->route('tickets.show', $ticket)->with('success', 'Ticket updated.');
    }

    public function destroy(Ticket $ticket)
    {
        abort_if($ticket->tenant_id !== $this->tid(), 403);
        $ticket->delete();
        return redirect()->route('tickets.index')->with('success', 'Ticket deleted.');
    }

    public function reply(Request $request, Ticket $ticket)
    {
        abort_if($ticket->tenant_id !== $this->tid(), 403);
        $data = $request->validate([
            'body'        => 'required|string',
            'is_internal' => 'boolean',
        ]);

        if (!$ticket->first_response_at) {
            $ticket->update(['first_response_at' => now()]);
        }

        TicketReply::create([
            'ticket_id'   => $ticket->id,
            'user_id'     => auth()->id(),
            'body'        => $data['body'],
            'is_internal' => $request->boolean('is_internal'),
        ]);

        if ($request->status && $request->status !== $ticket->status) {
            $update = ['status' => $request->status];
            if (in_array($request->status, ['resolved','closed']) && !$ticket->resolved_at) {
                $update['resolved_at'] = now();
            }
            $ticket->update($update);
        }

        return back()->with('success', 'Reply added.');
    }
}
