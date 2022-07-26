<?php
namespace Respins\BaseFunctions\Controllers\Game;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Contracts\Support\Arrayable;
use Respins\BaseFunctions\Controllers\API\SessionController;
use Illuminate\Support\Facades\Http;
use Respins\BaseFunctions\BaseFunctions;
use Illuminate\Support\Facades\Log;

class BgamingController extends SessionController
{

    public static function generateSessionToken($game_id, $method) {
        
        if($method === 'demo_method') {
            $url = 'https://bgaming-network.com/play/'.$game_id.'/FUN?server=demo';
            $http_get = Http::retry(3, 5000)->get($url);
            return $http_get;
        }
        return 'generateSessionToken() method not supported';
    }
    # Disclaimer: this should be made into a job and/or contract on any type of high load
    # Also, we are to return an edited HTML object to be implemented within launcher, this should be normally cached or even hardcoded
    # This by far is most heavy PHP 'job' in any of this package, please read the OPTIMIZATIONS.MD for more info about PHP stack in general
    public static function requestSession($session = NULL) 
    {
        $proposed_session = $session; // validate this again if you multi-server setup between API & actual session creation jobs
        $select_session = SessionController::sessionData($session['player_id'], $session['token_internal']);
        if($select_session === false or !$select_session['session_data']) {
               return 'error'; // todo: invalidate session && return fake error page of game provider to player
        }
    
        $explode_game = explode('/', $select_session['session_data']['game_id_original']);
        $original_game_id = $explode_game[1];

        $generate_session = self::generateSessionToken($original_game_id, 'demo_method');

        if($generate_session->status() !== 200) {
            return 'error'; //didnt get 200 status code from page, probably did not succeed
        }

        $game_content = $generate_session;
        $origin_session_token = BaseFunctions::in_between('\"play_token\":\"', '\",\"', $game_content);

        if($origin_session_token === false) {
            Log::critical('Not being able to select play_token, even though the status & original game data seems correct. Possibly game source/structure has changed itself - disable game before proceeding to investigate thoroughly. '.json_encode($http_get));
            return 'TOKEN_NOT_SELECTABLE'; // insert error for not being able to select the actual game_session token from original data
        }

        $new_api_endpoint = config('gameconfig.bgaming.new_api_endpoint');
        #$parts = parse_url($redirectURL);
        #parse_str($parts['query'], $query);
        $token = $select_session['session_data']['token_internal'];
        $replaceAPItoOurs = str_replace('https://bgaming-network.com/api/', $new_api_endpoint, $game_content);
        $replaceAPItoOurs = str_replace('sentry.softswiss.net', 'blueoceangaming.net', $replaceAPItoOurs); // sentry removal
        $replaceAPItoOurs = str_replace('googletagmanager.com', 'blueoceangames.net', $replaceAPItoOurs); // sentry removal
        #$replaceAPItoOurs = str_replace('x3Cscript', ' ', $replaceAPItoOurs); // sentry removal
        $replaceAPItoOurs = str_replace('yes', ' ', $replaceAPItoOurs); // sentry removal

        //$replaceAPItoOurs = str_replace($origin_session_token, $token, $game_content);
        
        return $replaceAPItoOurs;
    }
 




}