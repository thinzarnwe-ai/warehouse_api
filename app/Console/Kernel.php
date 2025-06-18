<?php

namespace App\Console;

use App\Console\Commands\CancelCode;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [CancelCode::class];

    protected function schedule(Schedule $schedule)
    {
        $schedule->command('remove:cancel')->timezone('Asia/Yangon')->dailyAt('07:00');
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
