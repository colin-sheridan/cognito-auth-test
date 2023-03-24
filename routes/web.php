<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;

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

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::middleware(['auth', 'cognito'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');

    Route::get('/logout', [LogoutController::class, 'destroy'])->name('logout');
});

Route::get('/saml/login', [LoginController::class, 'samlLogin']);

Route::redirect('/login', env('COGNITO_HOSTED_UI_URL'))
    ->name('login');

