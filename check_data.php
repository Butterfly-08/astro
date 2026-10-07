<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$admin = App\Models\Admin::first();
echo "Admin email: " . ($admin ? $admin->email : 'NOT FOUND') . PHP_EOL;
echo "Astrologers total: " . App\Models\Astrologer::count() . PHP_EOL;
echo "Products total: " . App\Models\Product::count() . PHP_EOL;
echo "Orders total: " . App\Models\Order::count() . PHP_EOL;
echo "Commissions total: " . App\Models\Commission::count() . PHP_EOL;
echo "Wallets total: " . App\Models\Wallet::count() . PHP_EOL;
echo "Withdrawals total: " . App\Models\Withdrawal::count() . PHP_EOL;
echo "Settings total: " . App\Models\Setting::count() . PHP_EOL;
echo "Referrals total: " . App\Models\Referral::count() . PHP_EOL;
echo "WalletTransactions total: " . App\Models\WalletTransaction::count() . PHP_EOL;
echo PHP_EOL;
$astrologer = App\Models\Astrologer::first();
if ($astrologer) {
    echo "First astrologer: " . $astrologer->name . " | status: " . $astrologer->status . " | referral_code: " . $astrologer->referral_code . PHP_EOL;
    $wallet = $astrologer->wallet;
    if ($wallet) {
        echo "  Wallet balance: " . $wallet->balance . " | pending: " . $wallet->pending_balance . PHP_EOL;
    }
}
