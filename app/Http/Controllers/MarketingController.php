<?php

namespace App\Http\Controllers;

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
}
