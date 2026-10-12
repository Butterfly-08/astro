<?php

use App\Http\Controllers\Api\AstrologerApiController;
use App\Http\Controllers\Api\AstrologerDirectoryApiController;
use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\BookingApiController;
use App\Http\Controllers\Api\CartApiController;
use App\Http\Controllers\Api\ContentApiController;
use App\Http\Controllers\Api\OrderApiController;
use App\Http\Controllers\Api\ProductApiController;
use App\Http\Controllers\Api\ReferralApiController;
use App\Http\Controllers\Api\ServiceApiController;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| API Routes — AstroVani Platform API
|--------------------------------------------------------------------------
|
| Versioned RESTful API routes providing endpoints for:
| 1. Referral attribution & click tracking
| 2. Customer authentication & profile management
| 3. Astrologer partner authentication, wallet, commission & withdrawals
| 4. Public Astrologer directory, profiles, reviews & slot availability
| 5. Public Astrology consultation services catalog
| 6. Public E-commerce shop catalog, categories & products
| 7. Public Horoscopes & blog content
| 8. Shopping cart & coupon management
| 9. Protected customer bookings & consultation appointments
| 10. Protected customer checkout & orders history
|
*/

Route::prefix('v1')->group(function () {

    // ----------------------------------------------------------------------
    // 1. Referral & Attribution Tracking (Public)
    // ----------------------------------------------------------------------
    Route::prefix('referrals')->group(function () {
        Route::get('/validate/{code}', [ReferralApiController::class, 'validateCode']);
        Route::post('/track', [ReferralApiController::class, 'trackClick']);
    });

    // ----------------------------------------------------------------------
    // 2. Customer Authentication (Public & Protected)
    // ----------------------------------------------------------------------
    Route::prefix('auth')->group(function () {
        Route::post('/register', [AuthApiController::class, 'register']);
        Route::post('/login', [AuthApiController::class, 'login']);

        Route::middleware(['auth:sanctum'])->group(function () {
            Route::get('/user', [AuthApiController::class, 'user']);
            Route::put('/user', [AuthApiController::class, 'updateProfile']);
            Route::post('/logout', [AuthApiController::class, 'logout']);
        });
    });

    // ----------------------------------------------------------------------
    // 3. Astrologer Partner Authentication & Portal
    // ----------------------------------------------------------------------
    Route::post('/astrologer/login', [AstrologerApiController::class, 'login']);

    Route::middleware('auth:sanctum')->prefix('astrologer')->group(function () {
        Route::get('/profile', [AstrologerApiController::class, 'profile']);
        Route::get('/dashboard', [AstrologerApiController::class, 'dashboard']);
        Route::get('/wallet', [AstrologerApiController::class, 'wallet']);
        Route::get('/commissions', [AstrologerApiController::class, 'commissions']);
        Route::get('/withdrawals', [AstrologerApiController::class, 'withdrawals']);
        Route::post('/withdrawals', [AstrologerApiController::class, 'requestWithdrawal']);
        Route::get('/referral-links', [AstrologerApiController::class, 'referralLinks']);
        Route::post('/logout', [AstrologerApiController::class, 'logout']);
    });

    // ----------------------------------------------------------------------
    // 4. Astrologers Public Directory & Slots
    // ----------------------------------------------------------------------
    Route::prefix('astrologers')->group(function () {
        Route::get('/', [AstrologerDirectoryApiController::class, 'index']);
        Route::get('/featured', [AstrologerDirectoryApiController::class, 'featured']);
        Route::get('/{identifier}', [AstrologerDirectoryApiController::class, 'show']);
        Route::get('/{identifier}/reviews', [AstrologerDirectoryApiController::class, 'reviews']);
        Route::get('/{identifier}/slots', [BookingApiController::class, 'availableSlots']);
    });

    // ----------------------------------------------------------------------
    // 5. Services Public Catalog
    // ----------------------------------------------------------------------
    Route::prefix('services')->group(function () {
        Route::get('/', [ServiceApiController::class, 'index']);
        Route::get('/{identifier}', [ServiceApiController::class, 'show']);
    });

    // ----------------------------------------------------------------------
    // 6. E-Commerce Catalog (Categories & Products)
    // ----------------------------------------------------------------------
    Route::get('/categories', [ProductApiController::class, 'categories']);

    Route::prefix('products')->group(function () {
        Route::get('/', [ProductApiController::class, 'index']);
        Route::get('/featured', [ProductApiController::class, 'featured']);
        Route::get('/{identifier}', [ProductApiController::class, 'show']);
    });

    // ----------------------------------------------------------------------
    // 7. Horoscopes & Blogs Content
    // ----------------------------------------------------------------------
    Route::prefix('horoscopes')->group(function () {
        Route::get('/', [ContentApiController::class, 'horoscopes']);
        Route::get('/{sign}', [ContentApiController::class, 'horoscopeBySign']);
    });

    Route::prefix('blogs')->group(function () {
        Route::get('/', [ContentApiController::class, 'blogs']);
        Route::get('/{identifier}', [ContentApiController::class, 'showBlog']);
    });

    // ----------------------------------------------------------------------
    // 8. Shopping Cart & Coupons
    // ----------------------------------------------------------------------
    Route::prefix('cart')->group(function () {
        Route::get('/', [CartApiController::class, 'index']);
        Route::post('/items', [CartApiController::class, 'addItem']);
        Route::put('/items/{itemId}', [CartApiController::class, 'updateItem']);
        Route::delete('/items/{itemId}', [CartApiController::class, 'removeItem']);
        Route::post('/coupon', [CartApiController::class, 'applyCoupon']);
        Route::delete('/coupon', [CartApiController::class, 'removeCoupon']);
        Route::delete('/clear', [CartApiController::class, 'clear']);
    });

    // ----------------------------------------------------------------------
    // 9. Protected Customer Appointments & Bookings
    // ----------------------------------------------------------------------
    Route::middleware(['auth:sanctum'])->prefix('bookings')->group(function () {
        Route::get('/', [BookingApiController::class, 'index']);
        Route::post('/', [BookingApiController::class, 'store']);
        Route::get('/{id}', [BookingApiController::class, 'show']);
        Route::post('/{id}/cancel', [BookingApiController::class, 'cancel']);
        Route::post('/{id}/reviews', [BookingApiController::class, 'storeReview']);
    });

    // ----------------------------------------------------------------------
    // 10. Protected Customer Checkout & Orders
    // ----------------------------------------------------------------------
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::post('/checkout', [OrderApiController::class, 'checkout']);

        Route::prefix('orders')->group(function () {
            Route::get('/', [OrderApiController::class, 'index']);
            Route::get('/{identifier}', [OrderApiController::class, 'show']);
        });
    });
});
