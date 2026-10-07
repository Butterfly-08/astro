<?php

namespace App\Console\Commands;

use App\Services\CommissionService;
use Illuminate\Console\Command;

class ReleaseCommissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'commissions:release';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Release eligible pending commissions to astrologer available wallet balances';

    /**
     * Execute the console command.
     */
    public function handle(CommissionService $commissionService): int
    {
        $this->info('Checking for commissions eligible for release...');

        $released = $commissionService->releaseEligible();

        $this->info("Successfully released {$released} commissions to available wallet balance.");

        return Command::SUCCESS;
    }
}
