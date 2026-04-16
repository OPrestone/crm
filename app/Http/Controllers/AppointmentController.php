<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Contact;
use App\Models\Company;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    private function tid(): int { return auth()->user()->tenant_id; }

    public function index(Request $request)
    {
        $month = (int) ($request->month ?? now()->month);
        $year  = (int) ($request->year  ?? now()->year);
        $start = \Carbon\Carbon::create($year, $month, 1)->startOfMonth();
        $end   = $start->copy()->endOfMonth();

        $appointments = Appointment::where('tenant_id', $this->tid())
            ->whereBetween('start_at', [$start, $end])
            ->with(['contact', 'user'])
            ->orderBy('start_at')
            ->get();

        $upcoming = Appointment::where('tenant_id', $this->tid())
            ->where('start_at', '>=', now())
            ->where('status', 'scheduled')
            ->with('contact')
            ->orderBy('start_at')
            ->take(10)
            ->get();

        $stats = [
            'today'    => Appointment::where('tenant_id', $this->tid())->whereDate('start_at', today())->count(),
            'this_week'=> Appointment::where('tenant_id', $this->tid())->whereBetween('start_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'scheduled'=> Appointment::where('tenant_id', $this->tid())->where('status','scheduled')->where('start_at','>=',now())->count(),
            'completed'=> Appointment::where('tenant_id', $this->tid())->where('status','completed')->whereMonth('start_at', $month)->count(),
        ];

        return view('appointments.index', compact('appointments', 'upcoming', 'stats', 'month', 'year', 'start'));
    }

    public function create(Request $request)
    {
        $contacts  = Contact::where('tenant_id', $this->tid())->orderBy('first_name')->get();
        $companies = Company::where('tenant_id', $this->tid())->orderBy('name')->get();
        $date      = $request->date ?? now()->format('Y-m-d');
        return view('appointments.create', compact('contacts', 'companies', 'date'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'contact_id'  => 'nullable|exists:contacts,id',
            'company_id'  => 'nullable|exists:companies,id',
            'start_at'    => 'required|date',
            'end_at'      => 'required|date|after:start_at',
            'location'    => 'nullable|string|max:255',
            'type'        => 'required|in:call,meeting,demo,follow_up,other',
            'status'      => 'required|in:scheduled,completed,cancelled,no_show',
            'color'       => 'nullable|string|max:20',
        ]);
        $data['tenant_id']  = $this->tid();
        $data['user_id']    = auth()->id();
        $data['created_by'] = auth()->id();
        Appointment::create($data);
        return redirect()->route('appointments.index')->with('success', "Appointment '{$data['title']}' scheduled.");
    }

    public function show(Appointment $appointment)
    {
        abort_if($appointment->tenant_id !== $this->tid(), 403);
        $appointment->load(['contact', 'company', 'user']);
        return view('appointments.show', compact('appointment'));
    }

    public function edit(Appointment $appointment)
    {
        abort_if($appointment->tenant_id !== $this->tid(), 403);
        $contacts  = Contact::where('tenant_id', $this->tid())->orderBy('first_name')->get();
        $companies = Company::where('tenant_id', $this->tid())->orderBy('name')->get();
        return view('appointments.edit', compact('appointment', 'contacts', 'companies'));
    }

    public function update(Request $request, Appointment $appointment)
    {
        abort_if($appointment->tenant_id !== $this->tid(), 403);
        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'contact_id'  => 'nullable|exists:contacts,id',
            'company_id'  => 'nullable|exists:companies,id',
            'start_at'    => 'required|date',
            'end_at'      => 'required|date|after:start_at',
            'location'    => 'nullable|string|max:255',
            'type'        => 'required|in:call,meeting,demo,follow_up,other',
            'status'      => 'required|in:scheduled,completed,cancelled,no_show',
            'color'       => 'nullable|string|max:20',
        ]);
        $appointment->update($data);
        return redirect()->route('appointments.show', $appointment)->with('success', 'Appointment updated.');
    }

    public function destroy(Appointment $appointment)
    {
        abort_if($appointment->tenant_id !== $this->tid(), 403);
        $appointment->delete();
        return redirect()->route('appointments.index')->with('success', 'Appointment deleted.');
    }
}
