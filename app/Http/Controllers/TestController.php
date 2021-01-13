<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TestController extends Controller
{
    //

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

}
