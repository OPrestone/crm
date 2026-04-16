<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Lead;
use App\Models\WebForm;
use App\Models\WebFormSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WebFormController extends Controller
{
    private function tid() { return Auth::user()->tenant_id; }

    public function index(Request $request)
    {
        $query = WebForm::where('tenant_id', $this->tid())->withCount('submissions');
        if ($s = $request->search) {
            $query->where('name', 'like', "%$s%");
        }
        $forms = $query->latest()->paginate(15)->withQueryString();
        return view('web-forms.index', compact('forms'));
    }

    public function create()
    {
        $defaultFields = [
            ['label' => 'First Name', 'name' => 'first_name', 'type' => 'text',  'required' => true],
            ['label' => 'Last Name',  'name' => 'last_name',  'type' => 'text',  'required' => true],
            ['label' => 'Email',      'name' => 'email',      'type' => 'email', 'required' => true],
            ['label' => 'Phone',      'name' => 'phone',      'type' => 'text',  'required' => false],
            ['label' => 'Company',    'name' => 'company',    'type' => 'text',  'required' => false],
            ['label' => 'Message',    'name' => 'message',    'type' => 'textarea','required' => false],
        ];
        return view('web-forms.create', compact('defaultFields'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'            => 'required|string|max:200',
            'description'     => 'nullable|string|max:500',
            'submit_action'   => 'required|in:contact,lead,both',
            'success_message' => 'nullable|string|max:500',
            'redirect_url'    => 'nullable|url|max:500',
            'fields'          => 'nullable|array',
        ]);

        $data['tenant_id']  = $this->tid();
        $data['created_by'] = Auth::id();
        $data['is_active']  = true;

        $form = WebForm::create($data);

        return redirect()->route('web_forms.show', $form)
            ->with('success', 'Form created successfully!');
    }

    public function show(WebForm $webForm)
    {
        abort_if($webForm->tenant_id !== $this->tid(), 403);
        $submissions = $webForm->submissions()->latest()->paginate(20);
        return view('web-forms.show', compact('webForm','submissions'));
    }

    public function edit(WebForm $webForm)
    {
        abort_if($webForm->tenant_id !== $this->tid(), 403);
        return view('web-forms.edit', compact('webForm'));
    }

    public function update(Request $request, WebForm $webForm)
    {
        abort_if($webForm->tenant_id !== $this->tid(), 403);

        $data = $request->validate([
            'name'            => 'required|string|max:200',
            'description'     => 'nullable|string|max:500',
            'submit_action'   => 'required|in:contact,lead,both',
            'success_message' => 'nullable|string|max:500',
            'redirect_url'    => 'nullable|url|max:500',
            'fields'          => 'nullable|array',
            'is_active'       => 'boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active');
        $webForm->update($data);

        return redirect()->route('web_forms.show', $webForm)
            ->with('success', 'Form updated!');
    }

    public function destroy(WebForm $webForm)
    {
        abort_if($webForm->tenant_id !== $this->tid(), 403);
        $webForm->submissions()->delete();
        $webForm->delete();
        return redirect()->route('web_forms.index')->with('success', 'Form deleted.');
    }

    // Public form embed
    public function publicShow(WebForm $webForm)
    {
        abort_if(!$webForm->is_active, 404);
        return view('web-forms.public', compact('webForm'));
    }

    public function publicSubmit(Request $request, WebForm $webForm)
    {
        abort_if(!$webForm->is_active, 404);

        // Validate required fields
        $rules = [];
        foreach ($webForm->fields ?? [] as $field) {
            if ($field['required'] ?? false) {
                $rules[$field['name']] = 'required|string|max:500';
            } else {
                $rules[$field['name']] = 'nullable|string|max:500';
            }
        }
        $formData = $request->validate($rules);

        // Save submission
        $submission = WebFormSubmission::create([
            'form_id'    => $webForm->id,
            'tenant_id'  => $webForm->tenant_id,
            'data'       => $formData,
            'ip_address' => $request->ip(),
        ]);

        // Create contact/lead based on action
        $action = $webForm->submit_action;
        $email = $formData['email'] ?? null;

        if (($action === 'contact' || $action === 'both') && $email) {
            $contact = Contact::firstOrCreate(
                ['tenant_id' => $webForm->tenant_id, 'email' => $email],
                [
                    'first_name' => $formData['first_name'] ?? 'Unknown',
                    'last_name'  => $formData['last_name'] ?? '',
                    'phone'      => $formData['phone'] ?? null,
                    'status'     => 'active',
                ]
            );

            if ($action === 'both') {
                Lead::create([
                    'tenant_id'  => $webForm->tenant_id,
                    'title'      => 'Form: ' . $webForm->name . ' — ' . ($formData['first_name'] ?? $email),
                    'contact_id' => $contact->id,
                    'source'     => 'Website',
                    'status'     => 'new',
                    'notes'      => $formData['message'] ?? null,
                ]);
            }
        }

        $submission->update(['processed' => true]);

        if ($webForm->redirect_url) {
            return redirect($webForm->redirect_url);
        }

        $successMessage = $webForm->success_message ?: 'Thank you! Your submission has been received.';
        return view('web-forms.success', compact('webForm', 'successMessage'));
    }

    public function viewSubmission(WebForm $webForm, WebFormSubmission $submission)
    {
        abort_if($webForm->tenant_id !== $this->tid(), 403);
        abort_if($submission->form_id !== $webForm->id, 404);
        return view('web-forms.submission', compact('webForm','submission'));
    }
}
