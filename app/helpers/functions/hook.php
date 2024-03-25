<?php

function inCommonHeadComponent()
{
    
    $list = [
        // general
        "<!-- general -->",
        "<meta charset=\"UTF-8\">",
        "<meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\">",
        "<meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no\">",
        "<meta name=\"generator\" content=\"Rapidcode.iR\">",

        "<meta name=\"_token\" content=\"" . csrf_token() . "\">",
        "<title>" . getCurrentTitlePage() . "</title>",

        // meta tags seo
        "<!-- meta tags seo -->",
        getSeoMetaTags(),

        // schema seo
        "<!-- schema seo -->",
        getSeoSchema(),

        "<!-- favicon -->",
        "<link rel=\"apple-touch-icon\" sizes=\"96x96\" href=\"/" . getFaviconBySize(96, true) . "\">",
        "<link rel=\"apple-touch-icon\" sizes=\"128x128\" href=\"/" . getFaviconBySize(128, true) . "\">",
        "<link rel=\"apple-touch-icon\" sizes=\"144x144\" href=\"/" . getFaviconBySize(144, true) . "\">",
        "<link rel=\"apple-touch-icon\" sizes=\"152x152\" href=\"/" . getFaviconBySize(152, true) . "\">",
        "<link rel=\"apple-touch-icon\" sizes=\"180x180\" href=\"/" . getFaviconBySize(180, true) . "\">",
        "<link rel=\"apple-touch-icon\" sizes=\"192x192\" href=\"/" . getFaviconBySize(192, true) . "\">",
        "<link rel=\"apple-touch-icon\" sizes=\"384x384\" href=\"/" . getFaviconBySize(384, true) . "\">",
        "<link rel=\"apple-touch-icon\" sizes=\"512x512\" href=\"/" . getFaviconBySize(512, true) . "\">",
        "<link rel=\"icon\" type=\"image/png\" sizes=\"32x32\" href=\"/" . getFaviconBySize(32, true) . "\">",
        "<link rel=\"icon\" type=\"image/png\" sizes=\"16x16\" href=\"/" . getFaviconBySize(16, true) . "\">",
        "<link rel=\"shortcut icon\" type=\"image/png\" href=\"/" . getFaviconBySize(48, true) . "\">",

        "<!-- manifest -->",
        "<link rel=\"manifest\" href=\"/" . getSiteManifestTemplateURL() . "\">",

        "<!-- ios -->",
        "<meta name=\"apple-mobile-web-app-title\" content=\"" . $GLOBALS['site_name'] . "\">",
        "<meta name=\"apple-mobile-web-app-status-bar-style\" content=\"" . $GLOBALS["options"]['theme_color'] . "\">",
        "<meta name=\"apple-mobile-web-app-status-bar\" content=\"" . $GLOBALS["options"]['theme_color'] . "\">",

        "<!-- ms -->",
        "<meta name=\"msapplication-TileColor\" content=\"#ffffff\">",
        "<meta name=\"msapplication-TileImage\" content=\"/" . getFaviconBySize(144, true) . "\">",
        "<meta name=\"msapplication-navbutton-color\" content=\"" . $GLOBALS["options"]['theme_color'] . "\">",

        "<!-- theme -->",
        "<meta name=\"theme-color\" content=\"" . $GLOBALS["options"]['theme_color'] . "\">",
    ];

    // add translate 
    $list = addTranslateToJs($list);

    return join("\n", $list);
}

function checkForMaintenanceMode()
{
    $isMaintenanceActive = getAllType("site_maintenance_activate");
    $listOfPreservedUsers = ["super_admin", "admin"];

    $currentUser = getCurrentUser();
    $currentRole = getTypeRole($currentUser);

    if ($isMaintenanceActive && !in_array($currentRole, $listOfPreservedUsers)) {
        die(view("frontend.maintenance"));
    }
}

function checkActiveUser()
{
    $current_user = getCurrentUser();

    if (!$current_user) return;

    $sign_in_route = "/dashboard/";

    $res = checkUserBlocked($current_user, null, $sign_in_route);
    if (is_object($res) && $current_user) {
        die("<script>location.assign('{$sign_in_route}')</script>");
    }
}

#=========> dashboard

function head_dashboard_t1()
{
    echo inCommonHeadComponent();
    do_action('enqueue_style_head_dashboard');
    do_action('enqueue_script_head_dashboard');
}

function footer_dashboard_t1()
{
    do_action('enqueue_style_footer_dashboard');
    do_action('enqueue_script_footer_dashboard');
}

function pre_general_css()
{
    $strCommaList = "tinymce,datepicker.jalali,file.manager,google.lato,bootstrap.icons,bootstrap.min,simplebar,sweet.alert";

    if ($GLOBALS['site_language'] == "fa_IR") {
        $strCommaList .= ",local.font";
    }

    loopthrowString($strCommaList, "enqueueCssByRoute");
}

function enqueue_style_head_dashboard_t1()
{
    pre_general_css();
    checkEnqueue('css');
    echo enqueue_style('main', 'static/css/main.css');

    $currentUser = getCurrentUser();


    $themeColor = !empty($currentUser['theme_color']) ? $currentUser['theme_color'] : getDefaultThemeUser("theme_color");
    $themeColorHover = !empty($currentUser) ? $currentUser['theme_color_hover'] : getDefaultThemeUser("theme_color_hover");

    // overwrite user theme color
    echo "<style>
        :root{
            --color-system : $themeColor;
            --color-cover-system : $themeColorHover;
        }
    </style>";
}

function enqueue_keymap_json_fields()
{
    echo getErrorMessageJavascript();

    $keymap_json_parent = [];

    if ($GLOBALS['isDashboard']) {
        if (isInRoute('/create') || isInRoute('/edit')) {
            $keymap_json_parent['keymap_json_input'] = $GLOBALS['require_inputs'];
        }
    }

    // for old inputs
    if (!empty($GLOBALS['data_page'])) {
        $keymap_json_parent['keymap_data_page'] = $GLOBALS['data_page'];
    }

    $keymap_json_parent['keymap_timezone'] = $GLOBALS['site_time_zone'];

    $keymap_json_parent['no_image_source'] = getNoImageSrc();

    // reload page command for js
    $keymap_json_parent['reload_page_required'] = $GLOBALS['need_reload'];

    $json = json_encode($keymap_json_parent);
    echo setVariableJavascript("jsonDataServer", $json);
}

function enqueue_script_footer_dashboard_t1()
{
    loopthrowString("jquery,common.core,datepicker.jalali,bootstrap.bundle.min,tinymce,file.manager,simplebar,clipboard,sweet.alert", "enqueueJsByRoute");
    echo enqueue_script('main', 'static/js/main.js');
    echo enqueue_script('main.extra', 'static/js/main.extra.js');
    checkEnqueue('js');
}

function add_post_type_dashboard_menu()
{

    $parentElement = getDashboardMenuItems([
        'link' => '%post_type%',
        'icon' => 'bi bi-stickies',
        'label' => __local('Post Type'),
    ]);

    $childElement = getDashboardMenuChildTypeItems([
        "add" => [
            "link" => "/dashboard/post.type/x-type/create",
        ],
        "edit" => [
            "link" => "/dashboard/post.type/x-type/edit/{id}",
        ],
        "list" => [
            "link" => "/dashboard/post.type/x-type/index",
        ]
    ]);

    addElementDashboardNavMenuCompletely($GLOBALS['post_type'], $parentElement, $childElement, 1);
}

function add_taxonomy_dashboard_menu()
{

    $parentElement = getDashboardMenuItems([
        'link' => '%taxonomy%',
        'icon' => 'bi bi-diagram-3',
        'label' => __local('Taxonomy'),
    ]);

    $childElement = getDashboardMenuChildTypeItems([
        "add" => [
            "link" => "/dashboard/taxonomy/x-type/create",
        ],
        "edit" => [
            "link" => "/dashboard/taxonomy/x-type/edit/{id}",
        ],
        "list" => [
            "link" => "/dashboard/taxonomy/x-type/index",
        ],
    ]);

    addElementDashboardNavMenuCompletely($GLOBALS['taxonomy'], $parentElement, $childElement, 1);
}

function add_comments_dashboard_menu()
{

    $parentElement = getDashboardMenuItems([
        'link' => '%comments%',
        'icon' => 'bi bi-chat-quote',
        'label' => __local('Comments'),
    ]);

    $childElement = getDashboardMenuChildTypeItems([
        "edit" => [
            "link" => "/dashboard/comments/x-type/edit/{id}",
        ],
        "list" => [
            "link" => "/dashboard/comments/x-type/index",
        ],
    ]);

    addElementDashboardNavMenuCompletely($GLOBALS['comment'], $parentElement, $childElement, 4);
}

function add_require_comment_rating_field()
{
    addRequireInputToJsonKeyMap("comments", ["rating" => []]);
}

function add_comment_save_info($comment, $inputs)
{

    $cookieType = getPersistDataType();

    if ($cookieType == "cookie" && @$inputs['save_comment_info'] == "yes") {
        setcookie("user_comment_info", json_encode(["fullname" => $inputs['fullname'], "email" => $inputs['email']]), time() + (86400 * 30), "/", null, false, true);
    }
}

function post_type_comment_switch($item)
{
    $switch_value = [];
    $raw_value = "";

    $id = "post_type_comment_switch";

    $switch_value = getPostTypeMetaOLD($id);

    if (!$switch_value) {
        $switch_value = getPostTypeMetaByKeyFirst($item, $id);
    }

    if ($switch_value) {
        if (!is_string($switch_value)) {
            $switch_value = getTypeAttr($switch_value, 'value');
        }
        $raw_value = $switch_value;
        if ($switch_value != "")
            $switch_value = explode(",", $switch_value);
    }

    $post_type_info = (checkPostType(getTypee($item)))['current_post_type_info'];
    $post_type_comments = $post_type_info['comment'];

    if (!$post_type_comments) return false;

    // get options by registered comment 
    $post_type_comments_finall = [];
    $allCommentTypes = getCommentsType();
    array_walk($post_type_comments, function ($element, $key) use (&$post_type_comments_finall, $allCommentTypes) {
        $post_type_comments_finall[$element] = $allCommentTypes[$element];
    });

    $comment_options = join("\n", getCommentsTypeOption($switch_value, "", $post_type_comments_finall));

    $labelMeta = __local("Disable Comments Type");
    $align = trnsAlignBlockCls();
    echo getMetaWrapperPostType(
        "<label for=\"\" class=\"{$align}\">{$labelMeta}</label><select class=\"form-control select2-simple select2-multiple\" id=\"{$id}\" data-empty-content=\"true\" multiple>{$comment_options}</select><input type=\"text\" value=\"{$raw_value}\" data-group-id=\"meta:{$id}\" name=\"meta:{$id}\" data-label=\"{$labelMeta}\" id=\"{$id}_content\" class=\"d-none select2-content-list\">",
        $id,
        'input-wrapper mt-4 col-6'
    );
}

function post_type_term_add_term_select($item)
{
    $value = "";
    $data = null;

    $id = "post_type_term_select";

    $value = getPostTypeMetaOLD($id);

    if (!$value) {
        $data = getPostTypeMetaByKeyFirst($item, $id);
        if ($data) {
            $value = $data['value'];
        }
    }

    $options = join("\n", getTermSlugsOption($value, " (Selected)"));

    echo getMetaWrapperPostType(
        "<label for=\"{$id}\">Term</label><select class=\"form-control select2-simple\" id=\"{$id}\" data-empty-content=\"true\">{$options}</select><input type=\"text\" value=\"{$value}\" data-group-id=\"meta:{$id}\" name=\"meta:{$id}\" data-label=\"Term\" id=\"{$id}_content\" class=\"d-none select2-content-list\">",
        $id,
        'input-wrapper mt-3 col-6'
    );
}

function post_type_popup_ads_add_condition($item = null, $exportValue = false)
{
    $display_res = post_type_popup_ads_display_type($item, $exportValue);

    $raw_value = "";
    $value = [];

    $select_value = "";
    $text_value = "";

    $id = "post_type_popup_ads_select";

    $data = getPostTypeMetaByKey($item, $id, "first");
    if ($data) {
        $raw_value = $data['value'];
    }

    if (!$raw_value) {
        $raw_value = getPostTypeMetaOLD($id);
    }

    if ($raw_value) {
        $select_value = "";
        $text_value = "";

        $value = explode(":", $raw_value, 2);

        $select_value = strtolower($value[0]);
        $text_value = strtolower($value[1]);
    }

    if ($exportValue && $raw_value) {
        return [
            "stateCodition" => $value[0],
            "parameterCodition" => slashFixerPath($value[1]),
            "display" => $display_res
        ];
    } else if ($exportValue && !$raw_value) {
        return [];
    }


    $options = join("\n", getPopupConditionOption($select_value, " (Selected)"));
    $options_data = json_encode(getPopupCondition("*"));
    echo getMetaWrapperPostType(
        "<label for=\"{$id}\">Condition</label><select class=\"form-control select2-simple\" id=\"{$id}\" data-empty-content=\"true\" data-extra-data='{$options_data}'>{$options}</select><input type=\"text\" value=\"{$raw_value}\" data-group-id=\"meta:{$id}\" name=\"meta:{$id}\" data-label=\"Condition\" id=\"{$id}_content\" class=\"d-none select2-content-list select-value-to-text\"><input type=\"text\" class=\"form-control mt-2 the-text-value\" id=\"{$id}_text\" value=\"{$text_value}\">",
        $id,
        'input-wrapper mt-3 col-6'
    );
}

function post_type_popup_ads_display_type($item = null, $exportValue = false)
{
    $raw_value = "";
    $value = [];

    $select_value = "";
    $text_value = "";

    $id = "post_type_popup_ads_display";

    $data = getPostTypeMetaByKey($item, $id, "first");
    if ($data) {
        $raw_value = $data['value'];
    }

    if (!$raw_value) {
        $raw_value = getPostTypeMetaOLD($id);
    }

    if ($raw_value) {
        $value = $raw_value;
    }

    if ($exportValue && $raw_value) {
        return $raw_value;
    } else if ($exportValue && !$raw_value) {
        return false;
    }


    $options = join("\n", getPopupDisplayTypeOption($value, " (Selected)"));
    $options_data = json_encode(getPopupCondition("*"));
    echo getMetaWrapperPostType(
        "<label for=\"{$id}\">Display Type</label><select class=\"form-control select2-simple\" id=\"{$id}\" data-empty-content=\"true\" data-extra-data='{$options_data}'>{$options}</select><input type=\"text\" value=\"{$raw_value}\" data-group-id=\"meta:{$id}\" name=\"meta:{$id}\" data-label=\"Display Type\" id=\"{$id}_content\" class=\"d-none select2-content-list\">",
        $id,
        'input-wrapper mt-3 col-6'
    );
}

function add_meta_rating_edit($item)
{
    $rating = intval(getTypeAttr($item, "rating"));
    $labelRating = __local('Rating');
    $block = trnsAlignBlockCls();
    echo getMetaWrapperType("<label for=\"rating\" class=\"{$block}\">{$labelRating}</label> <input class=\"form-control mb-3\" dir=\"ltr\" min=\"1\" max=\"5\" value=\"{$rating}\" id=\"rating\" name=\"rating\" type=\"number\" data-group-id=\"rating\" data data-label=\"Rating\" required=\"required\">", "Comment-rating", null, null);
}

function remove_password_edit_action()
{
    removeRequireInputToJsonKeyMap($GLOBALS['current_page'], ["password"]);
}

function addRequireFieldToUserPageFromScratch(array $require_list)
{
    removeRequireInputToJsonKeyMap("user", true);
    addRequireInputToJsonKeyMap("user", $require_list);
}

function add_require_reset_password_fields()
{
    addRequireFieldToUserPageFromScratch([
        "client_via" => [],
        "client_id" => [],
    ]);
}

function add_require_sign_in_fields()
{
    $base = [
        "client_id" => [],
    ];

    $account_type_registeriation = getAllType("account_type_registeriation");

    if ($account_type_registeriation == "email") {
        $base["password"] = [];
    }

    addRequireFieldToUserPageFromScratch($base);

    return $base;
}

function otp_active_expired($model, $inputs, $finallInputs)
{

    unset($finallInputs['expired']);

    getOTPByFields($model, $finallInputs, function ($model) {
        if (isExpired(getTypeDateExpired($model))) {
            $model->update([
                "expired" => true
            ]);
        }
    });
}

function otp_active_expired_attempt($model, $inputs, $finallInputs)
{

    $finallInputs = ['id' => $finallInputs['id']] ?? false;

    if (!$finallInputs) return $finallInputs;

    getOTPByFields($model, $finallInputs, function ($model) {
        $attempt = intval(getTypeAttr($model, "attempt"));
        $finall_attempt = ($attempt + 1);

        if (getTypeAttr($model, "expired")) {
            $model->update([
                "expired" => true
            ]);
            return false;
        }

        $model->update([
            "attempt" => $finall_attempt
        ]);


        if ($GLOBALS['user_max_attempt'] < $finall_attempt) {
            $model->update([
                "expired" => true
            ]);
        }
    });
}

function otp_type_action_reset_password($model, $isExists, $inputs, $finallInputs)
{

    $data = getTypeAttr($model, "data");
    if ($data) {
        $user = $model->user;
        if ($user) {
            $user['password'] = bcrypt($data);
            $user->saveQuietly();
        }
    }
}

function otp_active_expired_seen_by_exists($model, $isExists, $inputs, $finallInputs)
{
    $callback_args = func_get_args();
    if ($isExists) {
        getOTPByFields($model, $finallInputs, function ($model) use ($callback_args) {
            $model->update([
                "expired" => true,
                "seen" => true,
            ]);

            $callback = "otp_type_action_" . getTypee($model);

            if (is_callable($callback)) {
                $callback_args[0] = $model;
                $callback(...$callback_args);
            }

            $user = $model->user;

            if (!$user) return false;


            $type = getTypee($model);
            $actions = getOTPToUserField($type);


            // if sms login
            if ($model['type'] == "verify_phone_sign_in" || $model['type'] == "verify_phone") {
                auth()->login($user, stripos(strval($model['data']), "REMEMBER") !== false);
            }


            // check user x-field is true if it's don't go any further
            if (isset($user[$actions['key']]) && $user[$actions['key']] == true && getTypeStatus($user) == "active") return false;

            // ( verify or reset ) active
            foreach ($actions['elements'] as $action) {

                $name = $action['name'];
                if (isset($action['raw']) && $action['raw']) {
                    $value = getOTPToUserFieldRawDynamic($action['value']);
                } else {
                    $value = $action['value'];
                }

                $user[$name] = $value;
            }

            $user['status'] = "active";

            $user->saveQuietly();
        });
    }
}

# Template for adding Post Meta
function add_post_meta_field($key, $label, $item = null, $template = null, $classWrapper = null)
{

    $template = $template ?: "<label for=\"x-label-for\" class=\"x-label-class\">x-label-text</label><input data-post-type-id=\"x-post-type-id\" type=\"text\" value='x-input-text' data-group-id=\"meta:x-input-key\" name=\"meta:x-input-key\" data-label=\"x-label-meta\" id=\"x-input-key\" class=\"form-control\">";
    $classWrapper = $classWrapper ?: "input-wrapper mt-4 col-6";

    $value = "";
    $meta = null;

    $id = $key;

    if ($item) {
        $meta = getPostTypeMetaByKeyFirst($item, $id);
        $value = getTypeValue($meta);
    }


    $value = getPostTypeMetaOLD($id, $value);

    $labelMeta = __local($label);
    $align = trnsAlignBlockCls();

    // dynamic template
    $template = str_replace(["x-label-for", "x-label-class", "x-label-text", "x-input-text", "x-input-key", "x-label-meta", "x-post-type-id"], [$key, $align, $labelMeta, $value, $key, $labelMeta, getTypeID($item)], $template);

    return getMetaWrapperPostType(
        $template,
        $id,
        $classWrapper
    );
}
# END Template for adding Post Meta

// check for maintenanc
add_action('dashboard_middle_ware_started', 'checkForMaintenanceMode');
add_action('front_end_started', 'checkForMaintenanceMode');

// title & meta & enqueue
add_action('head_dashboard', 'head_dashboard_t1');

// check for user is deactivated
add_action('front_end_started', 'checkActiveUser');

// enqueue
add_action('footer_dashboard', 'footer_dashboard_t1');

// style
add_action('enqueue_style_head_dashboard', 'enqueue_style_head_dashboard_t1');

// scripts 
add_action('enqueue_script_footer_dashboard', 'enqueue_script_footer_dashboard_t1');
add_action('enqueue_script_head_dashboard', 'enqueue_keymap_json_fields');

// add menu element
add_action("before_show_dashboard_menu", "add_post_type_dashboard_menu");
add_action("before_show_dashboard_menu", "add_taxonomy_dashboard_menu");
add_action("before_show_dashboard_menu", "add_comments_dashboard_menu");

// overview page
add_action("dashboard_index", function () {
    echo getOverviewWidgetPostTypeCounter();
}, 16);
add_action("dashboard_index", function () {
    echo getOverviewWidgetTaxonomyCounter();
}, 15);
add_action("dashboard_index", function () {
    echo getOverviewWidgetViewsCounter();
}, 14);
add_action("dashboard_index", function () {
    echo getOverviewWidgetFilesCounter();
}, 13);
add_action("dashboard_index", function () {
    echo getOverviewWidgetCommentsCounter();
}, 12);
add_action("dashboard_index", function () {
    echo getOverviewWidgetUsersCounter();
}, 11);


// comment and rating disable (meta)
add_action("post_type_edit_col_left", "post_type_comment_switch");

// add term select to post type Term
add_action("post_type_term_edit_col_left", "post_type_term_add_term_select");

// add condition to post type PopUp
add_action("post_type_popup_ads_create_col_left", "post_type_popup_ads_add_condition");
add_action("post_type_popup_ads_edit_col_left", "post_type_popup_ads_add_condition");


// rating add require
add_action('before_comment_rating_store_check', 'add_require_comment_rating_field');
// comment save fullname,email cookie
add_action('comment_comment_successfully_stored', 'add_comment_save_info', 10, 2);

// rating meta field for comments/rating/edit/x
add_action("comment_rating_edit_col_left", 'add_meta_rating_edit');

// remove password require for edit/update from dashboard
add_action('user_edit_action', "remove_password_edit_action");
// user reset password add Required Fields
add_action('before_reset_password_check', "add_require_reset_password_fields");
// user sign-in add Required Fields
add_action('before_signin_check', "add_require_sign_in_fields");

// OTP@show hook for o_t_p_s 
add_action('before_exists_check_otp', 'otp_active_expired', 10, 3);
add_action('before_exists_check_otp', 'otp_active_expired_attempt', 10, 3);
add_action('after_exists_check_otp', 'otp_active_expired_seen_by_exists', 10, 4);

// sitemap
add_action("post_type_successfully_stored", "generateSitemapTypes");
add_action("post_type_successfully_updated", "generateSitemapTypes");
add_action("posttype_successfully_deleted", "generateSitemapTypes");

add_action("taxonomy_successfully_stored", "generateSitemapTypes");
add_action("taxonomy_successfully_updated", "generateSitemapTypes");
add_action("taxonomy_successfully_deleted", "generateSitemapTypes");

#=========> front end

function head_front_1()
{
    echo inCommonHeadComponent();
    do_action('enqueue_style_head_front');
    do_action('enqueue_script_head_front');
}

function footer_front_1()
{
    do_action('enqueue_style_footer_front');
    do_action('enqueue_script_footer_front');
}

function enqueue_style_head_front_t1()
{
    loopthrowString("sweet.alert", "enqueueCssByRoute");
    echo enqueue_style('core', 'static/front-end/css/core.css');
    //header front-end
    echo enqueue_style('fonts-google', 'https://fonts.googleapis.com/css2?family=Montserrat&family=Oswald:wght@400;500;600&display=swap');
    echo enqueue_style('cloudflare', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css');
    echo enqueue_style('flaticon', 'static/front-end/assets/lib/flaticon/font/flaticon.css');
    echo enqueue_style('owl-carousel', 'static/front-end/assets/lib/owlcarousel/assets/owl.carousel.min.css');
    echo enqueue_style('lightbox', 'static/front-end/assets/lib/lightbox/css/lightbox.min.css');
    echo enqueue_style('style', 'static/front-end/assets/css/style.css');
}

function enqueue_script_head_front_t1()
{
    echo getErrorMessageJavascript();

    $keymap_json_parent = [];

    // for old inputs
    if (!empty($GLOBALS['data_page'])) {
        $keymap_json_parent['keymap_data_page'] = $GLOBALS['data_page'];
    }

    // color mode
    $keymap_json_parent['color_modes'] = [
        "current" => getCurrentColorMode(),
        "info" => getColorModes("*")
    ];
    

    $json = json_encode($keymap_json_parent);
    echo setVariableJavascript("jsonDataServer", $json);
}

function enqueue_script_footer_front_t1()
{
    echo enqueue_script('jquery-js', 'https://code.jquery.com/jquery-3.4.1.min.js') . "\n";
    loopthrowString("sweet.alert,common.core", "enqueueJsByRoute");
    echo enqueue_script('core', 'static/front-end/js/core.js') . "\n";
    //footer front-end
    echo enqueue_script('bundle-js', 'https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js') . "\n";
    echo enqueue_script('easing', 'static/front-end/assets/lib/easing/easing.min.js') . "\n";
    echo enqueue_script('owl-carousel', 'static/front-end/assets/lib/owlcarousel/owl.carousel.min.js') . "\n";
    echo enqueue_script('isotope', 'static/front-end/assets/lib/isotope/isotope.pkgd.min.js') . "\n";
    echo enqueue_script('isotope', 'static/front-end/assets/lib/lightbox/js/lightbox.min.js') . "\n";
    echo enqueue_script('main-js', 'static/front-end/assets/js/main.js') . "\n";
}

function popup_ads_show()
{
    $query = App\Models\PostType::where("status", "publish")->where("type", "popup_ads")->latest()->with("meta");
    $popup_list_db = $query->get();
    if (!count($popup_list_db)) return false;

    $path_url = slashFixerPath(request()->path());

    $matched_list = [];
    $type_list = getPopupCondition('*');



    foreach ($popup_list_db as $item) {
        $popup_condition = post_type_popup_ads_add_condition($item, true);

        if (!$popup_condition) continue;
        $current_type = $type_list[$popup_condition['stateCodition']] ?? [];
        if (!$current_type) continue;

        $theList = [
            "condition" => $popup_condition,
            "item" => $item,
            "id" => $current_type['id'],
            "type" => $current_type
        ];

        if ($popup_condition['stateCodition'] == "contain") {
            if (is_int(strpos($path_url, $popup_condition['parameterCodition']))) {
                $matched_list[] = $theList;
            }
        } else if ($popup_condition['stateCodition'] == "exact") {
            if ($path_url == $popup_condition['parameterCodition']) {
                $matched_list[] = $theList;
            }
        }
    }

    $theMatch = null;

    $type_list_ids = array_values(getPopupCondition("id"));
    $matched_list = collect($matched_list);

    $finallMatches = [];
    $matchesType = [];

    foreach ($type_list_ids as $id_item) {
        $theMatches = $matched_list->where("id", $id_item);
        if ($theMatches) {
            foreach ($theMatches as $theMatch) {
                $theMatchDisplay = $theMatch['condition']['display'] ?? null;

                if ($theMatch && !in_array($theMatchDisplay, $matchesType)) {
                    $matchesType[] = $theMatchDisplay;
                    $finallMatches[] = $theMatch;
                }
            }
        }
    }

    if (!count($finallMatches)) return false;

    $jsListData = [];
    foreach ($finallMatches as $theMatch) {

        $view_id = addViewLog(getTypeID($theMatch['item']));

        if (0 < $view_id || $theMatch['condition']['display'] === "notification") {
            $theMatch['item'] = getArrayElementByKeys(["id", "body", "body_raw", "thumbnail_url", "title"], $theMatch['item']->getOriginal());
            $jsListData[] = $theMatch;
        }
    }

    if ($jsListData) {
        echo setVariableJavascript("popupAds", json_encode($jsListData));
    }
}

function cookie_policy_show()
{
    $isCookieSet = isset($_COOKIE['cookie_policy']);
    if ($isCookieSet) return false;

    $cookie_content = getOptionByKey("cookie_policy", null, null, "value");

    if (isCookieDisabledFromAdmin($cookie_content)) return false;

    // did not ask yet
    $data = json_encode([
        "content" => $cookie_content,
        "actions" => getCookieActions("*")
    ]);

    echo setVariableJavascript("cookiePolicyData", "{$data}");
}

function add_view_to_post_type()
{
    $item = $GLOBALS['DB'] ?? false;
    if (!$item) return false;
    if (!is_a($item, "App\Models\PostType")) return false;
    if (getTypeStatus($item) != "publish") return false;

    return addViewLog(getTypeID($item));
}

// head and footer init
add_action("head_front", "head_front_1", 9999);
add_action("footer_front", "footer_front_1");

// enqueue
add_action("enqueue_style_head_front", "enqueue_style_head_front_t1");
add_action("enqueue_script_head_front", "enqueue_script_head_front_t1");
add_action("enqueue_script_footer_front", "enqueue_script_footer_front_t1");


// popup ads
add_action("head_front", "popup_ads_show");
// cookie policy agreement
add_action("head_front", "cookie_policy_show");
// view user
add_action("head_front", "add_view_to_post_type");