<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

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
        print_r($campaignData);
        //$this->info('Campaign ids send to processing queue');
        echo "44";
        return 55;
    }
}
