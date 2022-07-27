<?php

namespace Respins\BaseFunctions\Controllers\Livewire;

use Livewire\Component;
use Respins\BaseFunctions\BaseFunctions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Respins\BaseFunctions\Controllers\API\SessionController;

class GamesLauncher extends Component
{

    public $slug;
    public $url = '';
    public $mobile;
    public $error = 0;
    public $name = 'undefined';
    public $provider = 'undefined';
    public $rtp;
    public $description;
    
    public function mount($slug)
    {
        $this->url = self::url($slug);
        
    }

    protected $listeners = ['refreshComponent' => '$refresh'];

    public function refreshComponent()
    {
        $this->postCount = Post::count();
    }

   public function request_session($game)
   {
        $data = [
            'game' => $game,
            'currency' => 'USD',
            'player' => 'playerrr',
            'operator_key' => 'operatokeyyy',
            'mode' => 'real',
            'request_ip' => '1.1.1.1',
        ];

        $session_create = SessionController::createSession($data);

        if($session_create['status'] === 'success') {
            return $session_create['message']['session_url'];
        } else {
            $game_error_page = env('APP_URL').'/web/respins.io/games/error';
            return $game_error_page;
        }
    }

 
    public function url($slug)
    {
        $game_url = self::request_session($slug);
        return $game_url;
    }

    public function render()
    {              
        return view('respins::livewire.launcher-component')->layout('respins::layout-extension-livewire');
    }
}
