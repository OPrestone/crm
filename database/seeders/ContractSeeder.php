<?php

namespace Database\Seeders;

use App\Models\Contact;
use App\Models\Contract;
use App\Models\ContractTemplate;
use App\Models\Deal;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;

class ContractSeeder extends Seeder
{
    public function run(): void
    {
        $tid      = Tenant::where('slug', 'prestech-corp')->value('id');
        $owner    = User::where('email', 'demo@prestech.com')->first();
        $manager  = User::where('email', 'manager@prestech.com')->first();
        $contacts = Contact::where('tenant_id', $tid)->get();
        $deals    = Deal::where('tenant_id', $tid)->get();

        // ── Contract Templates ────────────────────────────────────────────
        $msaTemplate = ContractTemplate::create([
            'tenant_id'  => $tid,
            'name'       => 'Master Service Agreement (MSA)',
            'content'    => '<h2>Master Service Agreement</h2><p>This Master Service Agreement ("Agreement") is entered into as of {{start_date}} between {{company_name}} ("Client") and prestech Corporation ("Provider").</p><h3>1. Services</h3><p>Provider agrees to deliver the CRM platform services as described in the applicable Order Form. Services commence on {{start_date}} and continue through {{end_date}}.</p><h3>2. Payment Terms</h3><p>Client agrees to pay {{contract_value}} USD as set out in the Order Form. Payments are due Net 30.</p><h3>3. Confidentiality</h3><p>Both parties agree to keep proprietary information confidential.</p><p><em>Signed by: {{signed_by}}</em></p>',
            'variables'  => ['start_date', 'end_date', 'company_name', 'contract_value', 'signed_by'],
            'created_by' => $owner->id,
        ]);

        $nsdaTemplate = ContractTemplate::create([
            'tenant_id'  => $tid,
            'name'       => 'Non-Disclosure Agreement (NDA)',
            'content'    => '<h2>Non-Disclosure Agreement</h2><p>This NDA is entered into as of {{start_date}} between {{company_name}} and prestech Corporation.</p><h3>Obligations</h3><p>Both parties agree not to disclose confidential information to any third party without prior written consent.</p><p><em>Signed by: {{signed_by}}</em></p>',
            'variables'  => ['start_date', 'company_name', 'signed_by'],
            'created_by' => $manager->id,
        ]);

        $soaTemplate = ContractTemplate::create([
            'tenant_id'  => $tid,
            'name'       => 'Statement of Work (SOW)',
            'content'    => '<h2>Statement of Work</h2><p>This SOW is attached to and governed by the MSA between {{company_name}} and prestech Corporation, effective {{start_date}}.</p><h3>Scope</h3><p>prestech Corporation will deliver the following: CRM implementation, data migration, and onboarding training, as agreed.</p><h3>Timeline</h3><p>Project is expected to complete by {{end_date}}.</p><h3>Value</h3><p>Total contract value: ${{contract_value}} USD.</p>',
            'variables'  => ['start_date', 'end_date', 'company_name', 'contract_value'],
            'created_by' => $owner->id,
        ]);

        // ── Contracts ─────────────────────────────────────────────────────
        $contractData = [
            [
                'title'    => 'TechNova Enterprise MSA 2025',
                'template' => $msaTemplate,
                'cIdx'     => 0,
                'dealIdx'  => 0,
                'value'    => 185000,
                'status'   => 'signed',
                'start'    => '-30 days',
                'end'      => '+335 days',
                'signedAt' => '-28 days',
                'signedBy' => 'Alice Thompson',
            ],
            [
                'title'    => 'GlobalRetail Annual Contract 2025',
                'template' => $msaTemplate,
                'cIdx'     => 1,
                'dealIdx'  => 1,
                'value'    => 420000,
                'status'   => 'pending_signature',
                'start'    => '+5 days',
                'end'      => '+370 days',
                'signedAt' => null,
                'signedBy' => null,
            ],
            [
                'title'    => 'Helix Biotech Compliance NDA',
                'template' => $nsdaTemplate,
                'cIdx'     => 4,
                'dealIdx'  => 4,
                'value'    => 0,
                'status'   => 'signed',
                'start'    => '-60 days',
                'end'      => '+305 days',
                'signedAt' => '-58 days',
                'signedBy' => 'Eleanor Nguyen',
            ],
            [
                'title'    => 'DataStream Corp SOW – Core CRM',
                'template' => $soaTemplate,
                'cIdx'     => 6,
                'dealIdx'  => 6,
                'value'    => 215000,
                'status'   => 'draft',
                'start'    => '+7 days',
                'end'      => '+190 days',
                'signedAt' => null,
                'signedBy' => null,
            ],
            [
                'title'    => 'NorthStar Ventures Fund Analytics MSA',
                'template' => $msaTemplate,
                'cIdx'     => 10,
                'dealIdx'  => 10,
                'value'    => 340000,
                'status'   => 'pending_signature',
                'start'    => '+10 days',
                'end'      => '+375 days',
                'signedAt' => null,
                'signedBy' => null,
            ],
            [
                'title'    => 'BlueWave Consulting Starter NDA',
                'template' => $nsdaTemplate,
                'cIdx'     => 9,
                'dealIdx'  => 9,
                'value'    => 0,
                'status'   => 'signed',
                'start'    => '-20 days',
                'end'      => '+345 days',
                'signedAt' => '-18 days',
                'signedBy' => 'James Hoffmann',
            ],
            [
                'title'    => 'Orion Pharma Regulatory Suite MSA',
                'template' => $msaTemplate,
                'cIdx'     => 11,
                'dealIdx'  => 11,
                'value'    => 127000,
                'status'   => 'expired',
                'start'    => '-400 days',
                'end'      => '-35 days',
                'signedAt' => '-398 days',
                'signedBy' => 'Isabella Romano',
            ],
        ];

        foreach ($contractData as $data) {
            $deal = $deals->get($data['dealIdx']);
            Contract::create([
                'tenant_id'  => $tid,
                'title'      => $data['title'],
                'template_id'=> $data['template']->id,
                'contact_id' => $contacts[$data['cIdx']]->id,
                'deal_id'    => $deal ? $deal->id : null,
                'content'    => $data['template']->content,
                'value'      => $data['value'],
                'status'     => $data['status'],
                'start_date' => now()->modify($data['start']),
                'end_date'   => now()->modify($data['end']),
                'signed_at'  => $data['signedAt'] ? now()->modify($data['signedAt']) : null,
                'signed_by'  => $data['signedBy'],
                'notes'      => 'Reviewed by legal team. Standard enterprise terms applied.',
                'created_by' => $owner->id,
            ]);
        }
    }
}
