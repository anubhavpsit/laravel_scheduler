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
}
