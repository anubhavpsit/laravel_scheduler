<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class ListSubscribers extends Model
{
    protected $table = 'list_subscribers';

    public function getListSubscribersByListId($listId = NULL) {

    	if(is_null($listId) || $listId==0) { return []; }

    	$query = DB::table($this->table);
    	$query->where('list_id',$listId);
		
		return $query->get()->sortByDesc('id')->toArray();
    }

	public function getListSubscribersByListIdRemovingBounce($listId = NULL) {

		if(is_null($listId) || $listId==0) { return []; }

		$query = ListSubscribers::leftJoin('hardbounce', function($join) {
	      $join->on('list_subscribers.email', '=', 'hardbounce.email');
	    })
		->where('list_subscribers.list_id', $listId)
	    ->whereNull('hardbounce.email')
	    ->select('list_subscribers.*');
	    //->toArray()
		return $query->get()->sortByDesc('id');
	}
    
}
