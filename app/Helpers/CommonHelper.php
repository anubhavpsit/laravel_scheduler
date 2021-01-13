<?php
namespace App\Helpers;
use Storage;
use Carbon\Carbon;

class CommonHelper
{
    static function bob()
    {
        return 'Hiiiiiiiiiiii';
    }

    static function createAvtar($name, $userId) {

		
		$nameE = explode(" ", $name);
		$length = 1;
		if(sizeof($nameE) > 1) {
			$length = 2;
		}
		$avatar = new \LasseRafn\InitialAvatarGenerator\InitialAvatar();
		$img =  $avatar->name($name)
		->length($length)
		->fontSize(0.5)
		->size(30)
		->background(self::random_color())
		->color('#fff')
		->rounded()
		->smooth()
		->generate()
		->stream('png', 100);
		$avatarName = "public/avatars/".$userId."_avatar".time().".png";
		Storage::disk('local')->put($avatarName, $img);
		return $userId.'_avatar'.time().'.png';
    }

	static function random_color() {
		$color = ['#ffb400','#e58637','#d6423b','#b41039','#420c30','#00f9ff','#00d2ff','#009fff','#0078ff','#0051ff','#66545e','#a39193','#aa6f73','#eea990','#f6e0b5','#ed5565','#f8ac59','#23c6c8','#1ab394','#1c84c6','#b2d8d8','#66b2b2','#008080','#006666','#004c4c'];
		$random_key=array_rand($color,1);
		return $color[$random_key];
	}

	static function getTodayDateTime() {
		$mutable = Carbon::now();
		return $mutable->toDateTimeString(); 
	}

	static function getTodayDate() {
		$mutable = Carbon::now();
		return $mutable->toDateString(); 
	}	
}
