<?php

namespace App\Http\Controllers;

use App\Models\HistoryAction;

class HistoryActionController extends Controller
{
    public function edit($id)
    {

        $history_action = HistoryAction::findOrFail($id);
        do_action("history_action_edit_action", $history_action);

        // make it global for accessing title and other data
        generateGlobalTitle($history_action);

        return view("dashboard.history_action_create", [
            'DB' => $history_action,
            'action' => __FUNCTION__,
            'route_args' => ['id' => $id]
        ]);
    }

    public function index()
    {
        do_action("histoy_action.list");
        $menu = HistoryAction::where("id", "!=" , 0);

        // make it global for accessing title and other data
        generateGlobalTitle(new HistoryAction);

        $filterHtml = getTableHeadHistoryAction();
        $menu = filterListHandler($menu, $filterHtml, []);

        return view("dashboard.history_action_list", [
            'DB' => $menu,
            'route_args' => []
        ]);
    }
}
