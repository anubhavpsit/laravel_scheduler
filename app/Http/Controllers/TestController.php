<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Campaigns;
use App\Models\CampaignSubscribers;
use App\Models\ListPersonalizationInfo;

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
        $batchId = 1611141000;
        
        $campaigns = new Campaigns();
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
            $campaignsData[$batchCampaign->campaign_id] = $campaigns->getCampaignDataById($batchCampaign->campaign_id);
        }


        //echo addEmailTrackingPixel('4', '32', 'test@gmail.com');
        echo "<pre>";
        //print_r($lists);
        foreach($batchData as $bData) {
            //print_r($lists[$bData->list_id]);
            //($bData->list_id, $bData->campaign_id, $bData->email)
            //print_r($campaignsData[$bData->campaign_id]->html_content);

            $pixel = $campaigns->createTrackingPixel($bData->list_id, $bData->campaign_id, $bData->email);
            //$c = $campaignsData[$bData->campaign_id]->html_content;
            $content = $campaigns->addTrackingPixel($campaignsData[$bData->campaign_id]->html_content, $pixel);
            //print_r($content);
            //$campaignsData[$bData->campaign_id]->html_content = $campaigns->addTrackingPixel($bData->list_id, $bData->campaign_id, $bData->email);
            //print_r($campaignsData[$bData->campaign_id]);
        }

    }

    //public function addPixel()
}




// [
//     {"index":0,"personlized_field":"{first_name, fallback=}","db_field":"first_name"},
//     {"index":1,"personlized_field":"{email, fallback=}","db_field":"email"},
//     {"index":2,"personlized_field":"{city, fallback=}","db_field":"field_2"}
// ]