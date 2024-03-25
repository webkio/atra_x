<?php

namespace App\Http\Controllers;

use App\Models\User;

class FrontEndController extends Controller
{
    public function index_home()
    {
        // make it global for accessing title and other data
        generateGlobalTitle(["title" => $GLOBALS['site_description_raw'], "body_raw" => $GLOBALS['site_description_raw'], "thumbnail_url" => $GLOBALS['favicon_url'], "created_at" => getDateByUnixTime(null, filectime(getSiteManifestTemplateURL("path"))), "updated_at" => getDateByUnixTime(null, filemtime(getSiteManifestTemplateURL("path"))), "fake_class" => "Home"]);

        return view("frontend.home", [
            'DB' => [],
            'route_args' => []
        ]);
    }

    public function single_post_type($type, $id, $slug = null)
    {
        $info = checkPostType($type);
        $post_type = getSinglePostTypeQuery($id, $type);

        // draft preview limitation by role
        if (auth()->check()) {
            $permissionEdit = postTypePermission($type);

            if (isPostTypeForbidden($permissionEdit)) {
                $post_type = $post_type->where("status", "publish");
            }
        } else {
            $post_type = $post_type->where("status", "publish");
        }

        $post_type = $post_type->withAvg("comments_rating", "rating")->get()->first();

        abortByEntity($post_type);
        abortByUnPublicType(isDynamicTypePublic($info, "current_post_type_info"));

        // make it global for accessing title and other data
        generateGlobalTitle($post_type, [
            "type" => $type,
            "typeLabel" => checkPostType($type)['current_post_type_info']['label']
        ]);

        if (!$slug || $slug != getTypeSlug($post_type)) {
            return redirect(getPostTypeLink($post_type), 301);
        }

        // dynamic view
        // Priority (1-id , 2-slug, 3-type)
        $view_name = getViewFileBasedOnPriority("frontend.post_type@_single", [getTypeID($post_type), getTypeSlug($post_type), $info['current_post_type']]);


        return view($view_name, [
            'post_type' => $info['current_post_type'],
            'post_type_data' => $info['current_post_type_info'],
            'DB' => $post_type,
            'route_args' => [
                'type' => $type,
                'id' => $id,
                'slug' => $slug,
            ]
        ]);
    }

    public function index_taxonomy($type, $id, $slug = null)
    {
        $info = checkTaxonomy($type);
        $taxonomy = getSingleTaxonomyQuery($id, $type);

        // draft preview limitation by role
        if (auth()->check()) {
            if (!getCurrentUserRolesDetailsCanAccess("taxonomy.edit")) {
                $taxonomy = $taxonomy->where("status", "publish");
            }
        } else {
            $taxonomy = $taxonomy->where("status", "publish");
        }

        $taxonomy = $taxonomy->get()->first();

        abortByEntity($taxonomy);
        abortByUnPublicType(isDynamicTypePublic($info, "current_taxonomy_info"));


        $post_type_query = $taxonomy->post_types()->where("status", "publish");
        $queryBeforeFilter = getQueries($post_type_query);

        $post_type_query = apply_filters("post_types_in_taxonomy_{$type}", $post_type_query);

        $queryAfterFilter = getQueries($post_type_query);

        if ($queryBeforeFilter == $queryAfterFilter) {
            $post_type_query->latest();
        }

        $taxonomy->post_types = $post_type_query->paginate(getAllType("type_per_page"));


        // make it global for accessing title and other data
        generateGlobalTitle($taxonomy, [
            "type" => $type,
            "typeLabel" => checkTaxonomy($type)['current_taxonomy_info']['label']
        ]);


        if (!$slug || $slug != getTypeSlug($taxonomy)) {
            return redirect(getTaxonomyLink($taxonomy), 301);
        }

        // dynamic view
        // Priority (1-id , 2-slug, 3-type)
        $view_name = getViewFileBasedOnPriority("frontend.taxonomy_index@", [getTypeID($taxonomy), getTypeSlug($taxonomy), $info['current_taxonomy']]);

        return view($view_name, [
            'taxonomy' => $info['current_taxonomy'],
            'taxonomy_data' => $info['current_taxonomy_info'],
            'DB' => $taxonomy,
            'route_args' => [
                'type' => $type,
                'id' => $id,
                'slug' => $slug,
            ]
        ]);
    }

    public function show_form($type, $id = 0)
    {

        $formSchema = getFormSchemaByType($type);

        abortByEntity($formSchema);

        // make it global for accessing title and other data
        generateGlobalTitle(["title" => getTypeTitle($formSchema), "fake_class" => "FormsSchema"]);

        // dynamic view
        $sanitized_type = sanitizeDashType($type);
        $view_name = getDynamicViewType("frontend.form_show", "frontend.form_{$sanitized_type}_show");

        $form_content = "";

        return view($view_name, [
            "DB" => $formSchema,
            "id" => $id,
            "form_content" => $form_content,
            'route_args' => [
                'type' => $type,
            ]
        ]);
    }

    public function send_form($type)
    {
        $inputs = cleanTheArray(request()->post(), false);

        $input_files = request()->file();
        $filesObj = [];

        if ($input_files) {
            foreach ($input_files as $input_file_name => $input_file_Obj) {
                $filesObj[] = $input_file_Obj;
            }

            // add files to MAIN inputs
            $inputs = array_merge($inputs, $input_files);
        }

        $inputs = makeJsonEncodeIfWasArray($inputs);
        $formSchema = getFormSchemaByType($type);

        abortByEntity($formSchema);

        $currentUser = getCurrentUser();

        if ($formSchema['is_login_required'] && !$currentUser) {
            abort(403, getMessageMustBeLogginToDoThisAction());
        }

        // check captcha
        if ($formSchema['is_captcha_required']) {
            if (!validateCaptcha()) {
                return triggerServerError(getUserMessageValidate(getMessageCaptchaInvalid(), []));
            }
        }


        $sanitized_type = sanitizeDashType($type);
        $seperatedList = seperateRequiredAndUnRequiredElementsFormSchema($formSchema);


        $itemsToSave = itemsToSaveFromFormSchema($formSchema);

        $defaultRules = [
            "min" => "min:1",
            "max" => "max:100",
            "int_range" => "int_range:1,100",
            "float_range" => "float_range:1.0,100.0",
            "file_type" => "file_type:png , jpg",
            "file_size" => "file_size:1000000",
        ];

        $requiredListRules = [];
        $optionalListRules = [];

        $funcsAddDefaultRequiredRule = [
            "general" => function ($item, $serverRulesItem, $defaultRules, $requiredListRules) {
                // check for min:rule
                if (!is_int(stripos($item['server-rules'], "min:"))) {
                    $serverRulesItem[] = $defaultRules['min'];
                }

                // check for max:rule
                if (!is_int(stripos($item['server-rules'], "max:"))) {
                    $serverRulesItem[] = $defaultRules['max'];
                }

                $requiredListRules[$item['id-form']] = $serverRulesItem;

                return $requiredListRules;
            },

            "file" => function ($item, $serverRulesItem, $defaultRules, $requiredListRules) {
                // check for file_type:rule
                if (!is_int(stripos($item['server-rules'], "file_type:"))) {
                    $serverRulesItem[] = $defaultRules['file_type'];
                }

                // check for file_size:rule
                if (!is_int(stripos($item['server-rules'], "file_size:"))) {
                    $serverRulesItem[] = $defaultRules['file_size'];
                }

                $requiredListRules[$item['id-form']] = $serverRulesItem;

                return $requiredListRules;
            },
        ];


        $funcsAddDefaultRequiredRule['number'] = function ($item, $serverRulesItem, $defaultRules, $requiredListRules) use ($funcsAddDefaultRequiredRule) {
            $requiredListRules =  $funcsAddDefaultRequiredRule['general']($item, $serverRulesItem, $defaultRules, $requiredListRules);

            if (empty($requiredListRules[$item['id-form']])) {
                $requiredListRules[$item['id-form']] = [];
            }

            $html_attributes_list = getFormInputAttributes($item);
            $extra_html_attributes = $html_attributes_list['sanitize'];

            $typeNumber = @$extra_html_attributes['_type'] == "float" ? "float" : "int";

            $ruleName = "{$typeNumber}_range";

            if (!is_int(stripos($item['server-rules'], "{$ruleName}:"))) {
                $requiredListRules[$item['id-form']][] = $defaultRules[$ruleName];
            }

            return $requiredListRules;
        };

        $funcsAddDefaultRequiredRule['radioBased'] = function ($item, $serverRulesItem, $defaultRules, $requiredListRules) use ($funcsAddDefaultRequiredRule) {
            $requiredListRules =  $funcsAddDefaultRequiredRule['general']($item, $serverRulesItem, $defaultRules, $requiredListRules);

            $html_attributes_list = getFormInputAttributes($item);

            $extra_html_attributes = $html_attributes_list['sanitize'];
            $exceptional_extra_attributes = collectXAttributesFromFormInputs($extra_html_attributes, "item_gr_", 0);

            $listOfReservedValue = [@$extra_html_attributes['value']];
            $listOfReservedValue = array_values(array_merge($listOfReservedValue, $exceptional_extra_attributes));

            if (!$listOfReservedValue) {
                die('empty reserved array on array base input check');
            }

            if (empty($requiredListRules[$item['id-form']])) {
                $requiredListRules[$item['id-form']] = [];
            }

            $prefixRule = "resereved_values";

            if ($item['type-input-form'] == 'radio') {
                $prefixRule = "resereved_values_once";
            }


            $requiredListRules[$item['id-form']][] = "{$prefixRule}:" . json_encode($listOfReservedValue);

            return $requiredListRules;
        };

        $funcsAddDefaultRequiredRule['checkbox'] = function ($item, $serverRulesItem, $defaultRules, $requiredListRules) use ($funcsAddDefaultRequiredRule) {
            $requiredListRules =  $funcsAddDefaultRequiredRule['radioBased']($item, $serverRulesItem, $defaultRules, $requiredListRules);

            return $requiredListRules;
        };

        $funcsAddDefaultRequiredRule['radio'] = function ($item, $serverRulesItem, $defaultRules, $requiredListRules) use ($funcsAddDefaultRequiredRule) {
            $requiredListRules =  $funcsAddDefaultRequiredRule['radioBased']($item, $serverRulesItem, $defaultRules, $requiredListRules);

            return $requiredListRules;
        };

        // required list
        if (!empty($seperatedList['required'])) {
            foreach ($seperatedList['required'] as $item) {
                $serverRulesItem = json_decode($item['server-rules'], true);

                // add default rules for REQUIRED INPUTS
                $type = $item['type-input-form'];
                $cbk = $funcsAddDefaultRequiredRule[$type] ?? $funcsAddDefaultRequiredRule["general"];

                $requiredListRules = $cbk($item, $serverRulesItem, $defaultRules, $requiredListRules);
            }
        }

        // optional list
        if (!empty($seperatedList['optional'])) {
            foreach ($seperatedList['optional'] as $item) {
                $serverRulesItem = json_decode($item['server-rules'], true);

                // add default rules for REQUIRED INPUTS
                $type = $item['type-input-form'];
                $cbk = $funcsAddDefaultRequiredRule[$type] ?? $funcsAddDefaultRequiredRule["general"];

                $optionalListRules = $cbk($item, $serverRulesItem, $defaultRules, $optionalListRules);
            }
        }

        // re-build $optionalListRules based whatever user input optional fields
        if ($optionalListRules) {
            $_optionalListRules = $optionalListRules;
            $optionalListRules = [];

            foreach ($_optionalListRules as $optionalRuleKey => $optionalRuleValue) {
                if (in_array($optionalRuleKey, array_keys($inputs))) {
                    $optionalListRules[$optionalRuleKey] = $optionalRuleValue;
                }
            }
        }

        do_action("before_form_" . $sanitized_type . "_" . __FUNCTION__ . "_check", $inputs, $requiredListRules);
        do_action("before_form_" . __FUNCTION__ . "_check", $inputs, $requiredListRules);

        // required fields validation
        if ($requiredListRules) {
            addRequireInputToJsonKeyMap(__FUNCTION__, $requiredListRules);

            $error = validationUserFormInputs($inputs);

            if ($error['message']) {
                return triggerServerError($error);
            }
        }
        // end


        // optional fields validation
        if ($optionalListRules) {
            removeRequireInputToJsonKeyMap(__FUNCTION__, true);
            addRequireInputToJsonKeyMap(__FUNCTION__, $optionalListRules);

            $error = validationUserFormInputs($inputs);

            if ($error['message']) {
                return triggerServerError($error);
            }
        }
        // end

         // manuall error trigger
         $manualError = apply_filters("before_" . __FUNCTION__ . "_save_in_db", false, $inputs, $formSchema);
         if (is_a($manualError, "\Illuminate\Http\RedirectResponse")) return $manualError;
 
         $manualError = apply_filters("before_" . __FUNCTION__ . "_save_in_db_{$sanitized_type}", false, $inputs, $formSchema);
         if (is_a($manualError, "\Illuminate\Http\RedirectResponse")) return $manualError;
 

        $user_ip = request()->ip();

        // check if already sent form
        $alreadySentForm = \App\Models\Form::where([
            "form_schema_id" => getTypeID($formSchema),
            "ip" => $user_ip,
            "status" => "pending"
        ])->get()->first();

        if ($alreadySentForm) {
            $messageBlock = "you have already sent information for this form";

            $messageBlock = apply_filters("block_message_" . __FUNCTION__, $messageBlock, $inputs, $formSchema);
            $messageBlock = apply_filters("block_message_" . __FUNCTION__ . "_{$sanitized_type}", $messageBlock, $inputs, $formSchema);

            return triggerServerError(getUserMessageValidate(__local($messageBlock)));
        }


        $formSaveDB = itemsToSaveFromFormSchemaToDBArrayList($itemsToSave, $inputs);
        $formSaveDB = uploadFormFiles($formSaveDB);

        do_action("after_form_" . $sanitized_type . "_" . __FUNCTION__ . "_check", $inputs);
        do_action("after_form_" . __FUNCTION__ . "_check", $inputs);

        // make sure emoji encoded
        foreach (array_keys($formSaveDB) as $formInputKey) {
            if (!is_string($formSaveDB[$formInputKey])) continue;

            $formSaveDB[$formInputKey] = encodeEmojiCharactersToHtml($formSaveDB[$formInputKey]);
        }

        $attributes = [
            "user_id" => getTypeAttr($currentUser, "id", 0),
            "form_schema_id" => getTypeID($formSchema),
            "ip" => $user_ip,
            "form" => json_encode($formSaveDB),
        ];

        $attributes = apply_filters(__FUNCTION__ . "_attributes_to_save", $attributes);
        $attributes = apply_filters(__FUNCTION__ . "_attributes_to_save_{$sanitized_type}", $attributes);

        $form = \App\Models\Form::create($attributes);

        do_action("after_form_" . $sanitized_type . "_" . __FUNCTION__ . "_created", $form, $inputs);
        do_action("after_form_" . __FUNCTION__ . "_created", $form, $inputs);

        
        $message = "your request successfully send !";

        $message = apply_filters("form_message", $message);
        $message = apply_filters("form_message_{$sanitized_type}", $message);

        return redirect()->back()->withErrors([
            'jsonServerMessage' => json_encode(getUserMessageValidate(__local($message) , ["SEND_FORM_200"]))
        ]);
    }

    public function set_color_mode($mode)
    {
        $referer = request()->headers->get('referer',  "");

        $listOfModes = getColorModes("*");

        if (!array_key_exists($mode, $listOfModes)) {
            return redirect($referer);
        }

        $key_color_mode = getCurrentColorModeKey();

        $currentUser = getCurrentUser();

        if ($currentUser) {
            joinDataToExtra(["extra_" . $key_color_mode => $mode], $currentUser);
            $currentUser->save();
        } else {
            setcookie($key_color_mode, $mode, time() + (86400 * 30), "/", "", false, false);
        }

        return redirect($referer);
    }

    public function index_author($user_id)
    {

        $user = User::where("id", $user_id)->where("status", "active");
        $user = $user->get()->first();

        abortByEntity($user);

        $per_page = getAllType("type_per_page");

        $postType_published_type = getPostTypePublishable();

        $user->historyAction = $user->post_types()->whereExists(function ($query) use ($postType_published_type) {
            $query->from("post_types")->whereColumn("id", "history_actions.model_id")->where("status", "publish")->whereIn("type", $postType_published_type);
        })->latest()->paginate($per_page);
        $user->post_types = getPostTypeListAuthor($user->historyAction);

        $user['title_seo'] = __local("Archive Author") . " " . getTypeFullname($user);

        // make it global for accessing title and other data
        generateGlobalTitle($user);

        // dynamic view
        $view_name = "frontend.author_index";

        return view($view_name, [
            'DB' => $user,
            'route_args' => [
                'user_id' => $user_id,
            ]
        ]);
    }


    public function index_search($term = "")
    {
        $sanitized_term = trim(preg_replace("/\s{2,}/i", " ", getSearchTerm()));
        $result = searchTheTerm($sanitized_term);

        // make it global for accessing title and other data
        generateGlobalTitle(["title" => getSearchTerm(), "fake_class" => "Search"]);

        return view("frontend.search_index", [
            'DB' => $result,
            'route_args' => [
                'term' => $sanitized_term,
            ]
        ]);
    }

    public function index_sitemap($sitemap_name)
    {
        $the_sitemap_name = "{$sitemap_name}.xml";
        $sitemap_path = base_path("public/sitemap/" . $the_sitemap_name);

        $sitemap_exists = file_exists($sitemap_path);

        if (!$sitemap_exists) abort(404);

        $mime_type = mime_content_type($sitemap_path);
        $contentLength = mb_strlen($sitemap_path);

        header("Content-Type: {$mime_type}");
        header("Content-Length: {$contentLength}");

        readfile($sitemap_path);

        exit;
    }

    public function db_install()
    {

        if (hasTable('options')) return "already installed";

        $error = \Illuminate\Support\Facades\Artisan::call("migrate:fresh --force --seed");
        return !$error ? "success" : "error";
    }

    public function export($type)
    {
        // check export type
        if (!in_array($type, array_keys(getExportTypeRemote()))) {
            abort(403, __local("INVALID TYPE"));
        }

        $ref = urldecode(request()->query("ref", ""));

        // check for url reference
        if (!$ref || is_int(stripos($ref, "export/"))) {
            abort(403, "INVALID URL");
        }

        $url = ROOT_URL . "/{$ref}";
        $optionsGuzzle = [];

        // sync cookie session
        if (isset($_COOKIE['XSRF-TOKEN']) && isset($_COOKIE['laravel_session'])) {
            $cookieList = [
                "XSRF-TOKEN" => $_COOKIE['XSRF-TOKEN'],
                "laravel_session" => $_COOKIE['laravel_session'],
            ];

            $cookie = \GuzzleHttp\Cookie\CookieJar::fromArray(
                $cookieList,
                request()->getHost()
            );

            $optionsGuzzle['cookies'] = $cookie;
        }

        $client = new \GuzzleHttp\Client(['cookies' => true]);
        $isInvalid = false;
        try {
            $res = $client->request("GET", $url, $optionsGuzzle);


            $statusCode = intval($res->getStatusCode());
        } catch (\Exception $e) {
            $isInvalid = true;
            $statusCode = $e->getCode();
        }

        if (400 <= $statusCode || $isInvalid) {
            abort($statusCode);
        }

        $result = "OK";
        $responseText = (string) $res->getBody();

        $targetRoute = getRouteByUrl($ref);

        if ($targetRoute) {

            $targetRoute = str_replace(".", "_", $targetRoute);

            $cbkAction = "export_{$type}_generation_{$targetRoute}";

            if (is_callable($cbkAction)) {
                $result = $cbkAction($ref, $res, $responseText, $type);
            }
        }

        return $result;
    }
}
