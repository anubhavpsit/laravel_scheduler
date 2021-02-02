<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Campaigns;
use App\Models\CampaignDetails;
use App\Models\CampaignLinks;
use App\Models\CampaignSubscribers;
use App\Models\ScheduleCampaignsToProcess;
use App\Jobs\InsertCampaignSubscribers;
use App\Models\Lists;
use App\Models\ListSubscribers;

use CommonHelper;

class ScheduleEmailCampaign extends Command
{
    /**
     * The name and signature of the console command.
     * php artisan command:scheduleemailcampaign
     *
     * php artisan command:scheduleemailcampaign
     * php artisan queue:listen --queue=add_subscribers_for_campaign
     * TRUNCATE campaign_subscribers;
     * TRUNCATE schedule_campaigns_to_process;
     * TRUNCATE jobs;
     * TRUNCATE job_batches;
     * UPDATE campaigns SET status = 1 WHERE id IN (3,4);
     * DELETE FROM campaign_links WHERE campaign_id in(3,4);
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
        // $this->test();
        // exit();
        $trackingLink = "http://localhost:3000/";
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
        $campaignLinks = [];
        foreach($campaignsList as $campaign) {
            $c = [];
            $c['campaign_type'] = $campaign->campaign_type;
            $c['campaign_id'] = $campaign->id;
            $c['campaign_subject'] = $campaign->campaign_subject;
            $c['user_id'] = $campaign->user_id;
            $c['template_id'] = $campaign->campaign_details->template_id;
            $c['lists'] = $campaign->campaign_details->lists;
            $c['created_at'] = $scheduleDate;
            $c['updated_at'] = $scheduleDate;
            $c['campaign_links'] = null;
            $htmlDom = new \DOMDocument;
            $htmlDom->loadHTML($campaign->campaign_details->html_content);
            $links = $htmlDom->getElementsByTagName('a');
            $extractedLinks = array();
            $validLinks = [];
            $linkNumber = 1;
            foreach ($links as $link) {
                $linkText = $link->nodeValue;
                $linkHref = $link->getAttribute('href');
                // print_r($linkText);
                // print_r($linkHref);
                // print_r($linkHref);
                if(($linkHref == '#') || ($linkHref == 'javascript:void(0)') || ($linkHref == 'javascript:void(0);')){
                    continue;
                } else {
                    if(!in_array($linkHref, $validLinks)) {
                        $validLinks[$linkNumber] = $linkHref;
                        $linkNumber++;
                    }  
                }
            }

            foreach ($links as $link) {
                $key = array_search($link->getAttribute('href'), $validLinks);
                if($key) {
                    $link->setAttribute('href', $trackingLink.$key); 
                }
            }
            $campaign->campaign_details->html_content = $htmlDom->saveHTML();
            $c['content'] = $campaign->campaign_details->html_content;
            if(!empty($validLinks)) {
                $c['campaign_links'] = json_encode($validLinks);
            }
            array_push($processCampaigns, $c);
            array_push($campaignLinks, ['campaign_id' => $c['campaign_id'], 'campaign_links' => $c['campaign_links'], 'status' => CampaignLinks::ACTIVE]);


        }

        try {
            ScheduleCampaignsToProcess::insert($processCampaigns);
            CampaignLinks::insert($campaignLinks);
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


    function test() {
        $a = [];
        $c = [4,3];
        $a['campaign_ids']  =$c;

        $campaignIds = $a['campaign_ids'];
        $scheduleCampaignsToProcess = new ScheduleCampaignsToProcess();
        $scheduleCampaignsToProcessList = $scheduleCampaignsToProcess->getScheduledCampaignsListByStatus(ScheduleCampaignsToProcess::NOT_PICKED);
        $listModel = new Lists();
        $listSubscribersModel = new ListSubscribers();
        foreach ($scheduleCampaignsToProcessList as $scheduleCampaigns) {
            $lists = unserialize($scheduleCampaigns->lists);
            foreach ($lists as $list) {
                $listData = $listModel->getActiveListsById($list);
                if ($listData->status == Lists::ACTIVE) {
                    print_r($list);
                    $listSubscribers = $listSubscribersModel->getListSubscribersByListId($list);
                    $listSubscribersArr = [];
                    foreach($listSubscribers as $listSubscriber) {
                        unset($listSubscriber->id);
                        $lArr = [];
                        $lArr['list_id'] = $listSubscriber->list_id;
                        $lArr['email'] = $listSubscriber->email;
                        $lArr['first_name'] = $listSubscriber->first_name;
                        $lArr['last_name'] = $listSubscriber->last_name;
                        $lArr['phone_number'] = $listSubscriber->phone_number;
                        $lArr['age'] = $listSubscriber->age;
                        $lArr['gender'] = $listSubscriber->gender;
                        $lArr['city'] = $listSubscriber->city;
                        $lArr['file_row'] = $listSubscriber->file_row;
                        $lArr['created_at'] = $listSubscriber->created_at;
                        $lArr['updated_at'] = $listSubscriber->updated_at;
                        $lArr['created_by'] = $listSubscriber->created_by;
                        $lArr['updated_by'] = $listSubscriber->updated_by;
                        $lArr['field_0'] = $listSubscriber->field_0;
                        $lArr['field_1'] = $listSubscriber->field_1;
                        $lArr['field_2'] = $listSubscriber->field_2;
                        $lArr['field_3'] = $listSubscriber->field_3;
                        $lArr['campaign_id'] = $scheduleCampaigns->campaign_id;
                        array_push($listSubscribersArr, $lArr);
                    }
                    try {
                        CampaignSubscribers::insert($listSubscribersArr);
                    } catch(\Exception $e) {
                        $this->info('Error '. $e->getMessage());
                    }
                }
            }
            $scheduleCampaignsToProcess->updateCampaignsToProcessStatus(ScheduleCampaignsToProcess::READY_TO_GO, $scheduleCampaigns->campaign_id);
        }
    }
}
