<?php

namespace Respins\BaseFunctions\Controllers\Livewire;

use Livewire\Component;
use Illuminate\Http\Request;
use Respins\BaseFunctions\Models\RawGameslist;

class GamesList extends Component
{

    public $games_list = [];
    public $games_count = 0 ;
    public $thumbnail_prefix;

    public function mount()
    {
        $this->games_list = self::games_list();
        $this->games_count = self::pagination_count();
        $this->thumbnail_prefix = config('baseconfig.frontend.thumbnail_cdn');
    }

    public function games_list()
    {
        $games = RawGameslist::where('provider', 'bgaming')->paginate(config('baseconfig.frontend.gameslist.games_per_page'));
        return array('games' => $games);
    } 

    public function pagination_count()
    {
        $games_count = RawGameslist::count();
        return $games_count;
    }

    public function render()
    {              
        return view('respins::livewire.games-list-component')->layout('respins::layout-extension-livewire');
    }
}
