<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LivePortfolioController;
use App\Http\Controllers\RiskCalculatorController;
use App\Http\Controllers\ScreenshotsController;
use App\Http\Controllers\TradesController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware(['auth', 'role:Admin,User'])->group(function () {
//     Route::get('/', 'HomeController@index')->name('home');
// });

Route::post('/register', [UserController::class, 'store']);

Route::middleware(['auth', 'role:Admin,User'])->group(function () {
    Route::get('/home', [HomeController::class, 'generateListOfTradeYears']);
});
    
Route::middleware(['auth', 'role:Admin,User'])->group(function () {
    Route::get('/riskcalculator', [UserController::class, 'showRiskCalculatorSettings']);
});

Route::middleware(['auth', 'role:Admin,User'])->group(function () {
    Route::get('/settings', [UserController::class, 'showUserSettings']);
});

Route::middleware(['auth', 'role:Admin,User'])->group(function () {
    Route::put('/update', [UserController::class, 'update']);
});
    
//vue api routerlink /newtrades
// Route::middleware(['auth', 'role:Admin,User'])->group(function () {
//     Route::get('/newtrades', function() {
//         return view('newtrades');
//     });
// });

Route::middleware(['auth', 'role:Admin,User'])->group(function () {
    Route::get('/trades', [TradesController::class, 'index']);
});

Route::middleware(['auth', 'role:Admin,User'])->group(function () {
    Route::get('/tradehistory', [TradesController::class, 'generateTradeHistory']);
});

Route::middleware(['auth', 'role:Admin,User'])->group(function () {
    Route::post('/getTradeDataForSelectedDate', [TradesController::class, 'getTradeDataForSelectedDate']);
});

Route::middleware(['auth', 'role:Admin,User'])->group(function () {
    Route::get('/calculateMonthlyTotals', [TradesController::class, 'calculateMonthlyBalanceAndProfitLoss']);
});

Route::middleware(['auth', 'role:Admin,User'])->group(function () {
    Route::get('/tradeYearsWithTrades', [TradesController::class, 'getListOfTradeYears']);
});

Route::middleware(['auth', 'role:Admin,User'])->group(function () {
    Route::post('/newtrades', [TradesController::class, 'addNewTrade']);
});

Route::middleware(['auth', 'role:Admin,User'])->group(function () {
    Route::post('/closetrades', [TradesController::class, 'closeTrade']);
});

Route::middleware(['auth', 'role:Admin,User'])->group(function () {
    Route::post('/generateTradeChartDataSets', [TradesController::class, 'generateTradeChartDataSets']);
});

Route::middleware(['auth', 'role:Admin,User'])->group(function () {
    Route::delete('/deletetrades/{id}', [TradesController::class, 'delete']);
});

Route::middleware(['auth', 'role:Admin,User'])->group(function () {
    Route::post('/calculate', [RiskCalculatorController::class, 'calculate']);
});

Route::middleware(['auth', 'role:Admin,User'])->group(function () {
    Route::get('/tradescreenshots/{id}', [ScreenshotsController::class, 'getScreenshotsByTrade']);
});

Route::middleware(['auth', 'role:Admin,User'])->group(function () {
    Route::post('/newscreenshots', [ScreenshotsController::class, 'store']);
});

Route::middleware(['auth', 'role:Admin,User'])->group(function () {
    Route::delete('/deletescreenshot/{activeScreenshotPath}', [ScreenshotsController::class, 'delete']);
});

Route::middleware(['auth', 'role:Admin,User'])->group(function () {
    Route::get('/liveportfolio', [LivePortfolioController::class, 'index']);
});
