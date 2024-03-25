<?php

namespace App\Http\Controllers;

use App\Models\Form;
use App\Models\FormsSchema;

class FormController extends Controller
{
    public function create()
    {
        // make it global for accessing title and other data
        generateGlobalTitle(new FormsSchema);

        return view("dashboard.form_create", [
            'DB' => [],
            'action' => __FUNCTION__,
            'route_args' => []
        ]);
    }

    public function store()
    {
        $inputs = cleanTheArray(request()->post(), false);


        // handle export action
        $inputs = exportFormAction($inputs);

        do_action("before_form_schema_" . __FUNCTION__ . "_check", $inputs);

        if (!empty($inputs['type'])) {
            // make `type` sanitize
            $inputs['type'] = urlLikeSantize(["key" => "type", "value" => $inputs['type']], ["type"]);
        }

        // general validation
        $error = validationUserFormInputs($inputs, [
            "status" => getStatusPage()
        ], [new FormsSchema(), ["title", "type"]]);

        // custom validation
        if (empty($error['message'])) {
            $error = validationFormSchema($inputs);
        }

        if ($error['message']) {
            return triggerServerError($error);
        }

        do_action("after_form_" . __FUNCTION__ . "_check", $inputs);

        // form
        $formSchema = new FormsSchema();
        $new_formSchema = $formSchema->create([
            "title" => $inputs['title'],
            "is_login_required" => !empty($inputs['is_login_required']),
            "is_captcha_required" => !empty($inputs['is_captcha_required']),
            "type" => $inputs['type'],
            "schema" => $inputs['schema'],
            "status" => $inputs['status'],
        ]);

        do_action("form_schema_successfully_" . __FUNCTION__ . "d", $new_formSchema, $inputs);

        $link = getTypeEditLink($new_formSchema, "forms_schema", ["id"]);
        return redirect($link);
    }

    public function edit($id)
    {
        $formSchema = FormsSchema::where("id", $id)->get();
        $formSchema = $formSchema->first();

        abortByEntity($formSchema);

        // need redirect to edit again to remove `type`
        if (request()->query("type")) {
            $link = getTypeEditLink($formSchema, "forms_schema", ["id"]);
            return redirect($link);
        }

        do_action("form_schema_edit_action", $formSchema);

        // make it global for accessing title and other data
        generateGlobalTitle($formSchema);

        return view("dashboard.form_create", [
            'action' => __FUNCTION__,
            'DB' => $formSchema,
            'route_args' => [
                "id" => $id
            ]
        ]);
    }

    public function update($id)
    {
        $inputs = cleanTheArray(request()->post(), false);

        do_action("before_form_schema_" . __FUNCTION__ . "_check", $inputs);

        if (!empty($inputs['type'])) {
            // make `type` sanitize
            $inputs['type'] = urlLikeSantize(["key" => "type", "value" => $inputs['type']], ["type"]);
        }

        // general validation
        $error = validationUserFormInputs($inputs, [
            "status" => getStatusPage()
        ], [new FormsSchema(), ["title", "type"], $id]);

        // custom validation
        if (empty($error['message'])) {
            $error = validationFormSchema($inputs);
        }

        if ($error['message']) {
            return triggerServerError($error);
        }

        do_action("after_form_schema_" . __FUNCTION__ . "_check", $inputs);

        // form
        $formSchema = FormsSchema::findOrFail($id);
        $formSchema->update([
            "title" => $inputs['title'],
            "is_login_required" => !empty($inputs['is_login_required']),
            "is_captcha_required" => !empty($inputs['is_captcha_required']),
            "type" => $inputs['type'],
            "schema" => $inputs['schema'],
            "status" => $inputs['status'],
        ]);

        do_action("form_schema_successfully_" . __FUNCTION__ . "d", $formSchema, $inputs);

        $link = getTypeEditLink($formSchema, "forms_schema", ["id"]);
        return redirect($link);
    }

    public function index()
    {

        do_action("form_schema.list");

        $form_schema = FormsSchema::where("id", "!=", 0);

        $filterHtml = getTableHeadFormSchema();
        $form_schema = filterListHandler($form_schema, $filterHtml);

        // make it global for accessing title and other data
        generateGlobalTitle(new FormsSchema());

        return view("dashboard.form_schema_list", [
            'DB' => $form_schema,
            'route_args' => []
        ]);
    }

    public function index_form()
    {

        do_action("form.list");

        $form = Form::where("id", "!=", 0);

        $filterHtml = getTableHeadForm();
        $form = filterListHandler($form, $filterHtml);

        // make it global for accessing title and other data
        generateGlobalTitle(new Form());

        return view("dashboard.form_list", [
            'DB' => $form,
            'route_args' => []
        ]);
    }

    public function destroy_form()
    {
        return deleteType(getFullNamespaceByModel("Form", "findOrfail") , [] , "id");
    }

    public function destroy()
    {
        return deleteType(getFullNamespaceByModel("FormsSchema", "findOrfail"));
    }

    public function statusType()
    {
        $result = setStatusType("FormsSchema", "forms_schema");
        return $result;
    }

    public function statusTypeForm()
    {
        $result = setStatusType("Form", "forms" , [] , "id");
        return $result;
    }
}
