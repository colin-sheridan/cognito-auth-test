<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;

class LoginController extends Controller
{
    /**
     * Handles the follow-up from a Cognito login.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function samlLogin(Request $request)
    {
        // Get code out of query string
        $authCode = $request->input('code');

        // Send code to /oauth2/token
        $data = $this->exchangeCodeForTokens($authCode, env('COGNITO_APP_ID'), env('COGNITO_REDIRECT_URL'));

        Log::Debug('exchangeCodeForTokens Response: ');
        Log::Debug($data);

        if (!array_key_exists('error', $data)) {

            // Put cognito_access_code in the session
            $request->session()->put(
                'cognito_access_token',
                $data['access_token']
            );

            // Get some info about them & make sure they're in the db
            $detailsData = $this->getUserDetails($data['access_token']);
            // Put the refresh_token in the database with the user info
            Log::Debug('$detailsData incoming:');
            Log::Debug($detailsData);
            Log::Debug('$data is:');
            Log::Debug($data);
            Log::Debug('$refresh_token is: ');
            Log::Debug($data['refresh_token']);
            $user = User::updateOrCreate(
                ['email' => $detailsData['username']],
                [
                    'email' => $detailsData['username'],
                    'display_name' => 'Colin Sheridan',
                    'username' => 'csheridan',
                    'employee_id' => '123456',
                    'given_name' => 'Colin',
                    'family_name' => 'Sheridan',
                    'refresh_token' => $data['refresh_token'],
                ]
            );

            // Log them in
            Auth::login($user);

            // Get desired_url out of state param and redirect there
            $desiredUrl = $this->desiredUrlExtract($request->input('state'));
            return redirect($desiredUrl);
        } else {
            // invalid_grant is basically the only reasonable error code
            // https://docs.aws.amazon.com/cognito/latest/developerguide/token-endpoint.html#post-token-negative
            if ($data['error'] === "invalid_grant") {
                $attemptedUrl = $this->desiredUrlExtract($request->input('state'));
                return route('login', ["desired_url" => urlencode($attemptedUrl)]);
            } else {
                abort(500, "Something went wrong logging you in.");
            }
        }
    }

    /**
     * Takes the authentication code provided by Cognito
     * and returns a access and refresh token from Cognito.
     *
     * @param String $authCode
     * @param String $appId Cognito App ID
     * @param String $redirectUrl Cognito App redirect_url
     * @return Array An array containing access_token and refresh_token
     */
    public function exchangeCodeForTokens($authCode, $appId, $redirectUrl)
    {
        $client = new Client([
            'base_uri' => env('COGNITO_API_BASE_URI'),
        ]);

        $response = $client->post('/oauth2/token', [
            'headers' => ["Content-Type" => "application/x-www-form-urlencoded"],
            'form_params' => [
                'grant_type' => 'authorization_code',
                'client_id' => $appId,
                'code' => $authCode,
                'redirect_uri' => $redirectUrl
            ]
        ]);

        return json_decode($response->getBody(), true);
    }

    /**
     * Takes a cognito access token and returns
     * information about the user it is assigned to.
     *
     * @param String $access_token
     * @return Array An array containing user details such as email,
     * given_name, family_name.
     */
    public function getUserDetails($access_token)
    {
        $client = new Client([
            'base_uri' => env('COGNITO_API_BASE_URI'),
        ]);

        $response = $client->get('/oauth2/userInfo', [
            'headers' => ["Authorization" => "Bearer " . $access_token],
        ]);

        return json_decode($response->getBody(), true);
    }

    /**
     * Extracts the desired_url from the state parameter
     * following a Cognito login.
     *
     * @param String $stateParam
     * @return String $desiredUrl
     */
    public function desiredUrlExtract($stateParam)
    {
        $decoded = urldecode($stateParam);

        $explodedArray = explode("desired_url=", $decoded);

        if (count($explodedArray) !== 2) {
            return URL::to('/');
        } else {
            $desiredUrl = $explodedArray[1];

            return $desiredUrl;
        }
    }
}
