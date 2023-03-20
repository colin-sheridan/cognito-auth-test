<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Aws\CognitoIdentityProvider\CognitoIdentityProviderClient;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;

class EnsureCognitoAuthenticates
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $attemptedUrl = $request->fullUrl();
        Log::debug($attemptedUrl);

        // If no access_token in session, redirect to sign-in
        if (!$request->session()->has('cognito_access_token')) {
            Log::debug('does not have token!');
            return route('login', ["desired_url" => urlencode($attemptedUrl)]);
        } else {
            try {
                // Now there is a token, lets verify it
                $client = new CognitoIdentityProviderClient([
                    'region' => env("COGNITO_APP_REGION"),
                    'version' => '2016-04-18'
                ]);

                // Check access token is still valid
                Log::debug('checking token validity!');
                $client->getUser([
                    'AccessToken' => $request->session()->get('cognito_access_token')
                ]);
                // If no error, then we're authenticated, continue
                return $next($request);
            } catch (\Aws\CognitoIdentityProvider\Exception\CognitoIdentityProviderException $e) {
                // If problem, then it's invalid or its expired
                // See if the Laravel session can save us
                if (Auth::check()) {
                    Log::debug('checking auth!');
                    // Try to refresh it with a token from the DB
                    $user = Auth::user();
                    try {
                        $this->exchangeRefreshForAccess($user->refresh_token, $attemptedUrl);
                        // If no error, then we're authenticated, continue
                        Log::debug('Authenticated!');
                        return $next($request);
                    } catch (\Aws\CognitoIdentityProvider\Exception\CognitoIdentityProviderException $e) {
                        // If refreshing doesn't work, refresh token is out of time too.
                        Log::debug('token expired!');
                        $user->refresh_token = "";
                        $user->save();
                    }
                    // If we get this far, nothing has worked. Redirect to the login screen
                    Log::debug('returning route!');
                    return route('login', ["desired_url" => urlencode($attemptedUrl)]);
                } else {
                    // Laravel session did not save us. Redirect to login.
                    Log::debug('no valid session!');
                    return route('login', ["desired_url" => urlencode($attemptedUrl)]);
                }
            }
        }
    }

    /**
     * Exchanges refresh_token for a fresh
     * access_token
     *
     * @param String $refresh_token
     * @param String $attemptedUrl
     * @return String $access_token
     */
    public function exchangeRefreshForAccess($refresh_token, $attemptedUrl)
    {
        Log::debug("Exchanging refresh for access");
        $client = new Client([
            'base_uri' => env('COGNITO_API_BASE_URI'),
        ]);
        $response = $client->post('/oauth2/token', [
            'headers' => ["Content-Type" => "application/x-www-form-urlencoded"],
            'form_params' => [
                'grant_type' => 'refresh_code',
                'client_id' => env('COGNITO_APP_ID'),
                'refresh_token' => $refresh_token
            ]
        ]);

        $data = json_decode($response->getBody(), true);

        if (!array_key_exists('error', $data)) {
            Log::debug('access token: ');
            Log::debug($data['access_token']);
            return $data['access_token'];
        } else {
            return route('login', ["desired_url" => urlencode($attemptedUrl)]);
        }
    }
}
