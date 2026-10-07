<?php

use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AstrologerController as AdminAstrologerController;
use App\Http\Controllers\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\ServiceController as AdminServiceController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\AstrologerController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\User\BookingController as UserBookingController;
use App\Http\Controllers\User\DashboardController as UserDashboardController;
use App\Http\Controllers\User\OrderController as UserOrderController;
use App\Http\Controllers\User\WishlistController as UserWishlistController;
use App\Http\Controllers\Admin\CommissionController as AdminCommissionController;
use App\Http\Controllers\Admin\WithdrawalController as AdminWithdrawalController;
use App\Http\Controllers\Admin\WalletController as AdminWalletController;
use App\Http\Controllers\Admin\SettingsController as AdminSettingsController;
use App\Http\Controllers\Admin\ReferralController as AdminReferralController;
use App\Http\Controllers\ReferralLandingController;
use App\Http\Controllers\Astrologer\AstrologerAuthController;
use App\Http\Controllers\Astrologer\AstrologerDashboardController;
use App\Http\Controllers\Astrologer\AstrologerReferralController;
use App\Http\Controllers\Astrologer\AstrologerCommissionController;
use App\Http\Controllers\Astrologer\AstrologerWalletController;
use App\Http\Controllers\Astrologer\AstrologerWithdrawalController;
use App\Http\Controllers\Astrologer\AstrologerAnalyticsController;
use App\Http\Controllers\Astrologer\AstrologerProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');


/*
|--------------------------------------------------------------------------
| Public Astrologer Listing & Profiles
|--------------------------------------------------------------------------
*/

Route::prefix('astrologers')->name('astrologers.')->group(function () {
    Route::get('/', [AstrologerController::class, 'index'])->name('index');
    Route::get('/{slug}', [AstrologerController::class, 'show'])->name('show');
});

/*
|--------------------------------------------------------------------------
| Public Services
|--------------------------------------------------------------------------
*/

Route::prefix('services')->name('services.')->group(function () {
    Route::get('/', [ServiceController::class, 'index'])->name('index');
    Route::get('/{slug}', [ServiceController::class, 'show'])->name('show');
});

/*
|--------------------------------------------------------------------------
| Public Astrology E-Commerce Shop
|--------------------------------------------------------------------------
*/

Route::prefix('shop')->name('shop.')->group(function () {

    // Product listing
    Route::get('/', [ShopController::class, 'index'])->name('index');

    // Product categories
    Route::get('/category/{slug}', [ShopController::class, 'category'])
        ->name('category');

    // Product details
    Route::get('/product/{slug}', [ShopController::class, 'show'])
        ->name('product.show');
});


/*
|--------------------------------------------------------------------------
| Shopping Cart
|--------------------------------------------------------------------------
*/

Route::prefix('cart')->name('cart.')->group(function () {

    Route::get('/', [CartController::class, 'index'])
        ->name('index');

    Route::post('/add/{productId}', [CartController::class, 'add'])
        ->name('add');

    Route::patch('/update/{cartItemId}', [CartController::class, 'update'])
        ->name('update');

    Route::delete('/remove/{cartItemId}', [CartController::class, 'remove'])
        ->name('remove');

    Route::post('/coupon/apply', [CartController::class, 'applyCoupon'])
        ->name('coupon.apply');

    Route::delete('/coupon/remove', [CartController::class, 'removeCoupon'])
        ->name('coupon.remove');
});


/*
|--------------------------------------------------------------------------
| Checkout
|--------------------------------------------------------------------------
*/

Route::prefix('checkout')
    ->name('checkout.')
    ->middleware('auth:web')
    ->group(function () {

        Route::get('/', [CheckoutController::class, 'index'])
            ->name('index');

        Route::post('/place-order', [CheckoutController::class, 'placeOrder'])
            ->name('place-order');

        Route::get('/confirmation/{orderNumber}', [CheckoutController::class, 'confirmation'])
            ->name('confirmation');
    });


/*
|--------------------------------------------------------------------------
| Appointment Booking Routes
|--------------------------------------------------------------------------
*/

Route::prefix('booking')->name('bookings.')->group(function () {

    Route::get('/{astrologer:slug}', [BookingController::class, 'create'])
        ->name('create');

    Route::get('/{astrologer:slug}/slots', [BookingController::class, 'getAvailableSlots'])
        ->name('slots');

    Route::post('/', [BookingController::class, 'store'])
        ->name('store')
        ->middleware('auth:web');

    Route::get('/confirmation/{bookingNumber}', [BookingController::class, 'confirmation'])
        ->name('confirmation')
        ->middleware('auth:web');
});


/*
|--------------------------------------------------------------------------
| Customer Authentication Routes
|--------------------------------------------------------------------------
*/

Route::middleware('guest:web')->group(function () {

    Route::get('/login', [AuthController::class, 'showLoginForm'])
        ->name('login');

    Route::post('/login', [AuthController::class, 'login'])
        ->name('login.submit');

    Route::get('/register', [AuthController::class, 'showRegisterForm'])
        ->name('register');

    Route::post('/register', [AuthController::class, 'register'])
        ->name('register.submit');
});


/*
|--------------------------------------------------------------------------
| Customer Authenticated Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:web')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('logout');

    Route::get('/dashboard', [UserDashboardController::class, 'index'])
        ->name('user.dashboard');


    /*
    |--------------------------------------------------------------------------
    | Customer Bookings Management
    |--------------------------------------------------------------------------
    */

    Route::prefix('dashboard/bookings')
        ->name('user.bookings.')
        ->group(function () {

            Route::get('/', [UserBookingController::class, 'index'])
                ->name('index');

            Route::get('/{booking}', [UserBookingController::class, 'show'])
                ->name('show');

            Route::post('/{booking}/cancel', [UserBookingController::class, 'cancel'])
                ->name('cancel');

            Route::post('/{booking}/review', [UserBookingController::class, 'review'])
                ->name('review');
        });


    /*
    |--------------------------------------------------------------------------
    | Customer Orders Management
    |--------------------------------------------------------------------------
    */

    Route::prefix('dashboard/orders')
        ->name('user.orders.')
        ->group(function () {

            Route::get('/', [UserOrderController::class, 'index'])
                ->name('index');

            Route::get('/{order}', [UserOrderController::class, 'show'])
                ->name('show');

            Route::post('/{order}/cancel', [UserOrderController::class, 'cancel'])
                ->name('cancel');
        });

    Route::prefix('dashboard/wishlist')
        ->name('user.wishlist.')
        ->group(function () {

            Route::get('/', [UserWishlistController::class, 'index'])
                ->name('index');

            Route::post('/toggle/{productId}', [UserWishlistController::class, 'toggle'])
                ->name('toggle');

            Route::delete('/remove/{wishlistId}', [UserWishlistController::class, 'remove'])
                ->name('remove');

            Route::delete('/clear', [UserWishlistController::class, 'clear'])
                ->name('clear');
        });
});


/*
|--------------------------------------------------------------------------
| Administrator Authentication & Management Routes
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->group(function () {


    /*
    |--------------------------------------------------------------------------
    | Admin Guest Routes
    |--------------------------------------------------------------------------
    */

    Route::middleware('admin.guest')->group(function () {

        Route::get('/login', [AdminAuthController::class, 'showLoginForm'])
            ->name('admin.login');

        Route::post('/login', [AdminAuthController::class, 'login'])
            ->name('admin.login.submit');
    });


    /*
    |--------------------------------------------------------------------------
    | Admin Authenticated Routes
    |--------------------------------------------------------------------------
    */

    Route::middleware('admin')->group(function () {

        Route::post('/logout', [AdminAuthController::class, 'logout'])
            ->name('admin.logout');

        Route::get('/dashboard', [AdminDashboardController::class, 'index'])
            ->name('admin.dashboard');


        /*
        |--------------------------------------------------------------------------
        | Users
        |--------------------------------------------------------------------------
        */

        // -----------------------------------------------------------------------
        // Booking Management (Phase 3/4)
        // -----------------------------------------------------------------------
        Route::prefix('bookings')->name('admin.bookings.')->group(function () {
            Route::get('/', [AdminBookingController::class, 'index'])->name('index');
            Route::get('/create', [AdminBookingController::class, 'create'])->name('create');
            Route::post('/', [AdminBookingController::class, 'store'])->name('store');
            Route::get('/slots', [AdminBookingController::class, 'getAvailableSlots'])->name('slots');
            Route::get('/{booking}', [AdminBookingController::class, 'show'])->name('show');
            Route::put('/{booking}/status', [AdminBookingController::class, 'updateStatus'])->name('update-status');
        });

        Route::get('/users', [AdminUserController::class, 'index'])
            ->name('admin.users.index');


        /*
        |--------------------------------------------------------------------------
        | Astrologer Management
        |--------------------------------------------------------------------------
        */

        Route::prefix('astrologers')
            ->name('admin.astrologers.')
            ->group(function () {

                Route::get('/', [AdminAstrologerController::class, 'index'])
                    ->name('index');

                Route::get('/create', [AdminAstrologerController::class, 'create'])
                    ->name('create');

                Route::post('/', [AdminAstrologerController::class, 'store'])
                    ->name('store');

                Route::get('/{astrologer}', [AdminAstrologerController::class, 'show'])
                    ->name('show');

                Route::get('/{astrologer}/edit', [AdminAstrologerController::class, 'edit'])
                    ->name('edit');

                Route::put('/{astrologer}', [AdminAstrologerController::class, 'update'])
                    ->name('update');

                Route::delete('/{astrologer}', [AdminAstrologerController::class, 'destroy'])
                    ->name('destroy');


                // Quick actions

                Route::post('/{astrologer}/approve', [AdminAstrologerController::class, 'approve'])
                    ->name('approve');

                Route::post('/{astrologer}/reject', [AdminAstrologerController::class, 'reject'])
                    ->name('reject');

                Route::post('/{astrologer}/toggle-featured', [AdminAstrologerController::class, 'toggleFeatured'])
                    ->name('toggle-featured');

                Route::post('/{astrologer}/toggle-availability', [AdminAstrologerController::class, 'toggleAvailability'])
                    ->name('toggle-availability');
            });


        /*
        |--------------------------------------------------------------------------
        | Service / Category Management
        |--------------------------------------------------------------------------
        */

        Route::prefix('services')
            ->name('admin.services.')
            ->group(function () {

                Route::get('/', [AdminServiceController::class, 'index'])
                    ->name('index');

                Route::get('/create', [AdminServiceController::class, 'create'])
                    ->name('create');

                Route::post('/', [AdminServiceController::class, 'store'])
                    ->name('store');

                Route::get('/{service}/edit', [AdminServiceController::class, 'edit'])
                    ->name('edit');

                Route::put('/{service}', [AdminServiceController::class, 'update'])
                    ->name('update');

                Route::delete('/{service}', [AdminServiceController::class, 'destroy'])
                    ->name('destroy');

                Route::post('/{service}/toggle-status', [AdminServiceController::class, 'toggleStatus'])
                    ->name('toggle-status');
            });


        /*
        |--------------------------------------------------------------------------
        | Booking Management
        |--------------------------------------------------------------------------
        */

        Route::prefix('bookings')
            ->name('admin.bookings.')
            ->group(function () {

                Route::get('/', [AdminBookingController::class, 'index'])
                    ->name('index');

                Route::get('/{booking}', [AdminBookingController::class, 'show'])
                    ->name('show');

                Route::put('/{booking}/status', [AdminBookingController::class, 'updateStatus'])
                    ->name('update-status');
            });


        /*
        |--------------------------------------------------------------------------
        | Product Category Management
        |--------------------------------------------------------------------------
        */

        Route::prefix('categories')
            ->name('admin.categories.')
            ->group(function () {

                Route::get('/', [AdminCategoryController::class, 'index'])
                    ->name('index');

                Route::get('/create', [AdminCategoryController::class, 'create'])
                    ->name('create');

                Route::post('/', [AdminCategoryController::class, 'store'])
                    ->name('store');

                Route::get('/{category}/edit', [AdminCategoryController::class, 'edit'])
                    ->name('edit');

                Route::put('/{category}', [AdminCategoryController::class, 'update'])
                    ->name('update');

                Route::delete('/{category}', [AdminCategoryController::class, 'destroy'])
                    ->name('destroy');

                Route::post('/{category}/toggle-status', [AdminCategoryController::class, 'toggleStatus'])
                    ->name('toggle-status');
            });


        /*
        |--------------------------------------------------------------------------
        | Product Management
        |--------------------------------------------------------------------------
        */

        Route::prefix('products')
            ->name('admin.products.')
            ->group(function () {

                Route::get('/', [AdminProductController::class, 'index'])
                    ->name('index');

                Route::get('/create', [AdminProductController::class, 'create'])
                    ->name('create');

                Route::post('/', [AdminProductController::class, 'store'])
                    ->name('store');

                Route::get('/{product}/edit', [AdminProductController::class, 'edit'])
                    ->name('edit');

                Route::put('/{product}', [AdminProductController::class, 'update'])
                    ->name('update');

                Route::delete('/{product}', [AdminProductController::class, 'destroy'])
                    ->name('destroy');

                Route::post('/{product}/toggle-status', [AdminProductController::class, 'toggleStatus'])
                    ->name('toggle-status');

                Route::post('/{product}/toggle-featured', [AdminProductController::class, 'toggleFeatured'])
                    ->name('toggle-featured');
            });


        /*
        |--------------------------------------------------------------------------
        | Order Management
        |--------------------------------------------------------------------------
        */

        Route::prefix('orders')
            ->name('admin.orders.')
            ->group(function () {

                Route::get('/', [AdminOrderController::class, 'index'])
                    ->name('index');

                Route::get('/{order}', [AdminOrderController::class, 'show'])
                    ->name('show');

                Route::put('/{order}/status', [AdminOrderController::class, 'updateStatus'])
                    ->name('update-status');

                Route::put('/{order}/payment-status', [AdminOrderController::class, 'updatePaymentStatus'])
                    ->name('update-payment-status');
            });

        /*
        |--------------------------------------------------------------------------
        | Commission Management
        |--------------------------------------------------------------------------
        */
        Route::prefix('commissions')->name('admin.commissions.')->group(function () {
            Route::get('/', [AdminCommissionController::class, 'index'])->name('index');
            Route::get('/{commission}', [AdminCommissionController::class, 'show'])->name('show');
            Route::post('/{commission}/approve', [AdminCommissionController::class, 'approve'])->name('approve');
            Route::post('/{commission}/reject', [AdminCommissionController::class, 'reject'])->name('reject');
        });

        /*
        |--------------------------------------------------------------------------
        | Withdrawal Management
        |--------------------------------------------------------------------------
        */
        Route::prefix('withdrawals')->name('admin.withdrawals.')->group(function () {
            Route::get('/', [AdminWithdrawalController::class, 'index'])->name('index');
            Route::get('/{withdrawal}', [AdminWithdrawalController::class, 'show'])->name('show');
            Route::post('/{withdrawal}/approve', [AdminWithdrawalController::class, 'approve'])->name('approve');
            Route::post('/{withdrawal}/processing', [AdminWithdrawalController::class, 'markProcessing'])->name('processing');
            Route::post('/{withdrawal}/paid', [AdminWithdrawalController::class, 'markPaid'])->name('mark-paid');
            Route::post('/{withdrawal}/reject', [AdminWithdrawalController::class, 'reject'])->name('reject');
        });

        /*
        |--------------------------------------------------------------------------
        | Astrologer Wallet & Ledger Management
        |--------------------------------------------------------------------------
        */
        Route::prefix('wallets')->name('admin.wallets.')->group(function () {
            Route::get('/', [AdminWalletController::class, 'index'])->name('index');
            Route::get('/transactions', [AdminWalletController::class, 'transactions'])->name('transactions');
            Route::post('/{wallet}/adjust', [AdminWalletController::class, 'adjust'])->name('adjust');
        });

        /*
        |--------------------------------------------------------------------------
        | Referral Program Settings
        |--------------------------------------------------------------------------
        */
        Route::prefix('referral-settings')->name('admin.settings.')->group(function () {
            Route::get('/', [AdminSettingsController::class, 'index'])->name('index');
            Route::post('/', [AdminSettingsController::class, 'update'])->name('update');
        });

        /*
        |--------------------------------------------------------------------------
        | Referral Clicks & Partner Approval
        |--------------------------------------------------------------------------
        */
        Route::prefix('referrals')->name('admin.referrals.')->group(function () {
            Route::get('/', [AdminReferralController::class, 'index'])->name('index');
            Route::get('/partners', [AdminReferralController::class, 'partners'])->name('partners');
            Route::post('/partners/{astrologer}/status', [AdminReferralController::class, 'updatePartnerStatus'])->name('partners.update-status');
        });
    });
});

/*
|--------------------------------------------------------------------------
| Public Referral Landing Route
|--------------------------------------------------------------------------
*/
Route::get('/ref/{code}', [ReferralLandingController::class, 'handle'])->name('referral.landing');

/*
|--------------------------------------------------------------------------
| Astrologer Partner Portal Routes
|--------------------------------------------------------------------------
*/
Route::prefix('astrologer')->name('astrologer.')->group(function () {
    // Guest / Application routes
    Route::get('/login', [AstrologerAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AstrologerAuthController::class, 'login'])->name('login.post');
    Route::get('/register', [AstrologerAuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AstrologerAuthController::class, 'register'])->name('register.post');
    Route::post('/logout', [AstrologerAuthController::class, 'logout'])->name('logout');
    Route::get('/pending', [AstrologerAuthController::class, 'pending'])->name('pending');
    Route::get('/suspended', [AstrologerAuthController::class, 'suspended'])->name('suspended');

    // Authenticated and Approved Astrologer routes
    Route::middleware(['auth:web', 'approved.astrologer'])->group(function () {
        Route::get('/dashboard', [AstrologerDashboardController::class, 'index'])->name('dashboard');
        Route::get('/referrals', [AstrologerReferralController::class, 'index'])->name('referrals.index');
        Route::get('/referrals/qr', [AstrologerReferralController::class, 'qrCode'])->name('referrals.qr');
        Route::get('/commissions', [AstrologerCommissionController::class, 'index'])->name('commissions.index');
        Route::get('/commissions/{commission}', [AstrologerCommissionController::class, 'show'])->name('commissions.show');
        Route::get('/wallet', [AstrologerWalletController::class, 'index'])->name('wallet.index');
        Route::get('/withdrawals', [AstrologerWithdrawalController::class, 'index'])->name('withdrawals.index');
        Route::post('/withdrawals', [AstrologerWithdrawalController::class, 'store'])->name('withdrawals.store');
        Route::post('/withdrawals/{withdrawal}/cancel', [AstrologerWithdrawalController::class, 'cancel'])->name('withdrawals.cancel');
        Route::get('/analytics', [AstrologerAnalyticsController::class, 'index'])->name('analytics.index');
        Route::get('/profile', [AstrologerProfileController::class, 'index'])->name('profile.index');
        Route::post('/profile', [AstrologerProfileController::class, 'update'])->name('profile.update');
        Route::post('/profile/password', [AstrologerProfileController::class, 'updatePassword'])->name('profile.password');
    });
});
