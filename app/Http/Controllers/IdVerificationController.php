<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\IdVerification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class IdVerificationController extends Controller
{
    private function tenantId(): int
    {
        return Auth::user()->tenant_id;
    }

    public function index(Request $request)
    {
        $query = IdVerification::where('tenant_id', $this->tenantId())
            ->with(['contact', 'creator']);

        if ($request->status) {
            $query->where('status', $request->status);
        }
        if ($request->risk_level) {
            $query->where('risk_level', $request->risk_level);
        }
        if ($request->search) {
            $q = $request->search;
            $query->where(function ($s) use ($q) {
                $s->where('full_name', 'like', "%$q%")
                  ->orWhere('id_number', 'like', "%$q%");
            });
        }

        $verifications = $query->latest()->paginate(20)->withQueryString();

        $stats = [
            'total'        => IdVerification::where('tenant_id', $this->tenantId())->count(),
            'pending'      => IdVerification::where('tenant_id', $this->tenantId())->where('status', 'pending')->count(),
            'verified'     => IdVerification::where('tenant_id', $this->tenantId())->where('status', 'verified')->count(),
            'rejected'     => IdVerification::where('tenant_id', $this->tenantId())->where('status', 'rejected')->count(),
            'high_risk'    => IdVerification::where('tenant_id', $this->tenantId())->where('risk_level', 'high')->count(),
        ];

        return view('id-verification.index', compact('verifications', 'stats'));
    }

    public function create()
    {
        $contacts = Contact::where('tenant_id', $this->tenantId())
            ->orderBy('first_name')->get();
        return view('id-verification.create', compact('contacts'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'contact_id'      => 'nullable|exists:contacts,id',
            'full_name'       => 'required|string|max:255',
            'id_type'         => 'required|in:passport,national_id,driver_license,residence_permit',
            'id_number'       => 'nullable|string|max:100',
            'date_of_birth'   => 'nullable|date',
            'issue_date'      => 'nullable|date',
            'expiry_date'     => 'nullable|date',
            'issuing_country' => 'nullable|string|max:100',
            'nationality'     => 'nullable|string|max:100',
            'gender'          => 'nullable|in:male,female,other',
            'address'         => 'nullable|string',
            'document_front'  => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'document_back'   => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'selfie'          => 'nullable|file|mimes:jpg,jpeg,png|max:5120',
            'notes'           => 'nullable|string',
        ]);

        $data['tenant_id']  = $this->tenantId();
        $data['created_by'] = Auth::id();
        $data['status']     = 'pending';

        foreach (['document_front', 'document_back', 'selfie'] as $field) {
            if ($request->hasFile($field)) {
                $data[$field] = $request->file($field)->store("id-docs/{$this->tenantId()}", 'public');
            }
        }

        $data['confidence_score'] = $this->computeConfidence($data);
        $data['risk_level']       = $this->computeRisk($data);

        IdVerification::create($data);

        return redirect()->route('id-verification.index')
            ->with('success', 'ID verification record created successfully.');
    }

    public function show(IdVerification $idVerification)
    {
        $this->authorizeRecord($idVerification);
        $idVerification->load(['contact', 'creator', 'reviewer']);
        return view('id-verification.show', compact('idVerification'));
    }

    public function edit(IdVerification $idVerification)
    {
        $this->authorizeRecord($idVerification);
        $contacts = Contact::where('tenant_id', $this->tenantId())->orderBy('first_name')->get();
        return view('id-verification.edit', compact('idVerification', 'contacts'));
    }

    public function update(Request $request, IdVerification $idVerification)
    {
        $this->authorizeRecord($idVerification);

        $data = $request->validate([
            'contact_id'      => 'nullable|exists:contacts,id',
            'full_name'       => 'required|string|max:255',
            'id_type'         => 'required|in:passport,national_id,driver_license,residence_permit',
            'id_number'       => 'nullable|string|max:100',
            'date_of_birth'   => 'nullable|date',
            'issue_date'      => 'nullable|date',
            'expiry_date'     => 'nullable|date',
            'issuing_country' => 'nullable|string|max:100',
            'nationality'     => 'nullable|string|max:100',
            'gender'          => 'nullable|in:male,female,other',
            'address'         => 'nullable|string',
            'document_front'  => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'document_back'   => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'selfie'          => 'nullable|file|mimes:jpg,jpeg,png|max:5120',
            'notes'           => 'nullable|string',
        ]);

        foreach (['document_front', 'document_back', 'selfie'] as $field) {
            if ($request->hasFile($field)) {
                if ($idVerification->$field) Storage::disk('public')->delete($idVerification->$field);
                $data[$field] = $request->file($field)->store("id-docs/{$this->tenantId()}", 'public');
            }
        }

        $data['confidence_score'] = $this->computeConfidence(array_merge($idVerification->toArray(), $data));
        $data['risk_level']       = $this->computeRisk(array_merge($idVerification->toArray(), $data));

        $idVerification->update($data);

        return redirect()->route('id-verification.show', $idVerification)
            ->with('success', 'Record updated successfully.');
    }

    public function updateStatus(Request $request, IdVerification $idVerification)
    {
        $this->authorizeRecord($idVerification);
        $data = $request->validate([
            'status'           => 'required|in:pending,under_review,verified,rejected,expired',
            'rejection_reason' => 'nullable|string',
            'risk_level'       => 'nullable|in:low,medium,high',
        ]);

        $data['reviewed_by'] = Auth::id();
        if ($data['status'] === 'verified') {
            $data['verified_at'] = now();
        }
        if ($request->risk_level) {
            $data['risk_level'] = $request->risk_level;
        }

        $idVerification->update($data);

        return back()->with('success', 'Status updated to ' . ucfirst($data['status']));
    }

    public function destroy(IdVerification $idVerification)
    {
        $this->authorizeRecord($idVerification);
        $idVerification->delete();
        return redirect()->route('id-verification.index')->with('success', 'Record deleted.');
    }

    private function authorizeRecord(IdVerification $record): void
    {
        abort_if($record->tenant_id !== $this->tenantId(), 403);
    }

    private function computeConfidence(array $data): int
    {
        $score = 0;
        if (!empty($data['id_number']))       $score += 20;
        if (!empty($data['date_of_birth']))   $score += 15;
        if (!empty($data['expiry_date']))     $score += 10;
        if (!empty($data['issuing_country'])) $score += 10;
        if (!empty($data['document_front']))  $score += 25;
        if (!empty($data['document_back']))   $score += 10;
        if (!empty($data['selfie']))          $score += 10;
        return min($score, 100);
    }

    private function computeRisk(array $data): string
    {
        $flags = 0;
        if (!empty($data['expiry_date']) && now()->isAfter($data['expiry_date'])) $flags++;
        if (empty($data['document_front'])) $flags++;
        if (empty($data['selfie'])) $flags++;
        if (($data['confidence_score'] ?? 0) < 40) $flags++;
        return match(true) {
            $flags >= 3 => 'high',
            $flags >= 1 => 'medium',
            default     => 'low',
        };
    }
}
