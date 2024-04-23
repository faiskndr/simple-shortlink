<?php

use App\Http\Controllers\Api\ActivityController;
use App\Http\Controllers\Api\Admin\ActivityController as AdminActivityController;
use App\Http\Controllers\Api\Admin\AdminContoller;
use App\Http\Controllers\Api\Admin\DashboardController;
use App\Http\Controllers\Api\Admin\User\UserController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MicrositeController;
use App\Http\Controllers\Api\OverviewController;
use App\Http\Controllers\Api\ShortlinkController;
use App\Http\Middleware\LogActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('auth/user', function (Request $request) {
//     return $request->user();
// });

Route::post('auth/register',[AuthController::class,'register']);
Route::post('auth/login',[AuthController::class,'login'])->middleware(LogActivity::class);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('auth/logout', [AuthController::class, 'logout'])->middleware(LogActivity::class);
    Route::get('auth/user',[AuthController::class,'currentUser']);
    
    Route::post('shortlink',[ShortlinkController::class,'generateShortlink']);
    Route::put('shortlink/{shortlink:key}',[ShortlinkController::class,'editShortlink']);
    Route::delete('shortlink/{shortlink}',[ShortlinkController::class,'removeShortlink']);
    Route::get('shortlinks',[ShortlinkController::class,'index']);
    Route::get('shortlink/{shortlink:key}',[ShortlinkController::class,'showShortlink']);
    Route::get('shortlink/{shortlink:key}/clicked',[ShortlinkController::class,'shortlinkClick']);

    Route::prefix('overview')->group(function(){
        Route::get('',[OverviewController::class,'overview']);
        Route::get('chart',[OverviewController::class,'overview_chart']);
    });


    Route::prefix('microsite')->group(function(){
        Route::get('',[MicrositeController::class,'index']);
        Route::get('{key}',[MicrositeController::class,'edit']);
        Route::post('',[MicrositeController::class,'store']);
        Route::post('/{key}',[MicrositeController::class,'updateMicrosite']);
    });

    Route::post('qrcode',[ShortlinkController::class,'qrcode']);

    Route::middleware('admin')->prefix('admin')->group(function(){
        Route::get('dashboard',[DashboardController::class,'index']);

        Route::prefix('dashboard')->group(function(){
            Route::get('shortlinks',[DashboardController::class,'shortlinks']);
        });

        Route::get('users',[UserController::class,'index']);
        Route::get('user/{username}',[UserController::class,'show']);
        Route::put('user',[UserController::class,'verifyEmail']);
        Route::get('users/export',[UserController::class,'export']);

        Route::get('log-activities',[AdminActivityController::class,'index']);
        Route::get('log-activities/export',[AdminActivityController::class,'export']);

        Route::get('admins',[AdminContoller::class,'index']);
        Route::post('admin',[AdminContoller::class,'store']);
        Route::delete('admin/{admin}',[AdminContoller::class,'removeAdmin']);
    });


    Route::get('activities',[ActivityController::class,'index']);
});

Route::get('/{id}',[ShortlinkController::class,'redirectTo'])->middleware(['shortlink.token','shortlink.log']);

