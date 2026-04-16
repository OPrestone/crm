<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class OnboardingController extends Controller
{
    public function complete(): JsonResponse
    {
        $user = Auth::user();
        if ($user && is_null($user->onboarding_completed_at)) {
            $user->onboarding_completed_at = now();
            $user->save();
        }
        return response()->json(['ok' => true]);
    }

    public function restart(): JsonResponse
    {
        $user = Auth::user();
        if ($user) {
            $user->onboarding_completed_at = null;
            $user->save();
        }
        return response()->json(['ok' => true, 'redirect' => route('dashboard')]);
    }
}
