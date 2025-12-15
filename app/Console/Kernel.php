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
        // $schedule->command('inspire')->hourly();

        // Procesar secuencias
        $schedule->command('app:process-sequences')->dailyAt('07:00');
        
        // Revisar leads inactivos
        $schedule->command('leads:check-inactive')
            ->dailyAt('09:00')
            ->weekdays(); // Solo días laborables
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