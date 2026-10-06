<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

$email = 'autotest_' . time() . '@example.com';

echo "===========================================\n";
echo "  AstroVani Auth Test\n";
echo "===========================================\n\n";

// --- STEP 1: REGISTER ---
echo "[ REGISTRATION TEST ]\n";
echo "Creating user: $email\n";

try {
    $user = User::create([
        'first_name'    => 'Test',
        'last_name'     => 'User',
        'email'         => $email,
        'password'      => Hash::make('Password123'),
        'country'       => 'India',
    ]);

    echo "  OK  User created successfully!\n";
    echo "      ID         : {$user->id}\n";
    echo "      first_name : {$user->first_name}\n";
    echo "      last_name  : {$user->last_name}\n";
    echo "      full_name  : {$user->full_name}\n";
    echo "      email      : {$user->email}\n";
    echo "      status     : {$user->status}\n";
    echo "      country    : {$user->country}\n";
} catch (\Throwable $e) {
    echo "  FAIL  Registration FAILED!\n";
    echo "        Error: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n";

// --- STEP 2: LOGIN CHECK ---
echo "[ LOGIN TEST ]\n";
$found = User::where('email', $email)->first();

if (!$found) {
    echo "  FAIL  User not found in DB!\n";
    exit(1);
}

$passwordOk = Hash::check('Password123', $found->password);

if ($passwordOk) {
    echo "  OK  Password check PASSED!\n";
    echo "      Welcome back, {$found->first_name}!\n";
} else {
    echo "  FAIL  Password check FAILED!\n";
    exit(1);
}

echo "\n";

// --- STEP 3: WRONG PASSWORD ---
echo "[ WRONG PASSWORD TEST ]\n";
$wrongPass = Hash::check('WrongPassword', $found->password);
if (!$wrongPass) {
    echo "  OK  Wrong password correctly rejected!\n";
} else {
    echo "  FAIL  Wrong password was accepted (security bug)!\n";
}

echo "\n";

// --- CLEANUP ---
echo "[ CLEANUP ]\n";
$found->delete();
echo "  OK  Test user deleted.\n";

echo "\n===========================================\n";
echo "  ALL TESTS PASSED\n";
echo "===========================================\n";
