<?php
namespace Respins\BaseFunctions\Controllers\Data;

use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use DB;
use Respins\BaseFunctions\BaseFunctions;
use Respins\BaseFunctions\Traits\ApiResponseHelper;
use Respins\BaseFunctions\Models\RawGameslist;
use Respins\BaseFunctions\Models\Gameslist;

class GameImporterController
{
    use ApiResponseHelper;

    public static function seed_development()
    {
        $storageGamelist = 'https://gitlab.freedesktop.org/ryan-gate-2/casino-montik/-/raw/main/games255__2_.json';
        $getGames = Http::get($storageGamelist);
        $originTarget = 'bets.io';
        $getGamesDecode = json_decode($getGames, true);

        foreach ($getGamesDecode as $gameID => $data) {
            $explodeSSid = explode('/', $gameID);
            $bindTogether = $explodeSSid[0].':'.$explodeSSid[1];
            $typeGame = 'generic';
            $demoMode = 0;
            $demoPrefix = 0;
            $typeRatingGame = 0;
            $internal_origin_realmoneylink = [];

            if(isset($data['demo'])) {
                $demoMode = true;
                $demoPrefix = urldecode($data['demo']);
                if($originTarget === 'bitstarz.com') {
                    $demoPrefix = str_replace('http://bitstarz.com', '', $demoPrefix);
                }
            }

            if(isset($data['real'])) {
                $internal_origin_realmoneylink = $data['real'];        
            }

            $stringifyDetails = json_encode($data['collections']);        
            if(str_contains($stringifyDetails, 'slots')) {
                $typeGame = 'slots';
                if(isset($data['collections']['slots'])) {
                    $typeRatingGame = $data['collections']['slots'];
                } else {
                    $typeRatingGame = 100;
                }
            }

            if(str_contains($stringifyDetails, 'live')) {
                $typeGame = 'live';
                if(isset($data['collections']['live'])) {
                    $typeRatingGame = $data['collections']['live'];
                } else {
                    $typeRatingGame = 100;
                }
            }

            if(str_contains($stringifyDetails, 'bonusbuy')) {
                $hasBonusBuy = 1;
            } else {
                $hasBonusBuy = 0;
            }

            if(str_contains($stringifyDetails, 'jackpot')) {
                $hasJackpot = 1;
            } else {
                $hasJackpot = 0;
            }

            if($data['provider'] === 'bgaming') {
                $internal_enabled = 1;
            } else {
                $internal_enabled = 0;
            }

            $prepareArray = array(
                'gid' => $gameID,
                'slug' => $bindTogether,
                'name' => $data['title'],
                'provider' => $data['provider'],
                'type' => $typeGame,
                'typeRating' => $typeRatingGame,
                'popularity' => $data['collections']['popularity'],
                'bonusbuy' => $hasBonusBuy,
                'jackpot' => $hasJackpot,
                'demoplay' => $demoMode,
                'internal_origin_demolink' => $demoPrefix,
                'internal_origin_casino' => $originTarget,
                'internal_origin_realmoney' => json_encode($internal_origin_realmoneylink),
                'internal_origin_rawobject' => json_encode($data),
                'internal_enabled' => $internal_enabled,
            );

            $gameArray[] = $prepareArray;
            RawGameslist::insert($prepareArray);
        }


        return true;
    }



    public function gamelistImporter(Request $request)
    {
        // Origin target right now 1 of 3, for demo games best is bets.io as being fastest & probably most accurate
        if(!$request->origin_target) {
            $errorResponse = array('message' => 'Incorrect origin_target specified, pick between: bets.io | bitstarz.com | duxcasino.com');
            return $this->respondError($errorResponse);
        } 

        // Variables for origin target, these should be stored locally instead as they won't change and regardless if they do it would need manual intervention anyway, the goal anyways is that you should build maybe 10 centrifuge/session sources so that way you are safe and system can automatically select games/source that is working
        if(str_contains('bets.io', $request->origin_target)) {
            $originTarget = 'bets.io';
            $apiLocationGamelist = 'https://bets.io/api/games/allowed_desktop';
            $storageGamelist = 'https://gitlab.freedesktop.org/ryan-gate-2/casino-montik/-/raw/main/games255__2_.json';
        } elseif(str_contains('duxcasino.com', $request->origin_target)) {
            $originTarget = 'duxcasino.com';
            $apiLocationGamelist = 'https://www.duxcasino.com/api/games/allowed_desktop';
            $storageGamelist = 'https://gitlab.freedesktop.org/ryan-gate-2/casino-montik/-/raw/main/gamesdux.json'; 
        } elseif(str_contains('bitstarz.com', $request->origin_target)) {
            $originTarget = 'bitstarz.com';
            $apiLocationGamelist = 'https://www.bitstarz.com/api/games/allowed_desktop';
            $storageGamelist = 'https://pix-api.pointdns.rest/games-bitstarz.json';
            //$storageGamelist = 'https://gitlab.freedesktop.org/ryan-gate-2/casino-montik/-/raw/main/games_bitstarz.json'; 
        } else {
            $errorResponse = array('message' => 'Incorrect origin_target specified, pick between: bets.io | bitstarz.com | duxcasino.com');
            return $this->respondError($errorResponse);
        }

        // Check if request wants us to proxy the gamelist from proxy server (Germany specified because most likely German geo ip's have more access on Softswiss products due 90% being hosted within hetzner.de cheap boxes @ softswiss)
        if($request->origin_proxied === 1) {
        $getGames = Http::withHeaders([
            'check-url' => $apiLocationGamelist
        ])->get('http://vps-70325c4a.vps.ovh.net/api/gamelist');
        } else {
            $getGames = Http::get($storageGamelist);
        }

        $getGamesDecode = json_decode($getGames, true);

        if($getGamesDecode === NULL) {
            $errorResponse = array('message' => 'Error retrieving games it seems, please check the sourcing used.');
            return $this->respondError($errorResponse);
        }

        // Check if request wants to truncate previous mySQL list entries
        if(isset($request->clean)) {
            RawGameslist::where('internal_origin_casino', $originTarget)->delete();
        }

        foreach ($getGamesDecode as $gameID => $data) {
            $explodeSSid = explode('/', $gameID);
            $bindTogether = $explodeSSid[0].':'.$explodeSSid[1];
            $typeGame = 'generic';
            $demoMode = 0;
            $demoPrefix = 0;
            $typeRatingGame = 0;
            $internal_origin_realmoneylink = [];

            if(isset($data['demo'])) {
                $demoMode = true;
                $demoPrefix = urldecode($data['demo']);
                if($originTarget === 'bitstarz.com') {
                    $demoPrefix = str_replace('http://bitstarz.com', '', $demoPrefix);
                }
            }

            if(isset($data['real'])) {
                $internal_origin_realmoneylink = $data['real'];        
            }

            $stringifyDetails = json_encode($data['collections']);        
            if(str_contains($stringifyDetails, 'slots')) {
                $typeGame = 'slots';
                if(isset($data['collections']['slots'])) {
                    $typeRatingGame = $data['collections']['slots'];
                } else {
                    $typeRatingGame = 100;
                }
            }

            if(str_contains($stringifyDetails, 'live')) {
                $typeGame = 'live';
                if(isset($data['collections']['live'])) {
                    $typeRatingGame = $data['collections']['live'];
                } else {
                    $typeRatingGame = 100;
                }
            }

            if(str_contains($stringifyDetails, 'bonusbuy')) {
                $hasBonusBuy = 1;
            } else {
                $hasBonusBuy = 0;
            }

            if(str_contains($stringifyDetails, 'jackpot')) {
                $hasJackpot = 1;
            } else {
                $hasJackpot = 0;
            }

            $prepareArray = array(
                'gid' => $gameID,
                'slug' => $bindTogether,
                'name' => $data['title'],
                'provider' => $data['provider'],
                'type' => $typeGame,
                'typeRating' => $typeRatingGame,
                'popularity' => $data['collections']['popularity'],
                'bonusbuy' => $hasBonusBuy,
                'jackpot' => $hasJackpot,
                'demoplay' => $demoMode,
                'internal_origin_demolink' => $demoPrefix,
                'internal_origin_casino' => $originTarget,
                'internal_origin_realmoney' => json_encode($internal_origin_realmoneylink),
                'internal_origin_rawobject' => json_encode($data),
                'internal_enabled' => 0,
            );

            $gameArray[] = $prepareArray;

            if(isset($request->import)) {
                RawGameslist::insert($prepareArray);
            }

        }

        ## Return and show the output
        if(isset($request->raw_list_output)) {
            return response()->json($getGamesDecode, 200);
        }

        return response()->json($gameArray, 200);
    }


}