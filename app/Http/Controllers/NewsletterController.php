<?php

namespace App\Http\Controllers;

use App\Models\Newsletter;

class NewsletterController extends Controller
{
    public function store()
    {

        $inputs = cleanTheArray(request()->post(), false);
        $inputs['ip'] = request()->ip();

        $typeList = getNewsletterType();

        // dynamically change validation by type
        if(!empty($inputs['type']) && !empty($inputs['client_id']) && in_array($inputs['type'] , array_keys($typeList))){
            if($inputs['type'] == "email"){
                removeRequireInputToJsonKeyMap($GLOBALS['current_page'] , ['client_id']);
                
                $list = $GLOBALS['newsletter_valid_domain'];
                $label = is_array($list) ? join(" , " , $list) : "any valid domain";
                $label = "({$label})";

                addRequireInputToJsonKeyMap($GLOBALS['current_page'] , ["client_id" => ["newsletter_email:{$label}","max:90"]]);
            }
        }

        do_action("before_newsletter_" . __FUNCTION__ . "_check", $inputs);

        $error = validationUserFormInputs($inputs, ["type" => $typeList], [new Newsletter(), ['client_id']]);

        if ($error['message']) {
            return triggerServerError($error);
        }

        do_action("after_newsletter_" . __FUNCTION__ . "_check", $inputs);

        $newsletter = new Newsletter();

        $new_newsletter = $newsletter->create([
            'type' => $inputs['type'],
            'client_id' => $inputs['client_id'],
            'ip' => $inputs['ip'],
        ]);

        do_action("newsletter_successfully_" . __FUNCTION__ . "d", $new_newsletter, $inputs);

        return triggerServerError(getUserMessageValidate(getMessageJoined() , ["client_id"]));
    }

    public function edit($id)
    {

        $newsletter = Newsletter::findOrFail($id);

        // make it global for accessing title and other data
        generateGlobalTitle($newsletter);

        do_action("newsletter_edit_action", $newsletter);

        return view("dashboard.newsletter_create", [
            'DB' => $newsletter,
            'action' => __FUNCTION__,
            'route_args' => ['id' => $id]
        ]);
    }

    public function index()
    {

        do_action("newsletter.list");
        $newsletter = Newsletter::where("id", "!=" , 0);

        // make it global for accessing title and other data
        generateGlobalTitle(new Newsletter);

        $filterHtml = getTableHeadNewsletter();
        $newsletter = filterListHandler($newsletter, $filterHtml, []);

        return view("dashboard.newsletter_list", [
            'DB' => $newsletter,
            'route_args' => []
        ]);
    }

    public function destroy()
    {
        return deleteType(getFullNamespaceByModel('Newsletter' , "findOrFail") , [] , "client_id");
    }
}
