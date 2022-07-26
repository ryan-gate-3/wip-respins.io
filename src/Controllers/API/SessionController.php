<?php
namespace Respins\BaseFunctions\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Respins\BaseFunctions\Traits\ApiResponseHelper;
use Respins\BaseFunctions\Controllers\DataController;
use Respins\BaseFunctions\Models\GameSessions;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use Respins\BaseFunctions\BaseFunctions;
use DB;

class SessionController
{
    use ApiResponseHelper;
 

    public function entrySession(Request $request)
    {
        $validate = $this->enterSessionValidation($request);
        if($validate->status() !== 200) {
            return $validate;
        }
        $player_id = $request->player_id;
        $token = $request->token;
        $entry_securekey = $request->entry;

        $select_session = SessionController::sessionData($request->player_id, $request->token);
        if($select_session === false) {
            return BaseFunctions::errorRouting(404, 'Session not found.');
        }
        $verify_signature = BaseFunctions::verify_sign($entry_securekey, $token);
        if($verify_signature === false) {
            return BaseFunctions::errorRouting(403, 'Entry invalid, create new session.');
        }

        $session_state_update = SessionController::sessionUpdate($request->player_id, $request->token, 'state', 'SESSION_ENTRY');
        if($session_state_update === false) {
            return BaseFunctions::errorRouting(400, 'Bad request. Not able to change session_state.');
        }
        
        $final_session_data = $session_state_update;

        $select_extra_meta = $final_session_data['extra_meta'];
        $game_controller = config('gameconfig.'.$select_extra_meta['provider'].'.controller');

        if(!$game_controller) {
            return BaseFunctions::errorRouting(400, 'Bad request. Failed to retrieve game controller, report to system admin.');
        }

        $game_launcher_behaviour = config('gameconfig.'.$select_extra_meta['provider'].'.launcher_behaviour');
        if(!$game_launcher_behaviour) {
            Log::critical('No launcher behaviour specified for method. Either disable games or add launcher behaviour to gameconfig.php. Session: '.json_encode($final_session_data));
            return BaseFunctions::errorRouting(400, 'Bad request. No launcher behaviour specified.');
        }
        
        $request_game_session = $game_controller::requestSession($final_session_data);

        if($request_game_session === false) {
            SessionController::sessionUpdate($player_id, $token, 'state', 'SESSION_FAILED');
            return BaseFunctions::errorRouting(400, 'Error trying to retrieve origin game, please refresh.');
        }

        if($game_launcher_behaviour === 'redirect') {
            SessionController::sessionUpdate($player_id, $token, 'state', 'SESSION_STARTED');
            return redirect($request_game_session);
        } 
        elseif($game_launcher_behaviour === 'internal_game') {
            SessionController::sessionUpdate($player_id, $token, 'state', 'SESSION_STARTED');
            return $request_game_session;
        } 
        else {
            Log::critical('Unsupported launcher configuration, set to either internal_game or redirect within gameconfig.php.');
            return BaseFunctions::errorRouting(400, 'Bad request. Unsupported launcher behaviour specified.');
        } 
    } 

    # Dummy example:
    # /api/respins.io/aggregation/createSession?game=softswiss:AlohaKingElvis&currency=USD&mode=real&player=croco&operator_key=1235523523523
    public function createSession(Request $request)
    {   
        $validate = $this->createSessionValidation($request);
        if($validate->status() !== 200) {
            return $validate;
        }

        /* IMPORTANT SELF NOTE V */        
        // todo, retrieve & validate operator auth: $operator_check = AuthController~~~
        $operator_id = $request->operator_key; // ^ to change to operator ID

        $collection = collect(DataController::getGames());
        $select_game = $collection->where('slug', $request->game)->where('internal_enabled', 1)->first();

        if(!$select_game) { // Game not found or enabled
            $search_disabled = $collection->where('slug', $request->game)->where('internal_enabled', 0)->first(); 
            if($search_disabled) {
                $prepareResponse = array('message' => 'Game found, however this game is disabled.', 'request_ip' => $request->ip());
            } else {
                $prepareResponse = array('message' => 'Game not found', 'request_ip' => $request->ip());
            }
            return $this->respondError($prepareResponse);
        }

        /* IMPORTANT SELF NOTE V */        
        // todo: $currency_check = self::currencyValidation();
        $currency = $request->currency; // ^ to change to validated currency symbol
        $player_id = $request->player;

        $invalidate_previous_init = self::invalidatePrev($player_id, $operator_id);
        if($invalidate_previous_init === false) { // Return error, as for some reason we were unable to invalidate previous sessions
            $prepareResponse = array('message' => 'Critical error, please contact your account manager ASAP. Try using different player_id.', 'request_ip' => $request->ip());
            return $this->respondError($prepareResponse);
        }

        $extra_meta = [
            'provider' => $select_game->provider,
            'mode' => $request->mode,
        ];
        $token_generation = Str::orderedUuid();
        $prepend_session_object = array(
            'player_id' => $player_id,
            'operator_id' => $operator_id,
            'game_id' => $select_game->slug,
            'extra_meta' => json_encode($extra_meta),
            'token_internal' => $token_generation,
            'currency' => $currency,
            'game_id_original' => $select_game->gid,
            'token_original' => 0,
            'games_amount' => 0,
            'expired_bool' => 0,
            'state' => 'SESSION_INIT',
            'created_at' => now(),
            'updated_at' => now(),
        );

        $insert = GameSessions::insert($prepend_session_object);
        $store_in_cache = Cache::put($token_generation, $prepend_session_object, now()->addMinutes(120)); //storing session in cache however still use fallover on db, memcached is preferred for game handling under high load, see OPTIMIZATIONS.MD
        $entry_signature = BaseFunctions::generate_sign($token_generation);
        $session_url = config('gameconfig.session_entry_url').'?token='.$token_generation.'&entry='.$entry_signature.'&player_id='.$player_id;
        
        $prepareResponse = array('message' => array('session_data' => $prepend_session_object, 'session_url' => $session_url), 'request_ip' => $request->ip());
        return $this->respondOk($prepareResponse);
    }

    public function invalidatePrev($player, $operator) 
    {
        try {
            GameSessions::where('player_id', $player)
            ->where('operator_id', $operator)
            ->where('expired_bool', 0)
            ->where('state', 'SESSION_INIT')
            ->update([
               'state' => 'SESSION_OVERRULE_INVALIDATION',
               'expired_bool' => 1,
            ]);
        } catch (\Exception $exception) {
            Log::critical('Error trying to invalidate older sessions, this should never error. Investigate:'.$exception);
            return false;
        }

         return true;
    }

    public static function sessionData($player_id, $token_internal) 
    {
        $retrieve_session_from_cache = Cache::get($token_internal);

        if ($retrieve_session_from_cache) {
            $response_data = array('data_retrieval_method' => 'cache', 'session_data' => $retrieve_session_from_cache);
        } elseif(!$retrieve_session_from_cache) {
            $retrieve_session_from_database = GameSessions::where('player_id', $player_id)
            ->where('token_internal', $token_internal)
            ->first();

            if($retrieve_session_from_database) {
                $response_data = array('data_retrieval_method' => 'database', 'session_data' => $retrieve_session_from_database);
            }
        } else {
            return false; //session not found, neither in cache as in database - create appriopate action in place you are dialing
        }

        return $response_data ?? false;
    }


    public static function sessionUpdate($player_id, $token_internal, $key, $newValue) 
    {
        $retrieve_session_from_database = GameSessions::where('player_id', $player_id)
        ->where('token_internal', $token_internal)
        ->first();

        if(!$retrieve_session_from_database) {
            //Session not found
            return false;
        }

        try {
            $new = $retrieve_session_from_database->update([
                $key => $newValue
            ]);
        } catch (\Exception $exception) {
            Log::critical('Database error, most likely you are trying to update a non existing key/field, or cache is mismatched (token: '.$token_internal.') - clearing this key. Investigate asap. Error: '.json_encode($exception));
            Cache::pull($token_internal);
            return false;
        }

        $data = $retrieve_session_from_database;
        $data[$key] = $newValue;

        $store_in_cache = Cache::put($token_internal, $data, now()->addMinutes(120)); 
        return $data;

    }


    public function enterSessionValidation(Request $request) {
        $validator = Validator::make($request->all(), [
            'entry' => ['required', 'min:10', 'max:100'],
            'token' => ['required', 'min:10', 'max:100'],
            'player_id' => ['required', 'min:3', 'max:100'],
        ]);

        $ip = $_SERVER['REMOTE_ADDR'];
        if($ip === NULL || !$ip) {
            $ip = $request->header('CF-Connecting-IP');
            if($ip === NULL) {
              $ip = $request->ip();  
            }
        }

        if ($validator->stopOnFirstFailure()->fails()) {
            $errorReason = $validator->errors()->first();
            $prepareResponse = array('message' => $errorReason, 'request_ip' => $ip);
            return $this->respondError($prepareResponse);
        }

        return $this->respondOk();

    }

    public function createSessionValidation(Request $request) {
        $validator = Validator::make($request->all(), [
            'game' => ['required', 'max:30', 'min:3'],
            'currency' => ['required', 'min:2', 'max:40'],
            'player' => ['required', 'min:3', 'max:100', 'regex:/^[^(\|\]`!%^&=};:?><’)]*$/'],
            'currency' => ['required', 'min:2', 'max:7'],
            'operator_key' => ['required', 'min:10', 'max:50'],
            'mode' => ['required', 'min:2', 'max:15'],
        ]);

        $ip = $_SERVER['REMOTE_ADDR'];
        if($ip === NULL || !$ip) {
            $ip = $request->header('CF-Connecting-IP');
            if($ip === NULL) {
              $ip = $request->ip();  
            }
        }

        if ($validator->stopOnFirstFailure()->fails()) {
            $errorReason = $validator->errors()->first();
            $prepareResponse = array('message' => $errorReason, 'request_ip' => $ip);
            return $this->respondError($prepareResponse);
        }

        if($request->mode !== 'real') {
            $prepareResponse = array('message' => 'Mode can only be \'demo\' or \'real\'.', 'request_ip' => $ip);
            return $this->respondError($prepareResponse);
        }

        return $this->respondOk();
    }

}