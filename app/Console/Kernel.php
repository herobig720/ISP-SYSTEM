<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Console\Commands\MakeMinimalModule;

class Kernel extends ConsoleKernel
{
    /**
     * Register Artisan commands.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        $this->commands([
            MakeMinimalModule::class,
        ]);
    }

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Define scheduled tasks here
    }
}
