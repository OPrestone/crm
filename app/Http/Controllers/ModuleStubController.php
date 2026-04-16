<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ModuleStubController extends Controller
{
    private static array $moduleInfo = [
        'email_campaigns' => [
            'title'       => 'Email Campaigns',
            'icon'        => 'bi-envelope-paper-fill',
            'color'       => 'primary',
            'description' => 'Design and send targeted email campaigns to segments of your contacts. Track opens, clicks, and conversions in real time.',
            'features'    => ['Drag-and-drop email builder','Contact list segmentation','Open & click tracking','Automated drip sequences','Campaign analytics dashboard','A/B testing','Unsubscribe management'],
            'plan'        => 'Pro',
        ],
        'web_forms' => [
            'title'       => 'Web Forms & Lead Capture',
            'icon'        => 'bi-ui-checks-grid',
            'color'       => 'success',
            'description' => 'Build embeddable forms that automatically create leads and contacts in your CRM when visitors submit them.',
            'features'    => ['Visual form builder','Embed on any website','Auto-create contacts & leads','Custom field mapping','Spam protection (CAPTCHA)','Multi-step forms','Conditional logic'],
            'plan'        => 'Pro',
        ],
        'contracts' => [
            'title'       => 'Contract Management',
            'icon'        => 'bi-file-earmark-text-fill',
            'color'       => 'warning',
            'description' => 'Manage the full contract lifecycle — from drafting and negotiation through to e-signature and renewal reminders.',
            'features'    => ['Contract templates library','Version history & redlines','E-signature integration','Renewal reminders','Link to deals & contacts','PDF generation','Approval workflows'],
            'plan'        => 'Pro',
        ],
        'forecasting' => [
            'title'       => 'Sales Forecasting',
            'icon'        => 'bi-graph-up-arrow',
            'color'       => 'info',
            'description' => 'Predict future revenue with AI-assisted pipeline analysis, weighted forecasts, and quota tracking.',
            'features'    => ['Weighted pipeline forecast','Quota vs actual tracking','Stage-based probability','Team & rep breakdowns','Historical trend analysis','CSV export','Forecast snapshots'],
            'plan'        => 'Pro',
        ],
        'commissions' => [
            'title'       => 'Commission Tracking',
            'icon'        => 'bi-cash-coin',
            'color'       => 'success',
            'description' => 'Define commission plans per rep or role and automatically calculate earned commissions from closed deals.',
            'features'    => ['Commission plan builder','Auto-calculation on deal close','Tiered & flat rate support','Rep earnings dashboard','Monthly payout reports','Deal dispute tracking','Export to payroll'],
            'plan'        => 'Enterprise',
        ],
        'territories' => [
            'title'       => 'Territory Management',
            'icon'        => 'bi-map-fill',
            'color'       => 'secondary',
            'description' => 'Define geographic or account-based territories and assign them to sales reps for focused pipeline management.',
            'features'    => ['Geographic territory maps','Account-based assignment','Automatic lead routing','Territory performance reports','Conflict detection','Rep capacity tracking','Reassignment workflows'],
            'plan'        => 'Enterprise',
        ],
        'audit_log' => [
            'title'       => 'Audit Log & Compliance',
            'icon'        => 'bi-journal-check',
            'color'       => 'danger',
            'description' => 'Maintain a tamper-proof trail of every action performed in the CRM, meeting GDPR, SOC 2, and internal audit requirements.',
            'features'    => ['Full event timeline per record','User action attribution','IP address & device logging','Export for compliance reports','Retention policy settings','Alerting on suspicious activity','GDPR data-export requests'],
            'plan'        => 'Enterprise',
        ],
        'api_access' => [
            'title'       => 'API & Webhooks',
            'icon'        => 'bi-code-slash',
            'color'       => 'dark',
            'description' => 'Integrate your CRM with any third-party tool using a RESTful API and configurable webhooks for real-time event streaming.',
            'features'    => ['REST API with OAuth2 / API Keys','Configurable webhooks','API rate limiting & quotas','Interactive API docs (Swagger)','Postman collection download','Event log for all webhook calls','Sandbox / test environment'],
            'plan'        => 'Enterprise',
        ],
    ];

    public function show(string $module)
    {
        $info = self::$moduleInfo[$module] ?? null;
        abort_if(!$info, 404);
        return view('modules.coming-soon', ['info' => $info, 'module' => $module]);
    }
}
