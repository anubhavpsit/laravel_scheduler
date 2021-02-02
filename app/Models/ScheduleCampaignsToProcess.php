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
    public const READY_TO_GO = 5;

    public const TYPE_EMAIL = 1;
    public const TYPE_SPLIT = 2;
    public const TYPE_SMS = 3;

    public function getScheduledCampaignsListByStatus($status=0) {

    	$query = DB::table($this->table);
		$query->where('status',$status);
		
		$results = $query->get()->sortByDesc('id')->toArray();
		return $results;
    }

    public function updateCampaignsToProcessStatus($status=0, $campaignId=0) {

        if($campaignId == 0) {
            return false;
        } else {
            DB::table($this->table)->where('campaign_id',$campaignId)->update(['status' => $status]);   
            return true;         
        }
    }

    public function getCampaignDataById($campaignId) {

        $query = DB::table($this->table);
        $query->where('campaign_id', $campaignId)->select('*');

        $results = $query->get()->first();
        return $results;
    }
}
