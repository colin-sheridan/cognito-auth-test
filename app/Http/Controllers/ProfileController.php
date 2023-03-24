<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Log;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function show(Request $request): Response
    {
        Log::Debug('******PROFILE_BLOCK*****');
        $user = $request->user();
        Log::Debug($user);
        Log::Debug('*****SESSION***');
        Log::Debug($request->session()->all());
        Log::Debug('******END_PROFILE_BLOCK*****');

        return Inertia::render('Profile/Index', [
            'user' => $request->user(),
            'session' => $request->session()->get('cognito_access_token'),
        ]);
    }
}
