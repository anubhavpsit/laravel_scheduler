<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Campaigns;
use App\Models\CampaignDetails;

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
        $campaigns = new Campaigns();
        $campaingsList = $campaigns->getScheduledCampaignsList();

        $campaignDetails = new CampaignDetails();
        foreach($campaingsList as $campaing) {
            $cDetails = $campaignDetails->getCampaignDetailsById($campaing->id);
            print_r($cDetails);
        }
//        print_r($campaingsList);
        $this->info('Checks if any campaign is schedule for the time then send it in queue');
        return 0;
    }
}
