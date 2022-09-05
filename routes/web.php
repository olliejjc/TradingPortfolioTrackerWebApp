<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!

| Every route that requires a service for testing is implemented here
|
*/

Route::get('/{any}', function () {     
    return view('welcome');})
    ->where('any', '.*');

// Route::post('/login', [AuthenticatedSessionController::class, 'store'])
//     ->middleware('guest')->name('login');

Route::post('/login', [AuthenticatedSessionController::class, 'store']);

Route::post("/logout", [AuthenticatedSessionController::class, 'destroy'])->middleware('auth')->name("logout");