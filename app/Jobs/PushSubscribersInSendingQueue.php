<?php

namespace App\Jobs;

use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class PushSubscribersInSendingQueue implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $batchNumbers;

    /*
    Queue => push_subscribers_in_sending_queue
    Payload => {"uuid":"d4f6df0d-192a-42a7-a843-ad7d1218cefa","displayName":"App\\Jobs\\PushSubscribersInSendingQueue","job":"Illuminate\\Queue\\CallQueuedHandler@call","maxTries":null,"maxExceptions":null,"backoff":null,"timeout":null,"retryUntil":null,"data":{"commandName":"App\\Jobs\\PushSubscribersInSendingQueue","command":"O:38:\"App\\Jobs\\PushSubscribersInSendingQueue\":11:{s:15:\"\u0000*\u0000batchNumbers\";a:1:{s:7:\"batches\";a:5:{i:0;i:1611126656;i:1;i:1611126657;i:2;i:1611126658;i:3;i:1611126659;i:4;i:1611126660;}}s:3:\"job\";N;s:10:\"connection\";N;s:5:\"queue\";s:33:\"push_subscribers_in_sending_queue\";s:15:\"chainConnection\";N;s:10:\"chainQueue\";N;s:19:\"chainCatchCallbacks\";N;s:5:\"delay\";N;s:11:\"afterCommit\";N;s:10:\"middleware\";a:0:{}s:7:\"chained\";a:0:{}}"}}
    */
    /**
     * Create a new job instance.
     *
     * php artisan queue:listen --queue=push_subscribers_in_sending_queue
     * @return void
     */
    public function __construct($batchNumbers)
    {
        $this->batchNumbers = $batchNumbers;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        print_r($this->batchNumbers);
        return 56;
    }
}
