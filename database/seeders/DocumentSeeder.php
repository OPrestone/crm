<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Contact;
use App\Models\Deal;
use App\Models\Document;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;

class DocumentSeeder extends Seeder
{
    public function run(): void
    {
        $tid      = Tenant::where('slug', 'acme-corp')->value('id');
        $owner    = User::where('email', 'demo@acme.com')->first();
        $staff1   = User::where('email', 'staff@acme.com')->first();
        $contacts = Contact::where('tenant_id', $tid)->get();
        $companies= Company::where('tenant_id', $tid)->get();
        $deals    = Deal::where('tenant_id', $tid)->get();

        // [title, fileName, fileType, fileSize, category, description, morphType, morphIdx]
        $data = [
            ['TechNova Enterprise Proposal 2025',  'technova-enterprise-proposal-2025.pdf',  'application/pdf',        2457600,  'Proposal',   'Full enterprise proposal including pricing, timeline, and SLA.', 'deal',    0],
            ['GlobalRetail NDA Agreement',         'globalretail-nda-signed.pdf',             'application/pdf',        524288,   'Contract',   'Signed mutual NDA between Acme Corp and GlobalRetail Ltd.',      'contact', 1],
            ['Meridian Finance SOW',               'meridian-finance-sow-v2.docx',            'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 384000, 'SOW', 'Statement of work for data platform implementation.', 'deal', 2],
            ['Helix Biotech Compliance Checklist', 'helix-compliance-checklist.xlsx',         'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 204800, 'Compliance', 'Compliance requirements checklist for regulatory module.', 'company', 4],
            ['DataStream API Integration Guide',   'datastream-api-integration-guide.pdf',   'application/pdf',        1048576,  'Technical',  'Step-by-step API integration guide for DataStream Corp.',         'deal',    6],
            ['NorthStar Ventures Pitch Deck',      'northstar-pitch-deck-q4.pptx',            'application/vnd.openxmlformats-officedocument.presentationml.presentation', 5242880, 'Presentation', 'Investor-grade pitch deck for NorthStar portfolio CRM module.', 'deal', 10],
            ['Orion Pharma Contract Draft',        'orion-pharma-contract-draft-v1.pdf',      'application/pdf',        3145728,  'Contract',   'Initial contract draft for Orion Pharma regulatory suite.',      'company', 11],
            ['Pacific Holdings Meeting Notes',     'pacific-holdings-meeting-notes-oct.docx', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 102400, 'Notes', 'Meeting notes from initial discovery session with Pacific Holdings.', 'deal', 12],
            ['Aurora Media Brand Guidelines',      'aurora-media-brand-guidelines.pdf',       'application/pdf',        8388608,  'Reference',  'Brand identity and design guidelines provided by Aurora Media.',   'contact', 5],
            ['Acme CRM Onboarding Guide',          'acme-crm-onboarding-guide-v3.pdf',        'application/pdf',        1572864,  'Internal',   'Internal onboarding guide for new Acme CRM clients.',             'contact', 0],
        ];

        foreach ($data as [$title, $fileName, $fileType, $fileSize, $category, $desc, $morphType, $morphIdx]) {
            if ($morphType === 'deal') {
                $morphable = $deals->get($morphIdx);
            } elseif ($morphType === 'contact') {
                $morphable = $contacts->get($morphIdx);
            } else {
                $morphable = $companies->get($morphIdx);
            }

            Document::create([
                'tenant_id'         => $tid,
                'title'             => $title,
                'file_name'         => $fileName,
                'file_path'         => 'documents/' . date('Y/m') . '/' . $fileName,
                'file_type'         => $fileType,
                'file_size'         => $fileSize,
                'category'          => $category,
                'description'       => $desc,
                'documentable_id'   => $morphable ? $morphable->id : null,
                'documentable_type' => $morphable ? get_class($morphable) : null,
                'uploaded_by'       => ($morphIdx % 2 === 0) ? $owner->id : $staff1->id,
            ]);
        }
    }
}
