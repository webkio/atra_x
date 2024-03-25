<?php

namespace App\Http\Controllers;



class CheckController extends Controller
{

    private $allowed_column_list = [
        "User" => ["action" => [
            "sign_up" => [
                "elements" => ["username", "email" , "phone"],
                "default" => [],
                "condition" => null
            ],
            "reset_password" => [
                "elements" => ["username", "email" , "phone"],
                "default" => [],
                "condition" => null
            ]
        ]],
        "OTP" => ["action" => [
            "verify_email" => [
                "elements" => ["id", "client_code" , "expired"],
                "default" => ["expired" => 0],
                "condition" => "where",
            ]
        ]],
        "File" => ["action" => [
            "check_file" => [
                "elements" => ["url"],
                "default" => [],
                "condition" => null,
            ]
        ]],
    ];

    public function show($model_name, $action , $overloadedData = [])
    {
        $the_model_name = null;
        $the_action_item = null;

        // validate action AND get exact model name 
        foreach ($this->allowed_column_list as $class_item_name => $class_item) {
            $current_action_item = $class_item['action'];
            if (strtolower($class_item_name) == strtolower($model_name) && in_array($action, array_keys($current_action_item))) {
                $the_action_item = $current_action_item[$action];
                $the_model_name = $class_item_name;
                break;
            }
        }

        // abort in not found model name in allowed list
        if (empty($the_model_name) || empty($the_action_item)) abort(403);


        $class_name = "App\Models\\" . $the_model_name;

        // abort if class not exists
        if (!class_exists($class_name)) abort(403);

        $model = new $class_name;

        
        $requested_fields = $overloadedData ? $overloadedData : request()->post();
        $finall_requested_fields = [];

        $allowed_column_list = $the_action_item['elements'] ?? false;

        if (!$allowed_column_list) dd("list not found");

        if(is_null($the_action_item['condition'])){
            $tmp_list = [];
            foreach ($requested_fields as $key_field => $field) {
                if (in_array($key_field, $allowed_column_list)){
                    $tmp_list[] = $key_field;
                }
            }

            if($tmp_list){
                $allowed_column_list = $tmp_list;
            }
        }

        do_action("before_column_check" , $model , $requested_fields);
        do_action("before_column_check_" . strtolower($model_name) , $model , $requested_fields);

        // default value
        foreach($the_action_item['default'] as $default_key => $default_value){
            if(!isset($requested_fields[$default_key])){
                $requested_fields[$default_key] = $default_value;
            }
        }

        foreach ($requested_fields as $key_field => $field) {
            if (in_array($key_field, $allowed_column_list) && (!empty($field) || $field == "0")) {
                $finall_requested_fields[$key_field] = $field;
            }
        }

        $allowed_column_diff = array_diff($allowed_column_list, array_keys($finall_requested_fields));


        if ($allowed_column_diff) abort(403, "Required Field/s " . join(", ", $allowed_column_diff));

        do_action("before_exists_check" , $model , $requested_fields , $finall_requested_fields);
        do_action("before_exists_check_" . strtolower($model_name) , $model , $requested_fields , $finall_requested_fields);

        $existsData = getExistsDataTable($finall_requested_fields, [$model, array_keys($finall_requested_fields)], $the_action_item['condition']);

        $isExists = $existsData == true;

        do_action("after_exists_check" , $model , $isExists , $requested_fields , $finall_requested_fields);
        do_action("after_exists_check_" . strtolower($model_name) , $model , $isExists  , $requested_fields , $finall_requested_fields);


        return [
            "isExists" => $isExists,
        ];
    }

    public function baseDataValidation($callback , $allowed_list){

        $the_callback = strtolower($callback);
        $callback_str = $allowed_list[$the_callback] ?? "";

        if(empty($callback_str)){
            abort(403);
        }

        if(!is_callable($callback_str)){
            abort(404);
        }

        return $callback_str;
    }

    public function showData($callback){
        $allowed_list = [
            "generatecaptcha" => "generateCaptcha",
            "gettaxonomyrest" => "getTaxonomyRest",
            "getfilerest" => "getFileRest",
            "setcookie_policy" => "setCookie_Policy",
            "getcommentrest" => "getCommentRest",
            "sendemailrest" => "sendEmailRest",
            "cloneposttyperest" => "clonePostTypeRest",
        ];

        $callback_str = $this->baseDataValidation($callback , $allowed_list);

        return [
            "data" => $callback_str()
        ];
    }

    public function setData($callback){
        $allowed_list = [
            "settaxonomyrest" => "setTaxonomyRest",
        ];

        $callback_str = $this->baseDataValidation($callback , $allowed_list);

        return $callback_str();
        
    }
}
