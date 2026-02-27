<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schedule;

class Kernel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:kernel';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
    }

    protected function schedule(Schedule $schedule)
    {
        // Discover content for users every 6 hours
        $schedule->command('content:discover --batch=100')->everySixHours()->withoutOverlapping()->sendOutputTo(storage_path('logs/content-discovery.log'));

        // Update popularity scores daily
        $schedule
            ->call(function () {
                app(\App\Services\ML\ResourceReferralService::class)->updatePopularityScores();
            })
            ->daily();
    }
}
