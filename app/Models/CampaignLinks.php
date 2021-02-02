<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;

class CampaignLinks extends Model
{
    use HasFactory;

    protected $table = 'campaign_links';

    public const INACTIVE = 0;
    public const ACTIVE = 1;

}
