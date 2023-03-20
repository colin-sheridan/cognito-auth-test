<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LoginController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::middleware(['auth', 'cognito'])->group(function () {
    Route::get('/', function () {
        return view('welcome');
    })->name('welcome');

    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
});

Route::get('/saml/login', [LoginController::class, 'samlLogin']);

Route::redirect('/login', 'https://willametteuniversity.auth.us-west-2.amazoncognito.com/oauth2/authorize?client_id=6d00flmvp6qtl71q9hgaboq461&response_type=code&scope=aws.cognito.signin.user.admin+email+openid+profile&redirect_uri=http%3A%2F%2Flocalhost%2Fsaml%2Flogin')
    ->name('login');
