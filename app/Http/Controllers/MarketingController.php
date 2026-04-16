<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MarketingController extends Controller
{
    public function pricing()
    {
        return view('marketing.pricing');
    }

    public function howTo()
    {
        return view('marketing.how-to');
    }

    public function contactSales(Request $request)
    {
        $data = $request->validate([
            'name'      => 'required|string|max:200',
            'email'     => 'required|email|max:200',
            'company'   => 'required|string|max:200',
            'phone'     => 'nullable|string|max:30',
            'team_size' => 'required|in:1-10,11-50,51-200,201-500,500+',
            'industry'  => 'nullable|string|max:100',
            'message'   => 'nullable|string|max:2000',
        ]);

        // Log the inquiry (in production this would email the sales team)
        Log::info('Sales Enquiry Received', $data);

        return redirect()
            ->route('pricing')
            ->with('sales_success', true)
            ->with('sales_name', $data['name'])
            ->with('sales_email', $data['email']);
    }
}
