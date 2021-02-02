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

    public function addTrackingLinks($htmlContent, $listId, $campaignId, $emailId) {

		$cLinksArr = $this->getCampaignLinks($campaignId);
		if (is_null($cLinksArr->campaign_links)) {
			return $htmlContent;
		}

		$cLinks = (array)json_decode($cLinksArr->campaign_links);
        $htmlDom = new \DOMDocument;
        $htmlDom->loadHTML($htmlContent);
        $links = $htmlDom->getElementsByTagName('a');
        $extractedLinks = array();
        foreach ($links as $link) {
            $linkHref = $link->getAttribute('href');
            $linkParts = explode("/",$linkHref);
            if(in_array(end($linkParts), array_keys($cLinks))) {
	            $link->setAttribute('href', $linkHref."/".base64_encode($campaignId."#".$listId."#".$emailId));
	            $link->setAttribute('target', "_blank");
            }
        }
        $htmlContent = $htmlDom->saveHTML();
        return $htmlContent;
    }

	public function getCampaignLinks($campaignId) {

        $query = DB::table($this->table);
        $query->where('campaign_id', $campaignId);
		$query->where('status',self::ACTIVE);

		$results = $query->get()->first();
		return $results;
	}
}
