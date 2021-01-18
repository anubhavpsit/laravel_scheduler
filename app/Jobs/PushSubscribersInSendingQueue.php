<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class PushSubscribersInSendingQueue implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $batchNumbers;

    /*
    Queue => push_subscribers_in_sending_queue
    Payload => {"uuid":"b6458e51-348b-4065-978a-69fcfd20b1dc","displayName":"App\\Jobs\\PushSubscribersInSendingQueue","job":"Illuminate\\Queue\\CallQueuedHandler@call","maxTries":null,"maxExceptions":null,"delay":null,"timeout":null,"timeoutAt":null,"data":{"commandName":"App\\Jobs\\PushSubscribersInSendingQueue","command":"O:38:\"App\\Jobs\\PushSubscribersInSendingQueue\":9:{s:15:\"\u0000*\u0000batchNumbers\";a:1:{s:7:\"batches\";a:5:{i:0;i:1610968492;i:1;i:1610968493;i:2;i:1610968494;i:3;i:1610968495;i:4;i:1610968496;}}s:3:\"job\";N;s:10:\"connection\";N;s:5:\"queue\";s:33:\"push_subscribers_in_sending_queue\";s:15:\"chainConnection\";N;s:10:\"chainQueue\";N;s:5:\"delay\";N;s:10:\"middleware\";a:0:{}s:7:\"chained\";a:0:{}}"}}
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
        return 56;
    }
}
