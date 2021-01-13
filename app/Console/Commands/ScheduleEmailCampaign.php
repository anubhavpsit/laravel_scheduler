<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ScheduleEmailCampaign extends Command
{
    /**
     * The name and signature of the console command.
     * php artisan command:scheduleemailcampaign
     * @var string
     */
    protected $signature = 'command:scheduleemailcampaign';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks if any campaign is schedule for the time then send it in queue';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $this->info('Checks if any campaign is schedule for the time then send it in queue');
        return 0;
    }
}
