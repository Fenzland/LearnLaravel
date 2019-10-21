<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class Pages extends Controller
{

    /**
     * Show the welcome page.
     *
     * @return \Illuminate\Http\Response
     */
    public function page(string $page)
    {
        $viewFactory= app('view');
        
        if(! $viewFactory->exists($page)) {
            return abort(404);
        }
        
        return $viewFactory->make($page);
    }

}
