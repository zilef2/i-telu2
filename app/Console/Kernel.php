<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
        //    $schedule->command('send:zip')->daily();
        //    $schedule->command('send:zip')->everyTwoMinutes();
       $schedule->command('backup:run')->everyTenMinutes();
       $schedule->command('send:zip')->everyTenMinutes();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
        
        // $this->command('command:nuncaUsare','app\Console\Commands\PruebaGenerarControlador.php');
    }
}
