<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class CampaignDetails extends Model
{
    protected $table = 'campaign_details';

    public function getCampaignDetailsById($campaignId) {

    	$query = DB::table($this->table);
    	$query->where('id',$campaignId);
		$results = $query->get()->sortByDesc('id')->toArray();
		if(!empty($results)) {
			return $results[0];
		}
		return $results;
    }
}
