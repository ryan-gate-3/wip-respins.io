<?php
namespace Respins\BaseFunctions\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Respins\BaseFunctions\Traits\ApiResponseHelper;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Support\Facades\Validator;
use Respins\BaseFunctions\BaseFunctions;

class EndpointRouter
{
    use ApiResponseHelper;

    ## Endpoint router is to validate and/or sanitize raw data from input to then send and return response from internal functions
    # You can extend this pretty advanced in-to different type of validation and returning this in various ways without any performance cost/loss
    # Try to only use php-only/local rulesets, like for example any external authentication or the curling of data should be done on the internal functions you call within this router.
 
    public function createPlayer(Request $request)
    {   
        $validate = $this->createPlayerValidation($request);
        if($validate->status() !== 200) {
            return $validate;
        }  
        
        $playerInsert = array(
            'pid' => $request->pid,
            'secret' => $request->secret ?? NULL,
            'nickname' => $request->nickname ?? NULL,
            'active' => true,
            'data' => [],
            'auth' => 'basic',
            'ownedBy' => 1,
        );

        return BaseFunctions::createPlayerFunction(json_encode($playerInsert));
    }

    # Input required: game (slug), currency (USD, EUR), mode (demo/real), player (id)
    public function createSession(Request $request)
    {   
        $validate = $this->createSessionValidation($request);
        if($validate->status() !== 200) {
            return $validate;
        }
    }

    ## Validations functions are stored below:
    # Used for various stuff
    public function createPlayerValidation(Request $request) {
        $validator = Validator::make($request->all(), [
            'pid' => ['required', 'min:4', 'max:100', 'regex:/^[^(\|\]`!%^&=};:?><’)]*$/'],
            'extra_id' => [ 'max:100', 'regex:/^[^(\|\]`!%^&=};:?><’)]*$/'],
            'nickname' => ['max:100', 'regex:/^[^(\|\]`!%^&=};:?><’)]*$/'],
            'secret' => ['max:50'],
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

        $this->respondOk();
    }

}
