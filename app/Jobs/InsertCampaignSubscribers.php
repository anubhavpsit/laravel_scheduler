<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\CampaignSubscribers;
use App\Models\ScheduleCampaignsToProcess;
use App\Models\Lists;
use App\Models\ListSubscribers;

class InsertCampaignSubscribers implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $campaignData;


    /*
    Queue => add_subscribers_for_campaign
    Payload => {"uuid":"de2e9334-ca61-4084-a6ac-42d36aac5b4d","displayName":"App\\Jobs\\InsertCampaignSubscribers","job":"Illuminate\\Queue\\CallQueuedHandler@call","maxTries":null,"maxExceptions":null,"delay":null,"timeout":null,"timeoutAt":null,"data":{"commandName":"App\\Jobs\\InsertCampaignSubscribers","command":"O:34:\"App\\Jobs\\InsertCampaignSubscribers\":9:{s:15:\"\u0000*\u0000campaignData\";a:1:{s:12:\"campaign_ids\";a:2:{i:0;i:4;i:1;i:3;}}s:3:\"job\";N;s:10:\"connection\";N;s:5:\"queue\";s:28:\"add_subscribers_for_campaign\";s:15:\"chainConnection\";N;s:10:\"chainQueue\";N;s:5:\"delay\";N;s:10:\"middleware\";a:0:{}s:7:\"chained\";a:0:{}}"}}
    */

    /**
     * Create a new job instance.
     * php artisan queue:listen --queue=add_subscribers_for_campaign
     *
     * @return void
     */
    public function __construct($campaignData)
    {
        $this->campaignData = $campaignData;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $campaignData = $this->campaignData;

        //$this->info('Campaign ids send to processing queue');
        $campaignIds = $campaignData['campaign_ids'];
        $scheduleCampaignsToProcess = new ScheduleCampaignsToProcess();
        $scheduleCampaignsToProcessList = $scheduleCampaignsToProcess->getScheduledCampaignsListByStatus(ScheduleCampaignsToProcess::NOT_PICKED);
        $listModel = new Lists();
        $listSubscribersModel = new ListSubscribers();
        foreach ($scheduleCampaignsToProcessList as $scheduleCampaigns) {
            $lists = unserialize($scheduleCampaigns->lists);
            foreach ($lists as $list) {
                $listData = $listModel->getActiveListsById($list);
                if ($listData->status == Lists::ACTIVE) {
                    \Log::info("Inserting list id " . $list . " for sending");
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
        }
        return 55;
    }
}
