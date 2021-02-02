<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Campaigns;
use App\Models\CampaignLinks;
use App\Models\CampaignSubscribers;
use App\Models\ListPersonalizationInfo;
use App\Models\ScheduleCampaignsToProcess;

class TestController extends Controller
{
    //

    public function testCalls() {
        $campaigns = new Campaigns();
        $campaigns->getScheduledCampaignsList();
    }

    public function sendTestEmail() {

    	// http://localhost:8080/send_test_mail
        $mailData = [];
        $mailData['name'] = 'Anubhav';
        $mailData['subject'] = 'Testing';

		\Mail::raw('This is a testing', function($message) {
		   $message->subject('Test email')->to('anubhav@test.com');
		});

        echo 'Email was sent';
    }

    public function test() {
        $batchId = 1612241094;
        //$batchId = 1612241096;
        
        $campaigns = new Campaigns();
        $scheduledCampaigns = new ScheduleCampaignsToProcess();

        $campaignLinks = new CampaignLinks();
        $campaignSubscribers = new CampaignSubscribers();
        $batchData = $campaignSubscribers->getBatchData($batchId);
        
        $batchLists = $campaignSubscribers->getBatchLists($batchId);
        $batchCampaigns = $campaignSubscribers->getBatchCampaigns($batchId);

        $listPersonalizationInfo = new ListPersonalizationInfo();

        $lists = [];
        $campaignsData = [];
        foreach($batchLists as $batchList) {
            $lpInfo = $listPersonalizationInfo->getListPersonalizationInfo($batchList->list_id);
            if(isset($lpInfo->list_personalizations)) {
                $batchList->list_personalizations = $lpInfo->list_personalizations;
            } else {
                $batchList->list_personalizations = '';
            }
            $lists[$batchList->list_id] = $batchList->list_personalizations;
        }

        foreach($batchCampaigns as $batchCampaign) {
            $campaignsData[$batchCampaign->campaign_id] = $scheduledCampaigns->getCampaignDataById($batchCampaign->campaign_id);
        }


        //echo addEmailTrackingPixel('4', '32', 'test@gmail.com');
        //echo "<pre>";
        //print_r($lists);
        foreach($batchData as $bData) {
            //print_r($lists[$bData->list_id]);
            //($bData->list_id, $bData->campaign_id, $bData->email)
            //print_r($campaignsData[$bData->campaign_id]->content);

            $pixel = $campaigns->createTrackingPixel($bData->list_id, $bData->campaign_id, $bData->email);
            //$c = $campaignsData[$bData->campaign_id]->content;
            $content = $campaigns->addTrackingPixel($campaignsData[$bData->campaign_id]->content, $pixel);

            $content = $campaignLinks->addTrackingLinks($content, $bData->list_id, $bData->campaign_id, $bData->email);

            $listPersonalizations = json_decode($lists[$bData->list_id]);
            preg_match_all('(\{\w+[, fallback=\w]+\})', $content, $matches);
            foreach($matches[0] as $m) {
                foreach($listPersonalizations as $listPersonalization){
                    if($m == $listPersonalization->personlized_field) {
                        $key = explode(",", str_replace(["{","}"], "", $m));
                        if(!isset($bData->{$listPersonalization->db_field}) || $bData->{$listPersonalization->db_field}=='') {
                            $replace_value = explode("=", $key[1])[1]; // setting fallback value here
                        } else {
                            $replace_value = $bData->{$listPersonalization->db_field}; // setting db value here
                        }
                        $content = str_replace($m, $replace_value, $content);
                    }
                }
            }
            print_r($content);
        }

    }

    //public function addPixel()
}




// [
//     {"index":0,"personlized_field":"{first_name, fallback=}","db_field":"first_name"},
//     {"index":1,"personlized_field":"{email, fallback=}","db_field":"email"},
//     {"index":2,"personlized_field":"{city, fallback=}","db_field":"field_2"}
// ]