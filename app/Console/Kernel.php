<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        // ====================================================================
        // DAILY DISTRIBUTION CALCULATIONS
        // ====================================================================
        
        // Calculate investor distributions at 11:58 PM daily
        $schedule->command('distributions:calculate --type=investor')
            ->dailyAt('23:58')
            ->withoutOverlapping()
            ->runInBackground()
            ->emailOutputOnFailure([config('mail.admin_email', 'admin@yourcompany.com')])
            ->environments(['production', 'staging'])
            ->description('Calculate daily investor distributions');
        
        // Calculate operator distributions at 11:59 PM daily
        $schedule->command('distributions:calculate --type=operator')
            ->dailyAt('23:59')
            ->withoutOverlapping()
            ->runInBackground()
            ->emailOutputOnFailure([config('mail.admin_email', 'admin@yourcompany.com')])
            ->environments(['production', 'staging'])
            ->description('Calculate daily operator distributions'); 
    }


    

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
    