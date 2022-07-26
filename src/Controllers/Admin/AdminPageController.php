<?php
namespace Respins\BaseFunctions\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AdminPageController
{
    public static function pages() 
    {
        $pages = config('adminconfig.pages');
        $collection = collect($pages);
        return $collection;
    }

    public function globals()
    {       
        $globals = [
            'pages' => self::pages(),
        ];
        return $globals;
    }

    public function enter($slug, Request $request)
    {   
        $pages_collection = self::pages();
        $select = $pages_collection->where('slug', $slug)->first();

        if(!$select) {
            return 'page not found';
        }

        if(auth()->guest()) {
            return 'plx login';
        }

        if($request->action) {
            return self::command($slug, $request);
        }

        $auth = [
            'user-id' => auth()->user()->id,
            'user-name' => auth()->user()->name,
            'user-email' => auth()->user()->email,
        ];

        $page_controller = $select['controller'];
        $page_meta = $page_controller::meta();
        $page_actions = $page_controller::actions();
        $page_view = $select['view'];
        $page_icon = $page_controller::icon();

        $scaffold = [
            'title' => env('APP_NAME'),
            'pages' => $pages_collection,
            'icon' => $page_icon,
            'layout' => 'respins::layout-extension-admin',
        ];

        $seo = [
            'seo_title' => $select['title'],
            'seo_description' => $select['description'],
        ];

        return view($page_view, compact('auth', 'seo', 'scaffold', 'page_meta', 'page_actions'));

    }

    public function command($slug, $request)
    {
        

        return redirect('/page/respins.io/admin/dashboard')->with(['message' => 'Successfully switched.', 'message_type' => 'success']);


    }



}