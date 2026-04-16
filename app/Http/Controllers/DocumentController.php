<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Contact;
use App\Models\Deal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentController extends Controller
{
    private function tid(): int { return auth()->user()->tenant_id; }

    public function index(Request $request)
    {
        $query = Document::where('tenant_id', $this->tid())->with('uploader');
        if ($request->search)   $query->where('title', 'like', "%{$request->search}%");
        if ($request->category) $query->where('category', $request->category);
        if ($request->type) {
            $query->where('file_type', 'like', "%{$request->type}%");
        }
        $documents  = $query->latest()->paginate(20)->withQueryString();
        $categories = Document::where('tenant_id', $this->tid())->whereNotNull('category')->distinct()->pluck('category');
        $stats      = [
            'total'   => Document::where('tenant_id', $this->tid())->count(),
            'size'    => Document::where('tenant_id', $this->tid())->sum('file_size'),
            'this_month' => Document::where('tenant_id', $this->tid())->whereMonth('created_at', now()->month)->count(),
        ];
        return view('documents.index', compact('documents', 'categories', 'stats'));
    }

    public function create()
    {
        $contacts = Contact::where('tenant_id', $this->tid())->orderBy('first_name')->get();
        $deals    = Deal::where('tenant_id', $this->tid())->where('status','open')->orderBy('title')->get();
        return view('documents.create', compact('contacts', 'deals'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'            => 'required|string|max:255',
            'category'         => 'nullable|string|max:100',
            'description'      => 'nullable|string',
            'file'             => 'required|file|max:20480',
            'documentable_type'=> 'nullable|in:contact,deal',
            'documentable_id'  => 'nullable|integer',
        ]);

        $file = $request->file('file');
        $path = $file->store("documents/{$this->tid()}", 'public');

        $morphMap = [
            'contact' => \App\Models\Contact::class,
            'deal'    => \App\Models\Deal::class,
        ];
        $morphType = isset($data['documentable_type']) ? ($morphMap[$data['documentable_type']] ?? null) : null;

        Document::create([
            'tenant_id'         => $this->tid(),
            'title'             => $data['title'],
            'category'          => $data['category'] ?? null,
            'description'       => $data['description'] ?? null,
            'file_name'         => $file->getClientOriginalName(),
            'file_path'         => $path,
            'file_type'         => $file->getMimeType(),
            'file_size'         => $file->getSize(),
            'documentable_type' => $morphType,
            'documentable_id'   => $data['documentable_id'] ?? null,
            'uploaded_by'       => auth()->id(),
        ]);

        return redirect()->route('documents.index')->with('success', 'Document uploaded successfully.');
    }

    public function show(Document $document)
    {
        abort_if($document->tenant_id !== $this->tid(), 403);
        return view('documents.show', compact('document'));
    }

    public function download(Document $document)
    {
        abort_if($document->tenant_id !== $this->tid(), 403);
        return Storage::disk('public')->download($document->file_path, $document->file_name);
    }

    public function destroy(Document $document)
    {
        abort_if($document->tenant_id !== $this->tid(), 403);
        Storage::disk('public')->delete($document->file_path);
        $document->delete();
        return redirect()->route('documents.index')->with('success', 'Document deleted.');
    }
}
