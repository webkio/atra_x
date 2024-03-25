<?php

namespace App\Http\Controllers;

use App\Models\View;

class ViewController extends Controller
{
   
    public function index()
    {
        do_action("view.list");
        $view = View::where("id", "!=" , 0);

        // make it global for accessing title and other data
        generateGlobalTitle(new View);

        $filterHtml = getTableHeadView();
        $view = filterListHandler($view, $filterHtml, []);

        return view("dashboard.view_list", [
            'DB' => $view,
            'route_args' => []
        ]);
    }
}
