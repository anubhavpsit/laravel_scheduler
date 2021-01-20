<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;

class ListPersonalizationInfo extends Model
{
    use HasFactory;
    protected $table = 'list_personalization_info';

    public function getListPersonalizationInfo($listId) {
    	$query = DB::table($this->table);
    	$query->where('list_id',$listId);
		
		$results = $query->get()->sortByDesc('id')->first();
		return $results;
    }
}
