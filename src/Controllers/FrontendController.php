<?php
namespace Respins\BaseFunctions\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Respins\BaseFunctions\Models\RawGameslist;
use Wave\Category;
use Respins\BaseFunctions\BaseFunctions;
use Respins\BaseFunctions\Controllers\DataController;

class FrontendController
{
    public function indexGames(Request $request, $slug)
    {
        if($slug === 'list') {
            return self::showGamesList();
        }
        
        if($slug === 'provider') {
            $data = DataController::selectProvider('bgaming');
            return $data;
            return self::showGamesList();
        }
        return BaseFunctions::errorRouting(404, 'Page not found.');
    }
 
    ## Populate Games to frontend
    # Validate then send along to specific game routing
    public function showGamesList($provider = NULL)
    {   
            if($provider !== NULL) {
                $check = RawGameslist::where('provider', '=', $slug)->firstOrFail();
                $games = RawGameslist::paginate(config('baseconfig.frontend.gameslist.games_per_page'));
                $games_count = RawGameslist::where('provider', '=', $slug);
                $categories = RawGames::rawProviders();
            } else {   
                $games = RawGameslist::where('provider', 'bgaming')->paginate(config('baseconfig.frontend.gameslist.games_per_page'));
                $games_count = RawGameslist::count();
                $thumbnail_prefix = config('baseconfig.frontend.thumbnail_cdn');
                $providers = json_decode(RawGameslist::rawProviders()) ?? NULL;
            }

            $seo = [
                'seo_title' => 'Games List',
                'seo_description' => 'List with games',
            ];
            
            $scaffold = [
                'layout' => 'respins::layout-extension-'.config('baseconfig.frontend.theme'),
                'show_provider_nav' => config('baseconfig.frontend.gameslist.show_provider_nav'),
                'show_header_text' => config('baseconfig.frontend.gameslist.show_header'),
                'show_extended_gameinfo' => config('baseconfig.frontend.gameslist.show_extended_gameinfo'),
            ];
        
        return view('respins::games-list', compact('games', 'games_count', 'thumbnail_prefix', 'providers', 'scaffold', 'seo'));
    }
    
    public function gameErrorPage()
    {   
        return BaseFunctions::errorRouting('400', 'Failed to load game.');
    }
    
    public function gameCategories()
    {   
        $category = Category::where('bonusplay', '=', $slug)->firstOrFail();
        $games = RawGameslist::where('provider', 'bgaming')->paginate(config('baseconfig.frontend.games_per_page'));
        $games_count = $games->count();
        $thumbnail_prefix = config('baseconfig.frontend.thumbnail_cdn');
        $categories = Category::all();

        return view('respins::games-list', compact('games', 'games_count', 'thumbnail_prefix', 'categories'));
    }

    public function gamesLauncher($slug)
    {   
        return view('respins::games-launcher')->with('slug', $slug);
    }
            /*


    public function gamesLauncher(Request $request)
    { 
        $content = $request->gamecontent;
        $appendJS = $request->appended_js;

                    return Blade::render(
                        'Hello, {{ $name }}',
                        ['name' => 'Julian Bashir'],
                        deleteCachedView: true
                    );
            <script>
                var app = <?php echo json_encode($array); ?>;
            </script><script>
            var app = <?php echo json_encode($array); ?>;
            </script>
        return view('respins::games-launcher')->with('content', $request);
    }
            */

}