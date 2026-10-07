<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('commissions:release')->hourly();

Artisan::command('commission:release', function () {
    $this->call('commissions:release');
})->purpose('Alias for commissions:release');
