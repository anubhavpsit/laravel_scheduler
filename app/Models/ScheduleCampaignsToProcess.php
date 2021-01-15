<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class ScheduleCampaignsToProcess extends Model
{
    protected $table = 'schedule_campaigns_to_process';

    public const NOT_PICKED = 0;
    public const PICKED = 1;
    public const SENDING = 2;
    public const SENT = 3;
    public const FAILED = 4;

    public const TYPE_EMAIL = 1;
    public const TYPE_SPLIT = 2;
    public const TYPE_SMS = 3;

}
