<?php
namespace Respins\BaseFunctions\Controllers\Game;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use Respins\BaseFunctions\Traits\ApiResponseHelper;

class BlueoceanController
{
    use ApiResponseHelper;

    public function rawListing(Request $request)
    {
        $url = 'https://stage.game-program.com/api/seamless/provider';
        $data = array(
            "api_password" => '8P3gmMEoZPlhdVaoIs',
            "api_login" => 'rxcgames_mc_s',
            "method" => "getGameList",
            "show_systems" => 0, //if false, parameter is not needed
            "show_additional" => false, //if false, parameter is not needed
            "currency" => "USD"
        );

        // Send off
        $liftoff = Http::withBody(
            json_encode($data), 'application/json'
        )->post($url);

        if($liftoff === NULL) {
            $errorResponse = array('message' => 'Error retrieving games it seems, please check the sourcing used.');
            return $this->respondError($errorResponse);
        }

        // Decode the liftoff response 
        $decodedLiftoff = json_decode($liftoff, true);
        
        if($decodedLiftoff === NULL) {
            $errorResponse = array('message' => 'Error retrieving games it seems, please check the sourcing used.');
            return $this->respondError($errorResponse);
        } elseif(!$decodedLiftoff) {
            $errorResponse = array('message' => 'Error retrieving games it seems, please check the sourcing used.');
            return $this->respondError($errorResponse);
        }

        $gamePrep = array(
            'games' => $decodedLiftoff,
        );
        $object = array('message' => $gamePrep);

        return $this->respondWithSuccess($object);
    }

}

