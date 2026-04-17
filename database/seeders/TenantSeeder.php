<?php

namespace Database\Seeders;

use App\Models\CrmSetting;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TenantSeeder extends Seeder
{
    public function run(): void
    {
        // ── Platform super-admin tenant ───────────────────────────────────
        $superTenant = Tenant::create([
            'name'         => 'Platform Admin',
            'slug'         => 'platform-admin',
            'email'        => 'admin@crm.io',
            'plan'         => 'enterprise',
            'status'       => 'active',
            'max_users'    => 999,
            'max_contacts' => 999999,
            'currency'     => 'USD',
        ]);

        $superAdmin = User::create([
            'name'      => 'Super Admin',
            'email'     => 'admin@crm.io',
            'password'  => Hash::make('password'),
            'tenant_id' => $superTenant->id,
            'job_title' => 'Platform Administrator',
        ]);
        $superAdmin->assignRole('super_admin');

        // ── Demo tenant: Acme Corporation ─────────────────────────────────
        $tenant = Tenant::create([
            'name'         => 'Acme Corporation',
            'slug'         => 'acme-corp',
            'email'        => 'hello@acmecorp.com',
            'phone'        => '+1 415-555-0100',
            'website'      => 'https://acmecorp.com',
            'plan'         => 'enterprise',
            'status'       => 'active',
            'max_users'    => 20,
            'max_contacts' => 10000,
            'currency'     => 'USD',
            'address'      => '101 Market Street, San Francisco, CA 94105',
        ]);
        $tid = $tenant->id;

        $owner = User::create([
            'name'      => 'John Smith',
            'email'     => 'demo@acme.com',
            'password'  => Hash::make('password'),
            'tenant_id' => $tid,
            'job_title' => 'CEO',
            'phone'     => '+1 415-555-0101',
        ]);
        $owner->assignRole('tenant_admin');

        $manager = User::create([
            'name'      => 'Sarah Johnson',
            'email'     => 'manager@acme.com',
            'password'  => Hash::make('password'),
            'tenant_id' => $tid,
            'job_title' => 'Sales Manager',
            'phone'     => '+1 415-555-0102',
        ]);
        $manager->assignRole('manager');

        $staff1 = User::create([
            'name'      => 'Michael Lee',
            'email'     => 'staff@acme.com',
            'password'  => Hash::make('password'),
            'tenant_id' => $tid,
            'job_title' => 'Account Executive',
            'phone'     => '+1 415-555-0103',
        ]);
        $staff1->assignRole('staff');

        $staff2 = User::create([
            'name'      => 'Emily Chen',
            'email'     => 'emily@acme.com',
            'password'  => Hash::make('password'),
            'tenant_id' => $tid,
            'job_title' => 'Business Development Rep',
            'phone'     => '+1 415-555-0104',
        ]);
        $staff2->assignRole('staff');

        // ── CRM Settings for demo tenant (key-value) ─────────────────────
        $settings = [
            ['general', 'company_name',      'Acme Corporation'],
            ['general', 'currency',          'USD'],
            ['general', 'date_format',       'M j, Y'],
            ['general', 'timezone',          'America/Los_Angeles'],
            ['general', 'fiscal_year_start', '1'],
            ['general', 'language',          'en'],
            ['email',   'from_name',         'Acme Corporation'],
            ['email',   'from_email',        'hello@acmecorp.com'],
        ];
        foreach ($settings as [$group, $key, $value]) {
            CrmSetting::create(['tenant_id' => $tid, 'group' => $group, 'key' => $key, 'value' => $value]);
        }
    }
}
