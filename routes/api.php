<?php

use App\Http\Controllers\Api\AstrologerApiController;
use App\Http\Controllers\Api\ReferralApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — AstroReferral System
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {
    // Referral verification & link resolution (Public)
    Route::get('/referrals/validate/{code}', [ReferralApiController::class, 'validateCode']);
    Route::post('/referrals/track', [ReferralApiController::class, 'trackClick']);

    // Astrologer API authentication
    Route::post('/astrologer/login', [AstrologerApiController::class, 'login']);

    // Protected Astrologer API endpoints
    // Supports sanctum guard when installed, or fallback to session/web
    $authMiddleware = class_exists(\Laravel\Sanctum\Sanctum::class) ? 'auth:sanctum' : 'auth:web';

    Route::middleware([$authMiddleware])->prefix('astrologer')->group(function () {
        Route::get('/profile', [AstrologerApiController::class, 'profile']);
        Route::get('/dashboard', [AstrologerApiController::class, 'dashboard']);
        Route::get('/wallet', [AstrologerApiController::class, 'wallet']);
        Route::get('/commissions', [AstrologerApiController::class, 'commissions']);
        Route::get('/withdrawals', [AstrologerApiController::class, 'withdrawals']);
        Route::post('/withdrawals', [AstrologerApiController::class, 'requestWithdrawal']);
        Route::get('/referral-links', [AstrologerApiController::class, 'referralLinks']);
        Route::post('/logout', [AstrologerApiController::class, 'logout']);
    });
});
