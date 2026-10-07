<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        if (!config('backup.enabled')) return;
        $schedule->command('backup:run')->dailyAt(config('backup.time'))->timezone(config('backup.timezone'))
            ->withoutOverlapping(180)->appendOutputTo(storage_path('logs/backups.log'));
        $schedule->command('backup:clean')->dailyAt('04:15')->timezone(config('backup.timezone'))
            ->withoutOverlapping(60)->appendOutputTo(storage_path('logs/backups.log'));
        $schedule->command('backup:monitor')->dailyAt('04:30')->timezone(config('backup.timezone'))
            ->withoutOverlapping(60)->appendOutputTo(storage_path('logs/backups.log'));

    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
