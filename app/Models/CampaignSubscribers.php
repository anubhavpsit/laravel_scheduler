<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class CampaignSubscribers extends Model
{
    //
    protected $table = 'campaign_subscribers';

    public const NOT_PICKED = 0;

    public function getBatchData($batchNumber) {

    	$query = DB::table($this->table);
    	$query->where('batch_number', $batchNumber);
		$query->where('status',self::NOT_PICKED);
		
		$results = $query->get()->sortByDesc('id')->toArray();
		return $results;
    }

    public function getBatchLists($batchNumber) {
		return DB::table($this->table)->select('list_id')->where('batch_number', $batchNumber)->distinct('list_id')->get()->toArray();
    }

    public function getBatchCampaigns($batchNumber) {
		return DB::table($this->table)->select('campaign_id')->where('batch_number', $batchNumber)->distinct('campaign_id')->get()->toArray();
    }
    
    
}
