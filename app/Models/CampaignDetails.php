<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class CampaignDetails extends Model
{
    protected $table = 'campaigns';

    public function getCampaignDetailsById($campaignId) {

    	$query = DB::table($this->table);
    	$query->where('id',$campaignId);
		$results = $query->get()->sortByDesc('id')->toArray();
		return $results;
    }
}
