<?php

namespace App\Console\Commands;

use App\Models\Admin;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ProvisionAdmin extends Command
{
    protected $signature = 'admin:provision {email} {--name=}';

    protected $description = 'Create an active admin account or reactivate an existing one';

    public function handle(): int
    {
        $email = strtolower(trim((string) $this->argument('email')));

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error('Enter a valid email address.');

            return self::FAILURE;
        }

        $admin = Admin::where('email', $email)->first();

        if ($admin) {
            if ($admin->isActive()) {
                $this->info('This admin account is already active.');

                return self::SUCCESS;
            }

            $admin->update(['status' => 'active']);
            $this->info('The admin account has been reactivated.');

            return self::SUCCESS;
        }

        $name = trim((string) ($this->option('name') ?: $this->ask(
            'Admin name',
            Str::headline(Str::before($email, '@'))
        )));

        if ($name === '') {
            $this->error('Admin name is required.');

            return self::FAILURE;
        }

        $password = $this->secret('New admin password (at least 12 characters)');
        $confirmation = $this->secret('Confirm admin password');

        if (!is_string($password) || strlen($password) < 12 || $password !== $confirmation) {
            $this->error('The password must be at least 12 characters and both entries must match.');

            return self::FAILURE;
        }

        Admin::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'status' => 'active',
        ]);

        $this->info('Admin account created and activated.');

        return self::SUCCESS;
    }
}
