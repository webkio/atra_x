<?php

namespace App\Http\Controllers;

use App\Models\Redirect;

class RedirectController extends Controller
{
    public function create()
    {
        // make it global for accessing title and other data
        generateGlobalTitle(new Redirect);

        return view("dashboard.redirect_create", [
            'DB' => [],
            'action' => __FUNCTION__,
            'route_args' => []
        ]);
    }

    public function store()
    {
        $inputs = cleanTheArray(request()->post(), false);

        do_action("before_redirect_" . __FUNCTION__ . "_check", $inputs);

        $error = validationUserFormInputs($inputs, [
            "http_code" => getHttpCodeRedirect()
        ]);

        if ($error['message']) {
            return triggerServerError($error);
        }

        do_action("after_redirect_" . __FUNCTION__ . "_check", $inputs);

        // remove `slash` from `start` and `end` url
        $this->sanitizeUrl($inputs);

        // redirect
        $redirect = new Redirect();
        $new_redirect = $redirect->create([
            "title" => $inputs['title'],
            "from" => $inputs['from'],
            "to" => $inputs['to'],
            "http_code" => $inputs['http_code'],
        ]);

        do_action("redirect_successfully_" . __FUNCTION__ . "d", $new_redirect, $inputs);

        return redirect(getTypeEditLink($new_redirect, "redirect", ["id"]));
    }

    public function edit($id)
    {

        $redirect = Redirect::findOrFail($id);

        // make it global for accessing title and other data
        generateGlobalTitle($redirect);

        do_action("redirect_edit_action", $redirect);

        return view("dashboard.redirect_create", [
            'DB' => $redirect,
            'action' => __FUNCTION__,
            'route_args' => ['id' => $id]
        ]);
    }

    public function update($id)
    {
        $inputs = cleanTheArray(request()->post(), false);

        do_action("before_redirect_" . __FUNCTION__ . "_check", $inputs);

        $error = validationUserFormInputs($inputs, [
            "http_code" => getHttpCodeRedirect()
        ]);

        if ($error['message']) {
            return triggerServerError($error);
        }

        do_action("after_redirect_" . __FUNCTION__ . "_check", $inputs);

        // remove `slash` from `start` and `end` url
        $this->sanitizeUrl($inputs);

        // redirect
        $redirect = Redirect::findOrFail($id);
        $new_redirect = $redirect->update([
            "title" => $inputs['title'],
            "from" => $inputs['from'],
            "to" => $inputs['to'],
            "http_code" => $inputs['http_code'],
        ]);

        do_action("redirect_successfully_" . __FUNCTION__ . "d", $new_redirect, $inputs);

        return redirect(getTypeEditLink($redirect, "redirect", ["id"]));
    }

    public function index()
    {

        do_action("redirect.list");
        $redirect = Redirect::where("id", "!=", 0);

        // make it global for accessing title and other data
        generateGlobalTitle(new Redirect);

        $filterHtml = getTableHeadRedirect();
        $redirect = filterListHandler($redirect, $filterHtml, []);

        return view("dashboard.redirect_list", [
            'redirect' => getAllType('redirect'),
            'DB' => $redirect,
            'route_args' => []
        ]);
    }

    public function destroy()
    {
        return deleteType(getFullNamespaceByModel("Redirect", "findOrfail"));
    }

    function sanitizeUrl(&$inputs)
    {
        $slash = "/";

        // remove more 2 or more "/"
        $subject_regex = "/\/{2,}/i";
        $inputs['from'] = preg_replace($subject_regex, "/", $inputs['from']);
        $inputs['to'] = preg_replace($subject_regex, "/", $inputs['to']);

        // remove slash from start and end if needed
        $inputs['from'] = $inputs['from'] == $slash ? $inputs['from'] : slashFixerPath($inputs['from']);
        $inputs['to'] = $inputs['to'] == $slash ? $inputs['to'] : slashFixerPath($inputs['to']);
    }
}
