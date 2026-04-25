<?php

namespace App\Http\Controllers;

use App\Models\Card;
use App\Models\CardTemplate;
use App\Models\Contact;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class CardController extends Controller
{
    private function tid() { return Auth::user()->tenant_id; }

    public function index()
    {
        $templates = CardTemplate::where('tenant_id', $this->tid())->withCount('cards')->get();
        $cards     = Card::where('tenant_id', $this->tid())->with(['template', 'contact'])->latest()->paginate(12);
        return view('cards.index', compact('templates', 'cards'));
    }

    public function createTemplate()
    {
        return view('cards.template-create');
    }

    public function storeTemplate(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:200',
            'category' => 'required|in:business,id,membership,event',
            'design'   => 'nullable|array',
            'fields'   => 'nullable|array',
        ]);
        $data['tenant_id']  = $this->tid();
        $data['created_by'] = Auth::id();
        CardTemplate::create($data);
        return redirect()->route('cards.index')->with('success', 'Template created!');
    }

    public function create()
    {
        $templates = CardTemplate::where('tenant_id', $this->tid())->get();
        $contacts  = Contact::where('tenant_id', $this->tid())->orderBy('first_name')->get();
        return view('cards.create', compact('templates', 'contacts'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:200',
            'template_id' => 'nullable|exists:card_templates,id',
            'contact_id'  => 'nullable|exists:contacts,id',
            'photo'       => 'nullable|file|mimes:jpg,jpeg,png|max:3072',
            'qr_data'     => 'nullable|string|max:500',
            'data'        => 'nullable|array',
        ]);

        $data['tenant_id']  = $this->tid();
        $data['created_by'] = Auth::id();

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store("cards/{$this->tid()}", 'public');
        }

        // Auto-build QR data from contact if not provided
        if (empty($data['qr_data']) && $request->contact_id) {
            $contact = Contact::find($request->contact_id);
            if ($contact) {
                $vcf = "BEGIN:VCARD\nVERSION:3.0\n";
                $vcf .= "FN:{$contact->first_name} {$contact->last_name}\n";
                if ($contact->email) $vcf .= "EMAIL:{$contact->email}\n";
                if ($contact->phone) $vcf .= "TEL:{$contact->phone}\n";
                if ($contact->company) $vcf .= "ORG:{$contact->company->name}\n";
                $vcf .= "END:VCARD";
                $data['qr_data'] = $vcf;
            }
        }

        Card::create($data);
        return redirect()->route('cards.index')->with('success', 'Card created!');
    }

    public function show(Card $card)
    {
        abort_if($card->tenant_id !== $this->tid(), 403);
        $card->load(['template', 'contact.company']);

        $qrCode = null;
        if ($card->qr_data) {
            $svg = (string) QrCode::format('svg')->size(180)->margin(1)->generate($card->qr_data);
            $qrCode = preg_replace('/(<svg[^>]*)\s+width="\d+"/', '$1 width="100%"', $svg);
            $qrCode = preg_replace('/(<svg[^>]*)\s+height="\d+"/', '$1 height="100%"', $qrCode);
        }

        return view('cards.show', compact('card', 'qrCode'));
    }

    public function edit(Card $card)
    {
        abort_if($card->tenant_id !== $this->tid(), 403);
        $card->load(['template', 'contact']);
        $templates = CardTemplate::where('tenant_id', $this->tid())->get();
        $contacts  = Contact::where('tenant_id', $this->tid())->orderBy('first_name')->get();
        return view('cards.edit', compact('card', 'templates', 'contacts'));
    }

    public function update(Request $request, Card $card)
    {
        abort_if($card->tenant_id !== $this->tid(), 403);

        $data = $request->validate([
            'name'        => 'required|string|max:200',
            'template_id' => 'nullable|exists:card_templates,id',
            'contact_id'  => 'nullable|exists:contacts,id',
            'photo'       => 'nullable|file|mimes:jpg,jpeg,png|max:3072',
            'qr_data'     => 'nullable|string|max:500',
            'data'        => 'nullable|array',
        ]);

        if ($request->hasFile('photo')) {
            if ($card->photo) Storage::disk('public')->delete($card->photo);
            $data['photo'] = $request->file('photo')->store("cards/{$this->tid()}", 'public');
        }

        if (empty($data['qr_data']) && $request->contact_id) {
            $contact = Contact::find($request->contact_id);
            if ($contact) {
                $vcf = "BEGIN:VCARD\nVERSION:3.0\n";
                $vcf .= "FN:{$contact->first_name} {$contact->last_name}\n";
                if ($contact->email) $vcf .= "EMAIL:{$contact->email}\n";
                if ($contact->phone) $vcf .= "TEL:{$contact->phone}\n";
                if ($contact->company) $vcf .= "ORG:{$contact->company->name}\n";
                $vcf .= "END:VCARD";
                $data['qr_data'] = $vcf;
            }
        }

        $card->update($data);
        return redirect()->route('cards.show', $card)->with('success', 'Card updated!');
    }

    public function destroy(Card $card)
    {
        abort_if($card->tenant_id !== $this->tid(), 403);
        if ($card->photo) Storage::disk('public')->delete($card->photo);
        $card->delete();
        return redirect()->route('cards.index')->with('success', 'Card deleted.');
    }

    public function pdf(Card $card)
    {
        abort_if($card->tenant_id !== $this->tid(), 403);
        $card->load(['template', 'contact.company']);

        $qrCode = null;
        if ($card->qr_data) {
            // Raw SVG string — template embeds it as a base64 data URI so DomPDF renders it
            $qrCode = (string) QrCode::format('svg')->size(200)->margin(1)->generate($card->qr_data);
        }

        $photoBase64 = null;
        if ($card->photo && Storage::disk('public')->exists($card->photo)) {
            $photoData   = Storage::disk('public')->get($card->photo);
            $mime        = Storage::disk('public')->mimeType($card->photo);
            $photoBase64 = "data:{$mime};base64," . base64_encode($photoData);
        }

        $pdf = Pdf::loadView('cards.pdf', compact('card', 'qrCode', 'photoBase64'))
            ->setPaper('a4', 'portrait');
        return $pdf->stream("card-{$card->id}.pdf");
    }
}
