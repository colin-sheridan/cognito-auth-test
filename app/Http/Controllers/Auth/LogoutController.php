<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class LogoutController extends Controller
{
    /**
    * Destroy an authenticated session.
    */

    public function destroy(Request $request): RedirectResponse
    {
        $user = $request->user();
        $user->update(['refresh_token' => '',]);

        Session::flush();

        Auth::logout();

        $cognitoURL = env('COGNITO_API_BASE_URI');
        $appId = env('COGNITO_APP_ID');
        return redirect (html_entity_decode($cognitoURL . '/logout?' . 'client_id=' . $appId . '&logout_uri=http://localhost'));
    }
}
