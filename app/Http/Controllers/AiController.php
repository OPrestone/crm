<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Contact;
use App\Models\Deal;
use App\Models\Invoice;
use App\Models\Lead;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AiController extends Controller
{
    private function tenantId(): int
    {
        return Auth::user()->tenant_id;
    }

    public function index()
    {
        $insights = $this->buildDashboardInsights();
        return view('ai.index', compact('insights'));
    }

    public function leadScore(Lead $lead)
    {
        abort_if($lead->tenant_id !== $this->tenantId(), 403);
        $lead->load(['contact', 'stage', 'assignedTo', 'activities']);

        $score     = $this->scoreLeadLogic($lead);
        $reasoning = $this->leadScoreReasoning($lead, $score);
        $actions   = $this->leadNextActions($lead);
        $emailDraft= $this->generateLeadEmail($lead);

        return view('ai.lead-score', compact('lead', 'score', 'reasoning', 'actions', 'emailDraft'));
    }

    public function dealInsight(Deal $deal)
    {
        abort_if($deal->tenant_id !== $this->tenantId(), 403);
        $deal->load(['contact', 'company', 'stage', 'assignedTo', 'activities', 'tasks']);

        $probability = $this->dealWinProbability($deal);
        $reasoning   = $this->dealReasoning($deal, $probability);
        $risks       = $this->dealRisks($deal);
        $actions     = $this->dealNextActions($deal);
        $emailDraft  = $this->generateDealEmail($deal);

        return view('ai.deal-insight', compact('deal', 'probability', 'reasoning', 'risks', 'actions', 'emailDraft'));
    }

    public function contactEnrich(Contact $contact)
    {
        abort_if($contact->tenant_id !== $this->tenantId(), 403);
        $contact->load(['company', 'deals', 'leads', 'tasks', 'activities']);

        $profile   = $this->buildContactProfile($contact);
        $emailDraft= $this->generateContactEmail($contact);
        $score     = $this->contactEngagementScore($contact);

        return view('ai.contact-enrich', compact('contact', 'profile', 'emailDraft', 'score'));
    }

    public function emailCompose(Request $request)
    {
        $type    = $request->type ?? 'follow_up';
        $context = [];

        if ($request->lead_id) {
            $lead = Lead::where('tenant_id', $this->tenantId())->findOrFail($request->lead_id);
            $lead->load('contact');
            $context['lead']    = $lead;
            $context['subject'] = $this->leadEmailSubject($type, $lead);
            $context['body']    = $this->leadEmailBody($type, $lead);
        } elseif ($request->deal_id) {
            $deal = Deal::where('tenant_id', $this->tenantId())->findOrFail($request->deal_id);
            $deal->load(['contact', 'company']);
            $context['deal']    = $deal;
            $context['subject'] = $this->dealEmailSubject($type, $deal);
            $context['body']    = $this->dealEmailBody($type, $deal);
        } elseif ($request->contact_id) {
            $contact = Contact::where('tenant_id', $this->tenantId())->findOrFail($request->contact_id);
            $contact->load('company');
            $context['contact'] = $contact;
            $context['subject'] = $this->contactEmailSubject($type, $contact);
            $context['body']    = $this->contactEmailBody($type, $contact);
        }

        $leads    = Lead::where('tenant_id', $this->tenantId())->with('contact')->latest()->take(20)->get();
        $deals    = Deal::where('tenant_id', $this->tenantId())->with('contact')->latest()->take(20)->get();
        $contacts = Contact::where('tenant_id', $this->tenantId())->orderBy('first_name')->take(50)->get();

        return view('ai.email-compose', compact('context', 'leads', 'deals', 'contacts', 'type'));
    }

    public function insights()
    {
        $insights = $this->buildDashboardInsights();
        $tid = $this->tenantId();

        $topDeals     = Deal::where('tenant_id', $tid)->where('status','open')->orderByDesc('value')->take(5)->with(['contact','stage'])->get();
        $stalledLeads = Lead::where('tenant_id', $tid)->whereNotIn('status',['won','lost','converted'])
            ->where('updated_at', '<', now()->subDays(14))->with(['contact','stage'])->take(6)->get();
        $overdueTasks = Task::where('tenant_id', $tid)->where('status','!=','done')
            ->where('due_date', '<', now())->take(6)->get();
        $wonDeals     = Deal::where('tenant_id', $tid)->where('status','won')
            ->whereMonth('updated_at', now()->month)->sum('value');
        $lostDeals    = Deal::where('tenant_id', $tid)->where('status','lost')
            ->whereMonth('updated_at', now()->month)->sum('value');

        return view('ai.insights', compact('insights','topDeals','stalledLeads','overdueTasks','wonDeals','lostDeals'));
    }

    // ─── Internal Scoring Logic ───────────────────────────────────────────────

    private function scoreLeadLogic(Lead $lead): int
    {
        $score = 0;
        // Source quality
        $score += match($lead->source ?? '') {
            'referral'    => 30,
            'website'     => 20,
            'linkedin'    => 18,
            'cold_call'   => 10,
            'trade_show'  => 22,
            default       => 10,
        };
        // Budget
        if ($lead->budget) {
            if ($lead->budget >= 50000) $score += 25;
            elseif ($lead->budget >= 10000) $score += 18;
            elseif ($lead->budget >= 1000) $score += 10;
            else $score += 5;
        }
        // Activity recency
        $lastActivity = $lead->activities->sortByDesc('created_at')->first();
        if ($lastActivity) {
            $days = now()->diffInDays($lastActivity->created_at);
            if ($days <= 3)  $score += 20;
            elseif ($days <= 7)  $score += 15;
            elseif ($days <= 14) $score += 8;
        }
        // Stage progress
        $score += min(($lead->stage?->position ?? 0) * 3, 15);
        // Completeness
        if ($lead->contact?->email)       $score += 5;
        if ($lead->contact?->phone)       $score += 3;
        if ($lead->contact?->company_id)  $score += 4;

        return min($score, 100);
    }

    private function leadScoreReasoning(Lead $lead, int $score): array
    {
        $reasons = [];
        if ($lead->source === 'referral')  $reasons[] = ['icon' => 'bi-star-fill', 'color' => 'success', 'text' => 'Referral leads convert 3-4x higher than cold outreach'];
        if ($lead->budget >= 50000)        $reasons[] = ['icon' => 'bi-cash-stack', 'color' => 'success', 'text' => 'High budget qualification — strong purchase intent'];
        if ($lead->activities->count() > 3) $reasons[] = ['icon' => 'bi-activity', 'color' => 'info', 'text' => 'Multiple touchpoints recorded — engaged prospect'];
        if ($lead->contact?->email && $lead->contact?->phone) $reasons[] = ['icon' => 'bi-person-check', 'color' => 'info', 'text' => 'Complete contact info — outreach ready'];
        $lastActivity = $lead->activities->sortByDesc('created_at')->first();
        if ($lastActivity && now()->diffInDays($lastActivity->created_at) > 14) {
            $reasons[] = ['icon' => 'bi-clock-history', 'color' => 'warning', 'text' => 'No activity in 14+ days — risk of going cold'];
        }
        if ($score < 40) $reasons[] = ['icon' => 'bi-exclamation-triangle', 'color' => 'danger', 'text' => 'Low engagement signals — needs nurturing sequence'];
        return $reasons;
    }

    private function leadNextActions(Lead $lead): array
    {
        $actions = [];
        $lastActivity = $lead->activities->sortByDesc('created_at')->first();
        $daysSince = $lastActivity ? now()->diffInDays($lastActivity->created_at) : 999;

        if ($daysSince > 7)   $actions[] = ['priority' => 'high',   'icon' => 'bi-telephone', 'text' => 'Schedule a follow-up call — lead has gone quiet'];
        if (!$lead->contact?->email) $actions[] = ['priority' => 'high', 'icon' => 'bi-envelope', 'text' => 'Capture email address to enable email nurturing'];
        if ($lead->activities->count() < 2) $actions[] = ['priority' => 'medium', 'icon' => 'bi-calendar-event', 'text' => 'Book a discovery call to understand requirements'];
        if ($lead->budget === null)  $actions[] = ['priority' => 'medium', 'icon' => 'bi-question-circle', 'text' => 'Qualify budget — ask about investment range'];
        $actions[] = ['priority' => 'low', 'icon' => 'bi-file-earmark-text', 'text' => 'Send relevant case study or success story'];
        $actions[] = ['priority' => 'low', 'icon' => 'bi-linkedin', 'text' => 'Connect on LinkedIn to build relationship'];

        return array_slice($actions, 0, 5);
    }

    private function dealWinProbability(Deal $deal): int
    {
        $prob = 10;
        // Stage-based baseline
        $position = $deal->stage?->position ?? 0;
        $prob += min($position * 12, 50);
        // Value signals
        if ($deal->value >= 10000)  $prob += 10;
        if ($deal->value >= 50000)  $prob += 5;
        // Activity
        $recentActivity = $deal->activities->where('created_at', '>=', now()->subDays(7))->count();
        $prob += min($recentActivity * 5, 15);
        // Overdue tasks reduce
        $overdueTasks = $deal->tasks->where('due_date', '<', now())->where('status', '!=', 'done')->count();
        $prob -= $overdueTasks * 5;
        // Closed expected date
        if ($deal->expected_close_date && now()->isAfter($deal->expected_close_date)) $prob -= 15;

        return max(5, min($prob, 95));
    }

    private function dealReasoning(Deal $deal, int $prob): array
    {
        $reasons = [];
        if ($deal->stage?->position >= 4) $reasons[] = ['icon' => 'bi-funnel', 'color' => 'success', 'text' => 'Advanced pipeline stage — late-stage qualification'];
        if ($deal->activities->count() > 5) $reasons[] = ['icon' => 'bi-activity', 'color' => 'success', 'text' => 'Strong activity history shows sustained engagement'];
        if ($deal->expected_close_date && now()->isAfter($deal->expected_close_date)) {
            $reasons[] = ['icon' => 'bi-calendar-x', 'color' => 'danger', 'text' => 'Expected close date has passed — update timeline'];
        }
        $overdue = $deal->tasks->where('due_date', '<', now())->where('status','!=','done')->count();
        if ($overdue > 0) $reasons[] = ['icon' => 'bi-exclamation-circle', 'color' => 'warning', 'text' => "$overdue overdue task(s) may be blocking progression"];
        if ($deal->value > 20000) $reasons[] = ['icon' => 'bi-trophy', 'color' => 'info', 'text' => 'High-value deal — prioritize executive involvement'];
        return $reasons;
    }

    private function dealRisks(Deal $deal): array
    {
        $risks = [];
        if ($deal->expected_close_date && now()->isAfter($deal->expected_close_date)) {
            $risks[] = ['level' => 'high', 'text' => 'Close date exceeded — re-qualify or reschedule'];
        }
        if ($deal->activities->count() === 0) {
            $risks[] = ['level' => 'high', 'text' => 'No activities logged — deal may be stalled'];
        }
        $daysSince = $deal->activities->count() ? now()->diffInDays($deal->activities->sortByDesc('created_at')->first()->created_at) : 999;
        if ($daysSince > 10) {
            $risks[] = ['level' => 'medium', 'text' => "No activity for {$daysSince} days — risk of losing momentum"];
        }
        if (!$deal->contact?->email) {
            $risks[] = ['level' => 'medium', 'text' => 'Primary contact has no email — limits outreach options'];
        }
        if (empty($risks)) {
            $risks[] = ['level' => 'low', 'text' => 'No major risks detected — maintain current cadence'];
        }
        return $risks;
    }

    private function dealNextActions(Deal $deal): array
    {
        $actions = [];
        $overdue = $deal->tasks->where('due_date', '<', now())->where('status','!=','done');
        if ($overdue->count()) $actions[] = ['priority'=>'high','icon'=>'bi-check2-square','text'=> "Complete {$overdue->count()} overdue task(s) blocking this deal"];
        if ($deal->expected_close_date && now()->isAfter($deal->expected_close_date)) {
            $actions[] = ['priority'=>'high','icon'=>'bi-calendar-check','text'=>'Update expected close date to reflect current timeline'];
        }
        $actions[] = ['priority'=>'medium','icon'=>'bi-file-earmark-pdf','text'=>'Send formal proposal or quote document'];
        $actions[] = ['priority'=>'medium','icon'=>'bi-people','text'=>'Schedule stakeholder alignment meeting'];
        $actions[] = ['priority'=>'low','icon'=>'bi-shield-check','text'=>'Prepare legal/contract documentation in advance'];
        return array_slice($actions, 0, 5);
    }

    private function contactEngagementScore(Contact $contact): int
    {
        $score = 0;
        if ($contact->email)    $score += 20;
        if ($contact->phone)    $score += 15;
        if ($contact->company_id) $score += 10;
        $score += min($contact->deals->count() * 10, 30);
        $score += min($contact->activities->count() * 3, 15);
        $lastActivity = $contact->activities->sortByDesc('created_at')->first();
        if ($lastActivity && now()->diffInDays($lastActivity->created_at) <= 7) $score += 10;
        return min($score, 100);
    }

    private function buildContactProfile(Contact $contact): array
    {
        $openDeals  = $contact->deals->where('status', 'open');
        $wonDeals   = $contact->deals->where('status', 'won');
        $totalValue = $contact->deals->sum('value');

        $persona = 'General Prospect';
        if ($totalValue > 100000) $persona = 'Enterprise Key Account';
        elseif ($totalValue > 20000) $persona = 'Mid-Market Opportunity';
        elseif ($contact->leads->count() > 0) $persona = 'Active Lead';
        elseif ($wonDeals->count() > 0) $persona = 'Existing Customer';

        return [
            'persona'         => $persona,
            'open_deals'      => $openDeals->count(),
            'won_deals'       => $wonDeals->count(),
            'total_value'     => $totalValue,
            'activity_count'  => $contact->activities->count(),
            'engagement'      => $this->contactEngagementScore($contact),
            'recommendations' => $this->contactRecommendations($contact),
        ];
    }

    private function contactRecommendations(Contact $contact): array
    {
        $recs = [];
        if (!$contact->email) $recs[] = 'Add email address to enable digital outreach';
        if (!$contact->phone) $recs[] = 'Add phone number for direct contact capability';
        if (!$contact->company_id) $recs[] = 'Associate with a company for better account management';
        if ($contact->leads->count() === 0 && $contact->deals->count() === 0) {
            $recs[] = 'Create a lead or deal to track active opportunity';
        }
        if ($contact->activities->count() === 0) $recs[] = 'Log an activity to start engagement tracking';
        if (empty($recs)) $recs[] = 'Contact profile is well-maintained — schedule next touchpoint';
        return $recs;
    }

    private function buildDashboardInsights(): array
    {
        $tid = $this->tenantId();
        $insights = [];

        $openDeals    = Deal::where('tenant_id', $tid)->where('status','open')->count();
        $totalValue   = Deal::where('tenant_id', $tid)->where('status','open')->sum('value');
        $wonThisMonth = Deal::where('tenant_id', $tid)->where('status','won')->whereMonth('updated_at', now()->month)->count();
        $overdueTasks = Task::where('tenant_id', $tid)->where('status','!=','done')->where('due_date','<',now())->count();
        $stalled      = Lead::where('tenant_id', $tid)->whereNotIn('status',['won','lost','converted'])->where('updated_at','<',now()->subDays(14))->count();
        $newLeadsWeek = Lead::where('tenant_id', $tid)->where('created_at','>=',now()->subDays(7))->count();

        if ($overdueTasks > 0)  $insights[] = ['type'=>'warning','icon'=>'bi-exclamation-circle','title'=>"$overdueTasks Overdue Tasks",'text'=>"$overdueTasks task(s) are past due and may be blocking deal progression.",'action'=>route('tasks.index'),'action_text'=>'View Tasks'];
        if ($stalled > 0)       $insights[] = ['type'=>'danger', 'icon'=>'bi-hourglass-split','title'=>"$stalled Stalled Leads",'text'=>"$stalled lead(s) haven't had activity in 14+ days. Immediate follow-up recommended.",'action'=>route('leads.index'),'action_text'=>'View Leads'];
        if ($wonThisMonth > 0)  $insights[] = ['type'=>'success','icon'=>'bi-trophy','title'=>"$wonThisMonth Deals Won This Month",'text'=>"Great momentum! Keep engaging the remaining $openDeals open deals worth $" . number_format($totalValue),'action'=>route('deals.index'),'action_text'=>'View Deals'];
        if ($newLeadsWeek > 0)  $insights[] = ['type'=>'info',   'icon'=>'bi-lightning','title'=>"$newLeadsWeek New Leads This Week",'text'=>"Strong top-of-funnel activity. Assign and qualify these leads quickly for best conversion.",'action'=>route('leads.index'),'action_text'=>'View Leads'];
        if ($openDeals > 10)    $insights[] = ['type'=>'info',   'icon'=>'bi-bar-chart','title'=>"Pipeline Health: $openDeals Open Deals",'text'=>"Pipeline value of $" . number_format($totalValue) . " — review stage distribution for bottlenecks.",'action'=>route('deals.index'),'action_text'=>'View Pipeline'];

        if (empty($insights))   $insights[] = ['type'=>'success','icon'=>'bi-check-circle','title'=>'Pipeline Looks Healthy','text'=>'No immediate risks detected. Focus on nurturing leads and progressing deals.','action'=>route('dashboard'),'action_text'=>'Dashboard'];

        return $insights;
    }

    // ─── Email Generation ─────────────────────────────────────────────────────

    private function generateLeadEmail(Lead $lead): array
    {
        $name = $lead->contact?->first_name ?? 'there';
        $agent = Auth::user()->name;
        $company = config('app.name', 'Our Team');
        return [
            'subject' => "Following up on your interest — {$lead->title}",
            'body'    => "Hi {$name},\n\nThank you for your interest in our solutions. I wanted to personally follow up regarding {$lead->title} and see how we can best support your goals.\n\nBased on what you've shared, I believe we have a strong fit. I'd love to schedule a 20-minute call to explore this further.\n\nWould any of the following times work for you?\n- [Time Option 1]\n- [Time Option 2]\n- [Time Option 3]\n\nLooking forward to connecting.\n\nBest regards,\n{$agent}\n{$company}",
        ];
    }

    private function generateDealEmail(Deal $deal): array
    {
        $name  = $deal->contact?->first_name ?? 'there';
        $agent = Auth::user()->name;
        $value = '$' . number_format($deal->value);
        return [
            'subject' => "Next Steps — {$deal->title}",
            'body'    => "Hi {$name},\n\nI hope this message finds you well. I wanted to follow up on our recent discussions regarding {$deal->title} ({$value}).\n\nWe're excited about the potential to work together and want to ensure we address any outstanding questions you may have before moving forward.\n\nCould we schedule a brief call this week to align on next steps and timeline?\n\nLooking forward to your response.\n\nWarm regards,\n{$agent}",
        ];
    }

    private function generateContactEmail(Contact $contact): array
    {
        $name    = $contact->first_name;
        $agent   = Auth::user()->name;
        $orgName = $contact->company?->name ?? 'your organization';
        return [
            'subject' => "Checking in, {$name}",
            'body'    => "Hi {$name},\n\nI hope you're doing well. I wanted to touch base and see if there's anything we can help you with.\n\nWe've recently introduced some new capabilities that I think could be valuable for you and your team at {$orgName}.\n\nWould you be open to a quick 15-minute call this week?\n\nBest,\n{$agent}",
        ];
    }

    private function leadEmailSubject(string $type, Lead $lead): string
    {
        return match($type) {
            'follow_up'    => "Following up — {$lead->title}",
            'introduction' => "Introduction: How we can help with {$lead->title}",
            'proposal'     => "Proposal for {$lead->title}",
            'closing'      => "Moving forward with {$lead->title}",
            default        => "Regarding {$lead->title}",
        };
    }

    private function leadEmailBody(string $type, Lead $lead): string
    {
        $name = $lead->contact?->first_name ?? 'there';
        $agent = Auth::user()->name;
        return match($type) {
            'introduction' => "Hi {$name},\n\nI'm reaching out because we specialize in helping teams like yours achieve measurable results. I'd love to learn more about {$lead->title} and see if we'd be a great fit.\n\nWould you be available for a brief introductory call?\n\nBest,\n{$agent}",
            'proposal'     => "Hi {$name},\n\nFollowing our recent conversations, I'm pleased to share our tailored proposal for {$lead->title}.\n\nPlease find the key points attached. I'm available to walk you through the details at your convenience.\n\nLooking forward to your feedback.\n\nBest regards,\n{$agent}",
            'closing'      => "Hi {$name},\n\nI wanted to follow up one final time on {$lead->title}. We're genuinely excited about the opportunity to work with your team.\n\nIf the timing isn't right now, I completely understand — but I'd hate for us to miss the opportunity to create value for you. Could we schedule a final call to discuss?\n\nWarm regards,\n{$agent}",
            default        => "Hi {$name},\n\nJust checking in on {$lead->title}. Do you have any questions I can help address?\n\nBest,\n{$agent}",
        };
    }

    private function dealEmailSubject(string $type, Deal $deal): string
    {
        return match($type) {
            'follow_up'    => "Following up — {$deal->title}",
            'proposal'     => "Proposal: {$deal->title}",
            'closing'      => "Ready to move forward with {$deal->title}?",
            default        => "Update on {$deal->title}",
        };
    }

    private function dealEmailBody(string $type, Deal $deal): string
    {
        $name  = $deal->contact?->first_name ?? 'there';
        $agent = Auth::user()->name;
        $value = '$' . number_format($deal->value);
        return match($type) {
            'proposal' => "Hi {$name},\n\nThank you for your continued interest in {$deal->title}.\n\nAttached is our formal proposal outlining the scope, investment ({$value}), and expected outcomes. We're confident this aligns well with your objectives.\n\nPlease don't hesitate to reach out with questions.\n\nBest,\n{$agent}",
            'closing'  => "Hi {$name},\n\nWe're very excited about the prospect of partnering with you on {$deal->title}. I believe we've addressed all your questions and are fully aligned on the value we'll deliver.\n\nAre you ready to take the next step? I can have the contract ready within 24 hours.\n\nLooking forward to your confirmation.\n\nWarm regards,\n{$agent}",
            default    => "Hi {$name},\n\nJust wanted to check in on {$deal->title} and see if there's anything I can help clarify before we move forward.\n\nBest,\n{$agent}",
        };
    }

    private function contactEmailSubject(string $type, Contact $contact): string
    {
        return match($type) {
            'follow_up'    => "Checking in, {$contact->first_name}",
            'introduction' => "Introduction — {$contact->first_name}, let's connect",
            'proposal'     => "Special offer for {$contact->first_name}",
            default        => "Hello {$contact->first_name}",
        };
    }

    private function contactEmailBody(string $type, Contact $contact): string
    {
        $name    = $contact->first_name;
        $agent   = Auth::user()->name;
        $orgName = $contact->company?->name ?? 'your organization';
        return match($type) {
            'introduction' => "Hi {$name},\n\nI'd love to introduce myself and learn more about what you're working on at {$orgName}.\n\nWould you be open to a 15-minute intro call?\n\nBest,\n{$agent}",
            default        => "Hi {$name},\n\nHope you're well! I wanted to touch base and see if there's anything we can assist with.\n\nBest,\n{$agent}",
        };
    }
}
