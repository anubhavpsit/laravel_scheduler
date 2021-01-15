<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Campaigns;
use App\Models\CampaignDetails;
use App\Models\ScheduleCampaignsToProcess;
use App\Jobs\InsertCampaignSubscribers;

use CommonHelper;

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
        $scheduleDate = CommonHelper::getTodayDateTime();
        $campaigns = new Campaigns();
        $campaignsList = $campaigns->getScheduledCampaignsList($scheduleDate);

        if(!count($campaignsList)) {
            $this->info('No campaigns scheduled for '.$scheduleDate);
            \Log::info('No campaigns scheduled for '.$scheduleDate);
        }

        $campaignDetails = new CampaignDetails();
        $campaignIds = [];
        foreach($campaignsList as $campaign) {
            array_push($campaignIds, $campaign->id);
            $campaign->campaign_details = $campaignDetails->getCampaignDetailsById($campaign->id);
        }

        $processCampaigns = [];
        foreach($campaignsList as $campaign) {
            $c = [];
            $c['campaign_type'] = $campaign->campaign_type;
            $c['campaign_id'] = $campaign->id;
            $c['campaign_subject'] = $campaign->campaign_subject;
            $c['user_id'] = $campaign->user_id;
            $c['template_id'] = $campaign->campaign_details->template_id;
            $c['content'] = $campaign->campaign_details->html_content;
            $c['lists'] = $campaign->campaign_details->lists;
            $c['created_at'] = $scheduleDate;
            $c['updated_at'] = $scheduleDate;
            array_push($processCampaigns, $c);
        }

        try {
            ScheduleCampaignsToProcess::insert($processCampaigns);
            InsertCampaignSubscribers::dispatch(['campaign_ids' => $campaignIds])->onQueue('add_subscribers_for_campaign');
            if($campaigns->updateCampaignsStatus($campaignIds)) {
                $this->info('Campaign ids '. implode(",", $campaignIds).' send to processing queue');
                \Log::info("CAMPAIGN QUEUE => " . 'Campaign ids '. implode(",", $campaignIds).' send to processing queue');
            } else {
                $this->info('Campaign ids '. implode(",", $campaignIds).' not sent to processing queue');
                \Log::info("CAMPAIGN QUEUE => " . 'Campaign ids '. implode(",", $campaignIds).' not sent to processing queue');
            }
        } catch(\Exception $e) {
            $this->info('Error '. $e->getMessage());
        }
 
        return 0;
    }
}
