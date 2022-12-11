<?php

namespace App\Console;

use App\Console\Commands\SendNewsLetterCommand;
use App\Console\Commands\SendNewsVerificationReminderCommand;
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
         $schedule->command('inspire')
             ->evenInMaintenanceMode()
             ->sendOutputTo(storage_path('inpire.log'))
             ->everyMinute();

         $schedule->call(function () {
            echo "Hola";
         })->everyFiveMinutes();

         $schedule->command(SendNewsLetterCommand::class)
             ->withoutOverlapping()
             ->onOneServer()
             ->mondays();

         $schedule->command(SendNewsVerificationReminderCommand::class)
             ->onOneServer()
             ->daily();

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
    }
}
