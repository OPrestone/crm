<?php

namespace App\Http\Controllers;

use App\Models\Card;
use App\Models\CardTemplate;
use App\Models\Contact;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CardController extends Controller
{
    private function tid() { return Auth::user()->tenant_id; }

    public function index()
    {
        $templates = CardTemplate::where('tenant_id', $this->tid())->withCount('cards')->get();
        $cards = Card::where('tenant_id', $this->tid())->with(['template', 'contact'])->latest()->paginate(12);
        return view('cards.index', compact('templates', 'cards'));
    }

    public function createTemplate()
    {
        return view('cards.template-create');
    }

    public function storeTemplate(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:200',
            'category' => 'required|in:business,id,membership,event',
            'design' => 'nullable|array',
            'fields' => 'nullable|array',
        ]);
        $data['tenant_id'] = $this->tid();
        $data['created_by'] = Auth::id();
        CardTemplate::create($data);
        return redirect()->route('cards.index')->with('success', 'Template created!');
    }

    public function create()
    {
        $templates = CardTemplate::where('tenant_id', $this->tid())->get();
        $contacts = Contact::where('tenant_id', $this->tid())->orderBy('first_name')->get();
        return view('cards.create', compact('templates', 'contacts'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:200',
            'template_id' => 'nullable|exists:card_templates,id',
            'contact_id' => 'nullable|exists:contacts,id',
            'data' => 'nullable|array',
        ]);
        $data['tenant_id'] = $this->tid();
        $data['created_by'] = Auth::id();
        Card::create($data);
        return redirect()->route('cards.index')->with('success', 'Card created!');
    }

    public function show(Card $card)
    {
        abort_if($card->tenant_id !== $this->tid(), 403);
        $card->load(['template', 'contact']);
        return view('cards.show', compact('card'));
    }

    public function destroy(Card $card)
    {
        abort_if($card->tenant_id !== $this->tid(), 403);
        $card->delete();
        return redirect()->route('cards.index')->with('success', 'Card deleted.');
    }

    public function pdf(Card $card)
    {
        abort_if($card->tenant_id !== $this->tid(), 403);
        $card->load(['template', 'contact']);
        $pdf = Pdf::loadView('cards.pdf', compact('card'))->setPaper([0, 0, 153.07, 241.89]);
        return $pdf->stream("card-{$card->id}.pdf");
    }
}
