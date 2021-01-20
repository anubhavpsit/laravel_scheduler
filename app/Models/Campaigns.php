<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class Campaigns extends Model
{
    protected $table = 'campaigns';
    
    public const INCOMPLETE = 0;
    public const COMPLETE = 1;
    public const SENDING = 2;
    public const SENT = 3;
    public const DELETED = 4;

    public const TYPE_EMAIL = 1;
    public const TYPE_SPLIT = 2;

    public function getCampaignsCount() {
        $listCount = DB::table($this->table)->count();
        return $listCount;    	
	}

	public function getUserCampaignsArrayByLimit($limit, $offset) {
        $lists = DB::table($this->table)->orderby('id', 'desc')->offset($offset)->limit($limit)->get()->toArray();
        return $lists;    	
	}

    public function getCampaignsFilteredCount($cond) {
        $query = DB::table($this->table);
        $query->where('user_id',$cond['user_id']);

        if (isset($cond['campaign_name'])) {
            $query->where('campaign_name', 'LIKE', '%' . $cond['campaign_name'] . '%');
        }

        if (isset($cond['campaign_type'])) {
            $query->whereIn('campaign_type', $cond['campaign_type']);
        }

        if (isset($cond['status'])) {
            $query->whereIn('status', $cond['status']);
        }
        return $query->count();

        // $listCount = DB::table($this->table)->where('created_by',$user_id)->count();
        // return $listCount;      
    } 


    public function getFilteredCampaignsArrayByLimit($limit, $offset, $cond) {

        if ($cond['user_id'] == 0) {
            return [];
        }

        $query = DB::table($this->table)->where('user_id',$cond['user_id']);
        $query->where('user_id',$cond['user_id']);
        
        if (isset($cond['campaign_name'])) {
            $query->where('campaign_name', 'LIKE', '%' . $cond['campaign_name'] . '%');
        }
        if (isset($cond['status'])) {
            $query->whereIn('status', $cond['status']);
        }
        if (isset($cond['campaign_type'])) {
            $query->whereIn('campaign_type', $cond['campaign_type']);
        }
        $results = $query->orderby('id','desc')->offset($offset)->limit($limit)->get()->toArray();

        $data = [];
        foreach($results as $result) {
            $result->status_text = 'Incomplete';
            if($result->status ==0) {$result->status_text = 'Incomplete';}
            if($result->status ==1) {$result->status_text = 'Complete';}
            if($result->status ==2) {$result->status_text = 'Sending';}
            if($result->status ==3) {$result->status_text = 'Sent';}
            if($result->status ==4) {$result->status_text = 'Deleted';}

            // $result->total_open = $this->getQrCodesOpenCount($result->id);
            // $result->distinct_open = $this->getQrCodesDistinctOpenCount($result->id);
            // $scan_data=[];
            // $scans = $this->getQrCodesOpenData($result->id);
            // Get the stats of campaign here
            array_push($data, $result);
        }

        return $data;
    }  

    public function getUserCampaignsArray($user_id = 0) {
    	
    	if ($user_id == 0) {
    		return [];
    	}

    	$query = DB::table($this->table);
		$query->where('user_id',$user_id);
		$results = $query->get()->sortByDesc('id');
		#$results = $query->get();

		$data = [];
		foreach($results as $result) {
			$result->processing_status_text = 'Processing';
			$result->list_uploaded_source = 'Web';
			array_push($data, $result);
		}

		return $data;
    }

    public function getScheduledCampaignsList($scheduleDate) {

    	$query = DB::table($this->table);
    	$query->where('is_scheduled',1);
		$query->where('scheduled_at', '<=', $scheduleDate);
		$query->where('status',self::COMPLETE);
		
		$results = $query->get()->sortByDesc('id')->toArray();
		return $results;
    }

    public function updateCampaignsStatus($campaignIds=[]){

        if(!is_array($campaignIds) || empty($campaignIds)) {
            return false;
        } else {
            DB::table($this->table)->whereIn('id',$campaignIds)->update(['status' => self::SENDING]);   
            return true;         
        }
    } 

    public function getCampaignDataById($campaignId) {

        $query = DB::table($this->table)->join('campaign_details', 'campaigns.id', '=', 'campaign_details.campaign_id');
        $query->where('campaigns.id', $campaignId)->select('campaigns.*', 'campaign_details.template_id', 'campaign_details.html_content');

        $results = $query->get()->first();
        return $results;
    }

}
