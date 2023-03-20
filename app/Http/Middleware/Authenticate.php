<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    // Get the path the user should be redirected to when they are not authenticated.

    // @param  \Illuminate\Http\Request  $request
    // @return string|null

   protected function redirectTo($request)
   {

       $attemptedUrl = $request->fullUrl();

       if (!$request->expectsJson()) {
           return route('login', ["desired_url" => urlencode($attemptedUrl)]);
       }
   }

}
