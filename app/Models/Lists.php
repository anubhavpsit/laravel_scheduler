<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class Lists extends Model
{
    protected $table = 'list';

    public const UPLOADING = 0;
    public const ACTIVE = 1;
    public const INACTIVE = 2;
    public const EXPIRED = 3;
    public const DELETED = 4;


    public function getActiveListsById($listId = NULL) {

    	if(is_null($listId) || $listId==0) { return []; }

    	$query = DB::table($this->table);
    	$query->where('id',$listId);
		$query->where('status',self::ACTIVE);
		
		$results = $query->get()->sortByDesc('id')->toArray();
		if(empty($results)) {
			return [];
		}
		return $results[0];
    }
}
