<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */

    protected $commands = [
        Commands\RefreshCachedTbl_users_nfm::class,
        Commands\ProcessSubmittedLoans::class,
        Commands\UpdateCountLoanStatus::class,
        Commands\UpdateCountLoanStatusEmail::class,
        Commands\Updateallocationreport::class,
        Commands\reallocate_loan::class,
        Commands\UpdateIndexToIdNumberHEF::class,






    ];

    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();

        $schedule->command('loans:process-submitted-loans')

            // ->everyTwoHours($minutes = 50)
            //  ->cron('55 */2 * * *') // Runs every two hours at the 50th minute
            ->everyTwoHours()

            ->timezone('Africa/Nairobi')

            ->runInBackground()
            ->withoutOverlapping();

        // $schedule->command('loans:updateallocationreport')

        // ->hourly()

        // ->timezone('Africa/Nairobi')

        // ->runInBackground();
        // ->withoutOverlapping();

        $schedule->command('loans:update-count-loan-status')


            ->timezone('Africa/Nairobi')
            ->everyTwoHours()

            ->runInBackground()
            ->withoutOverlapping();




        $schedule->command('loans:reallocate_loan')
            ->timezone('Africa/Nairobi')
            ->everyThirtyMinutes()
            ->runInBackground()
            ->withoutOverlapping();

        $schedule->command('run:updateindextoidumberhef')
            ->everyTenMinutes()
            ->timezone('Africa/Nairobi')
            ->runInBackground()
            ->withoutOverlapping();





        $schedule->command('loans:update-count-loan-status-email')


            ->timezone('Africa/Nairobi')
            ->twiceDaily(8, 17, 15)

            ->runInBackground()
            ->withoutOverlapping();


        // $schedule->command('loans:update-count-loan-status-email')
        //     ->timezone('Africa/Nairobi')
        //     ->cron('15 8,17 * * *')
        //     ->runInBackground()
        //     ->withoutOverlapping();

        // $schedule->command('loans:update-count-loan-status-email')
        //     ->dailyAt('08:15')
        //     ->timezone('Africa/Nairobi')
        //     ->runInBackground()
        //     ->withoutOverlapping();

        // $schedule->command('loans:update-count-loan-status-email')
        //     ->dailyAt('17:15')
        //     ->timezone('Africa/Nairobi')
        //     ->runInBackground()
        //     ->withoutOverlapping();
    }
    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
