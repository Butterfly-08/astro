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
    // Endpoint contract from Astro API EndPOints.xlsx
    // ----------------------------------------------------------------------
    // These routes intentionally define the API surface only. The referenced
    // EndpointController actions can be implemented independently.
    $endpointController = 'App\\Http\\Controllers\\Api\\EndpointController';

    Route::post('/home', [$endpointController, 'home']);

    Route::prefix('auth')->group(function () use ($endpointController) {
        Route::get('/me', [$endpointController, 'me'])->middleware('auth:sanctum');
        Route::post('/forgot-password', [$endpointController, 'forgotPassword']);
        Route::post('/reset-password', [$endpointController, 'resetPassword']);
    });

    Route::prefix('astrologers')->group(function () use ($endpointController) {
        Route::post('/', [$endpointController, 'astrologers']);
        Route::post('/{id}', [$endpointController, 'astrologer']);
        Route::post('/{id}/availability', [$endpointController, 'astrologerAvailability']);
        Route::post('/{id}/services', [$endpointController, 'astrologerServices']);
        Route::post('/{id}/reviews', [$endpointController, 'astrologerReviews']);
    });

    Route::post('/search/astrologers', [$endpointController, 'searchAstrologers']);

    Route::middleware('auth:sanctum')->group(function () use ($endpointController) {
        Route::get('/favorites', [$endpointController, 'favorites']);
        Route::post('/favorites/{astrologerId}', [$endpointController, 'addFavorite']);
        Route::delete('/favorites/{astrologerId}', [$endpointController, 'removeFavorite']);

        Route::post('/bookings/{id}/confirm', [$endpointController, 'confirmBooking']);
        Route::post('/bookings/{id}/reschedule', [$endpointController, 'rescheduleBooking']);
        Route::get('/bookings/{id}/status', [$endpointController, 'bookingStatus']);

        Route::post('/astrologer/bookings/{id}/accept', [$endpointController, 'acceptBooking']);
        Route::post('/astrologer/bookings/{id}/reject', [$endpointController, 'rejectBooking']);

        Route::post('/consultations/{id}/chat/start', [$endpointController, 'startChat']);
        Route::get('/consultations/{id}/messages', [$endpointController, 'messages']);
        Route::post('/consultations/{id}/messages', [$endpointController, 'sendMessage']);
        Route::post('/consultations/{id}/chat/end', [$endpointController, 'endChat']);
        Route::get('/consultations/{id}/chat/status', [$endpointController, 'chatStatus']);
        Route::post('/consultations/{id}/call/request', [$endpointController, 'requestCall']);
        Route::post('/consultations/{id}/video/request', [$endpointController, 'requestVideo']);
        Route::post('/consultations/{id}/call/accept', [$endpointController, 'acceptCall']);
        Route::post('/consultations/{id}/video/accept', [$endpointController, 'acceptVideo']);
        Route::post('/consultations/{id}/call/reject', [$endpointController, 'rejectCall']);
        Route::post('/consultations/{id}/call/token', [$endpointController, 'callToken']);
        Route::post('/consultations/{id}/call/join', [$endpointController, 'joinCall']);
        Route::post('/consultations/{id}/call/leave', [$endpointController, 'leaveCall']);
        Route::post('/consultations/{id}/call/end', [$endpointController, 'endCall']);
        Route::get('/consultations/{id}/call/status', [$endpointController, 'callStatus']);

        Route::get('/wallet', [$endpointController, 'wallet']);
        Route::get('/wallet/transactions', [$endpointController, 'walletTransactions']);
        Route::post('/wallet/topups', [$endpointController, 'createTopup']);
        Route::get('/wallet/topups/{id}', [$endpointController, 'topupStatus']);
        Route::post('/payments/order', [$endpointController, 'paymentOrder']);
        Route::post('/payments/verify', [$endpointController, 'verifyPayment']);
        Route::get('/payments/{id}', [$endpointController, 'paymentStatus']);
        Route::post('/payments/refund', [$endpointController, 'refundPayment']);
        Route::get('/invoices/{id}', [$endpointController, 'invoice']);
        Route::get('/referrals/invites', [$endpointController, 'referralInvites']);

        Route::get('/wishlist', [$endpointController, 'wishlist']);
        Route::post('/wishlist/items', [$endpointController, 'addWishlistItem']);
        Route::delete('/wishlist/items/{id}', [$endpointController, 'removeWishlistItem']);
        Route::get('/addresses', [$endpointController, 'addresses']);
        Route::post('/addresses', [$endpointController, 'createAddress']);
        Route::put('/addresses/{id}', [$endpointController, 'updateAddress']);
        Route::delete('/addresses/{id}', [$endpointController, 'deleteAddress']);
        Route::post('/orders/{id}/cancel', [$endpointController, 'cancelOrder']);
        Route::get('/notifications', [$endpointController, 'notifications']);
    });

    Route::get('/store/categories', [$endpointController, 'storeCategories']);
    Route::get('/store/products', [$endpointController, 'storeProducts']);
    Route::get('/store/products/{id}', [$endpointController, 'storeProduct']);

    Route::post('/webhooks/video-provider', [$endpointController, 'videoWebhook']);
    Route::post('/webhooks/payment-provider', [$endpointController, 'paymentWebhook']);

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
