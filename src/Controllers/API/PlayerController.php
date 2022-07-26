<?php
namespace Respins\BaseFunctions\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PlayerController
{
	public static function createPlayerFunction($data) {
		$data = json_decode($data, true);
		if($data['data'] === NULL) {
			$data = array();
		}
		if($data['secret'] === NULL) {
			$secret = Str::random(32);
		}

        /*
		if($data['auth'] === 'basic') {
			if(!auth()->user()) {
				return self::errorRouting(401, 'Failed to create player. Utilize API auth method or else (re)login first to your admin account and retry.');
			}
			$auth = auth()->user();

		} elseif($data['auth'] === 'api') {
			// Insert later api auth here and select the user id of
		}
        $auth =  \Auth::user()->id;
        */

		$player = new Players();
		$player->uuid = Str::orderedUuid();
		$player->pid = $data['pid'];
		$player->secret = $secret;
		$player->active = true;
		$player->data = $data['data'];
		$player->ownedBy = $data['ownedBy'];
		$player->timestamps = true;
		$player->save();

        return $player->makeVisible(['uuid'])->makeVisible(['secret']);
	}

}