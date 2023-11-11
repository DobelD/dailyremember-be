<?php

namespace App\Console;
use App\Console\Commands\Reminder;
use Illuminate\Console\Scheduling\Schedule;
use App\Http\Controllers\FirebaseServiceController;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        Reminder::class,
    ];
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('reminder:cron')->dailyAt('06:00')->timezone('Asia/Jakarta');
        $schedule->command('reminder:cron')->dailyAt('08:00')->timezone('Asia/Jakarta');
        $schedule->command('reminder:cron')->dailyAt('08:20')->timezone('Asia/Jakarta');
        $schedule->command('reminder:cron')->dailyAt('17:00')->timezone('Asia/Jakarta');
        $schedule->command('reminder:cron')->dailyAt('20:00')->timezone('Asia/Jakarta');
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
