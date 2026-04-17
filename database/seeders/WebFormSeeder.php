<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\User;
use App\Models\WebForm;
use App\Models\WebFormSubmission;
use Illuminate\Database\Seeder;

class WebFormSeeder extends Seeder
{
    public function run(): void
    {
        $tid   = Tenant::where('slug', 'acme-corp')->value('id');
        $owner = User::where('email', 'demo@acme.com')->first();

        $forms = [
            [
                'name'            => 'Contact Sales',
                'description'     => 'Lead capture form for the website homepage. Routes to sales team.',
                'submit_action'   => 'both',
                'success_message' => 'Thank you! A member of our team will be in touch within 24 hours.',
                'redirect_url'    => null,
                'is_active'       => true,
                'fields'          => [
                    ['name' => 'first_name', 'label' => 'First Name',    'type' => 'text',   'required' => true],
                    ['name' => 'last_name',  'label' => 'Last Name',     'type' => 'text',   'required' => true],
                    ['name' => 'email',      'label' => 'Email Address', 'type' => 'email',  'required' => true],
                    ['name' => 'company',    'label' => 'Company Name',  'type' => 'text',   'required' => false],
                    ['name' => 'phone',      'label' => 'Phone Number',  'type' => 'tel',    'required' => false],
                    ['name' => 'message',    'label' => 'How can we help?', 'type' => 'textarea', 'required' => false],
                ],
                'submissions' => [
                    ['first_name' => 'James', 'last_name' => 'Walker', 'email' => 'james.walker@acmeleads.com', 'company' => 'Walker & Sons', 'phone' => '+1 555-901-2345', 'message' => 'Interested in your enterprise plan.'],
                    ['first_name' => 'Sophia', 'last_name' => 'Reed', 'email' => 'sophia@reedventures.com', 'company' => 'Reed Ventures', 'message' => 'Need a demo ASAP.'],
                    ['first_name' => 'Carlos', 'last_name' => 'Vega', 'email' => 'carlos.vega@techfirm.io', 'company' => 'TechFirm', 'phone' => '+1 555-234-5678', 'message' => 'Looking to switch from Salesforce.'],
                ],
            ],
            [
                'name'            => 'Webinar Registration',
                'description'     => 'Registration form for the CRM Best Practices webinar on Nov 15, 2025.',
                'submit_action'   => 'lead',
                'success_message' => 'You are registered! Check your email for the link.',
                'redirect_url'    => 'https://acmecorp.com/webinar-confirmed',
                'is_active'       => true,
                'fields'          => [
                    ['name' => 'first_name', 'label' => 'First Name',    'type' => 'text',   'required' => true],
                    ['name' => 'last_name',  'label' => 'Last Name',     'type' => 'text',   'required' => true],
                    ['name' => 'email',      'label' => 'Work Email',    'type' => 'email',  'required' => true],
                    ['name' => 'job_title',  'label' => 'Job Title',     'type' => 'text',   'required' => false],
                    ['name' => 'company',    'label' => 'Company',       'type' => 'text',   'required' => false],
                ],
                'submissions' => [
                    ['first_name' => 'Mark', 'last_name' => 'Oliver', 'email' => 'mark.oliver@globalfirm.com', 'job_title' => 'VP Sales', 'company' => 'Global Firm'],
                    ['first_name' => 'Lisa', 'last_name' => 'Chang', 'email' => 'lisa.chang@innovate.io', 'job_title' => 'CRM Admin', 'company' => 'Innovate IO'],
                ],
            ],
            [
                'name'            => 'Free Trial Request',
                'description'     => 'Capture free trial signups from landing pages.',
                'submit_action'   => 'contact',
                'success_message' => 'Welcome! Your trial account is being set up — check your inbox.',
                'redirect_url'    => null,
                'is_active'       => true,
                'fields'          => [
                    ['name' => 'name',    'label' => 'Full Name',    'type' => 'text',  'required' => true],
                    ['name' => 'email',   'label' => 'Work Email',   'type' => 'email', 'required' => true],
                    ['name' => 'company', 'label' => 'Company Name', 'type' => 'text',  'required' => true],
                    ['name' => 'team_size','label' => 'Team Size',   'type' => 'select','required' => true, 'options' => ['1-5','6-20','21-100','100+']],
                ],
                'submissions' => [
                    ['name' => 'Tom Bradley', 'email' => 'tom@bradleyco.com', 'company' => 'Bradley Co', 'team_size' => '6-20'],
                    ['name' => 'Priya Sharma', 'email' => 'priya@startuphub.io', 'company' => 'StartupHub', 'team_size' => '1-5'],
                    ['name' => 'Andrew Mills', 'email' => 'amills@millsgroup.com', 'company' => 'Mills Group', 'team_size' => '21-100'],
                    ['name' => 'Fiona Adams', 'email' => 'f.adams@crescentai.com', 'company' => 'Crescent AI', 'team_size' => '1-5'],
                ],
            ],
        ];

        foreach ($forms as $formData) {
            $submissions = $formData['submissions'];
            unset($formData['submissions']);

            $form = WebForm::create([
                'tenant_id'       => $tid,
                'name'            => $formData['name'],
                'description'     => $formData['description'],
                'submit_action'   => $formData['submit_action'],
                'success_message' => $formData['success_message'],
                'redirect_url'    => $formData['redirect_url'],
                'is_active'       => $formData['is_active'],
                'fields'          => $formData['fields'],
                'created_by'      => $owner->id,
            ]);

            foreach ($submissions as $sub) {
                WebFormSubmission::create([
                    'form_id'    => $form->id,
                    'tenant_id'  => $tid,
                    'data'       => $sub,
                    'ip_address' => '192.168.' . rand(1, 255) . '.' . rand(1, 254),
                    'processed'  => (bool) rand(0, 1),
                ]);
            }
        }
    }
}
