<?php

namespace App\Http\Controllers;

use App\Models\Menu;


class MenuController extends Controller
{
    public function create()
    {
        // make it global for accessing title and other data
        generateGlobalTitle(new Menu);

        return view("dashboard.menu_create", [
            'menu' => getAllType('menu'),
            'DB' => [],
            'action' => __FUNCTION__,
            'route_args' => []
        ]);
    }

    public function store()
    {

        $inputs = cleanTheArray(request()->post(), false);

        do_action("before_menu_" . __FUNCTION__ . "_check", $inputs);

        $error = validationUserFormInputs($inputs, ["slug" => getMenuLocation()], [new Menu(), ['title', 'slug']]);

        // error handling
        $res = menuErrorHandler($inputs, $error);
        if (gettype($res) == strtolower('object')) {
            return $res;
        }

        $inputs['menu_items'] = json_encode($inputs['menu_items']??[]);

        do_action("after_menu_" . __FUNCTION__ . "_check", $inputs);

        $menu = new Menu();

        $new_menu = $menu->create([
            'title' => $inputs['title'],
            'slug' => $inputs['slug'],
            'menu_items' => $inputs['menu_items'],
        ]);

        do_action("menu_successfully_" . __FUNCTION__ . "d", $new_menu, $inputs);

        return redirect(getTypeEditLink($new_menu, $GLOBALS['current_page'], ['id']));
    }

    public function edit($id)
    {

        $menu = Menu::findOrFail($id);

        // make it global for accessing title and other data
        generateGlobalTitle($menu);

        do_action("menu_edit_action", $menu);

        return view("dashboard.menu_create", [
            'menu' => getAllType('menu'),
            'DB' => $menu,
            'action' => __FUNCTION__,
            'route_args' => ['id' => $id]
        ]);
    }

    public function update($id)
    {

        $inputs = cleanTheArray(request()->post(), false);
        $error = validationUserFormInputs($inputs, ["slug" => getMenuLocation()], [new Menu(), ['title', 'slug'], $id]);

        do_action("before_menu_" . __FUNCTION__ . "_check", $inputs);

        // error handling
        $res = menuErrorHandler($inputs, $error);
        if (gettype($res) == strtolower('object')) {
            return $res;
        }

        $inputs['menu_items'] = json_encode($inputs['menu_items']??[]);

        do_action("after_menu_" . __FUNCTION__ . "_check", $inputs);

        $menu = Menu::findOrFail($id);

        $menu->update([
            'title' => $inputs['title'],
            'slug' => $inputs['slug'],
            'menu_items' => $inputs['menu_items'],
        ]);

        do_action("menu_successfully_" . __FUNCTION__ . "d", $menu, $inputs);

        return redirect(getTypeEditLink($menu, $GLOBALS['current_page'], ['id']));
    }

    public function index()
    {

        do_action("menu.list");
        $menu = Menu::where("id", "!=" , 0);

        // make it global for accessing title and other data
        generateGlobalTitle(new Menu);

        $filterHtml = getTableHeadMenu();
        $menu = filterListHandler($menu, $filterHtml, []);

        return view("dashboard.menu_list", [
            'menu' => getAllType('menu'),
            'DB' => $menu,
            'route_args' => []
        ]);
    }

    public function destroy()
    {
        return deleteType(getFullNamespaceByModel('Menu' , "findOrFail"));
    }
}
