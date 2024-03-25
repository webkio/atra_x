<?php

function setConstants()
{
    define('SPE', DIRECTORY_SEPARATOR);
}

function getDefaultOptions()
{

    return [
        "site_name" => "the site name",
        "site_description_raw" => "the site description",
        "site_time_zone" => "Asia/Tehran",
        "site_language" => "fa_IR",
        "favicon_url" => "",
        "theme_color" => "",
        "theme_color_hover" => "",

        "record_per_page" => 10,
        "type_per_page" => 9,

        "comment_per_page" => 15,
        "comment_max_depth" => 3,
        "comment_must_be_login" => false,

        "user_max_attempt" => 3,
        "account_type_registeriation" => "email",
        "newsletter_valid_domain" => "*",

        "social_links" => "",

        "captcha_disable" => [
            "user" => [
                "sign_in" => false,
                "sign_up" => false,
                "reset_password" => false,
            ]
        ],
        "site_ssl_force" => false,
        "site_maintenance_activate" => false,
    ];
}

function setGlobals()
{
    // post type
    $GLOBALS['post_type'] = [];

    // taxonomy
    $GLOBALS['taxonomy'] = [];

    // comment
    $GLOBALS['comment'] = [];

    // menu
    $GLOBALS['menu'] = [];

    // add_action & do_action hooks
    $GLOBALS['hookList'] = [];

    // html Style link
    $GLOBALS['attachedStyles'] = [];
    $GLOBALS['triggeredStyles'] = [];

    // html Script link
    $GLOBALS['attachedScripts'] = [];
    $GLOBALS['triggeredScripts'] = [];

    // require inputs
    $GLOBALS['require_inputs'] = [
        "post.type" => [
            "elements" => [
                "title" => ["max:255"],
                // "slug" => ['max:255'],
                "body" => [],
                "body_raw" => [],
                "status" => ["max:255"],
                #"taxonomy:category or meta:price"
            ]
        ],
        "taxonomy" => [
            "elements" => [
                "title" => ["max:255"],
                "status" => ["max:255"]
            ]
        ],

        "forms_schema" => [
            "elements" => [
                "title" => ["max:255"],
                "type" => ["max:100"],
                "schema" => ["max:2000"],
                "status" => ["max:100"],
            ]
        ],

        "send_form" => [
            "elements" => []
        ],

        "menu" => [
            "elements" => [
                "title" => ['max:255'],
                "menu_items" => [],
                "slug" => ['max:255']
            ]
        ],
        "comments" => [
            "elements" => [
                "post_type_id" => ["max:255"],
                "email" => ["email:true", "max:255"],
                "fullname" => ["max:255"],
                "content" => ["max:5000"],
            ]
        ],

        "newsletter" => [
            "elements" => [
                "type" => ["max:35"],
                "client_id" => [],
                "ip" => ["max:255"],
            ]
        ],
        "user" => [
            "elements" => [
                "username" => ["regex:username_user", "min:3", "max:25"],
                "email" => ["email:true", "max:255"],
                "fullname" => ["max:36"],
                "password" => ["regex:password_user", "min:8", "max:50"],
                "role" => ["max:50"]
            ]
        ],

        "redirect" => [
            "elements" => [
                "title" => ["max:200"],
                "from" => ["max:200"],
                "to" => ["max:200"],
                "http_code" => ["max:100"],
            ]
        ],

        "settings" => [
            "elements" => [
                "site_name" => ["max:25"],
                "site_description_raw" => ["max:250"],
                "site_time_zone" => ["max:50"],
                "site_language" => ["max:50"],
                "favicon_url" => ["max:200"],
                "theme_color" => ["max:12"],
                "theme_color_hover" => ["max:45"],
                "record_per_page" => ["regex:must_positive_int_general"],
                "type_per_page" => ["regex:must_positive_int_general"],
                "comment_per_page" => ["regex:must_positive_int_general"],
                "comment_max_depth" => ["regex:must_positive_int_general"],
                "newsletter_valid_domain" => ["max:500"],
                "comment_must_be_login" => [],
                "user_max_attempt" => ["regex:must_positive_int_general"],
                "account_type_registeriation" => ["max:45"],
                "social_links" => [],
                "captcha_disable" => ["max:512"],
                "site_ssl_force" => ["max:15"],
                "site_maintenance_activate" => ["max:15"],
                "cookie_policy" => ["max:512"],
            ]
        ],
    ];

    // unallowed file list extension
    $GLOBALS['unallowed_extension'] = [
        // php
        "PHP",
        "PHTML",
        "PHP3",
        "PHP4",
        "PHP5",
        "PHPS",
        // html
        "HTM",
        "HTML",
        "XHTML",
        // xml
        "XML",
    ];

    // image resize list (based on width)
    $GLOBALS['resize_image_list'] = [
        150, 300, 550, 700, 1024
    ];

    // group type file
    $GLOBALS['group_type'] = [
        "image" => [
            "JPEG" => ["can_preview" => true, "can_resize" => true],
            "JPG" => ["can_preview" => true, "can_resize" => true],
            "PNG" => ["can_preview" => true, "can_resize" => true],
            "GIF" => ["can_preview" => true, "can_resize" => true],
            "WEBP" => ["can_preview" => true, "can_resize" => true],
            "SVG" => ["can_preview" => true, "can_resize" => false],
            "ICO" => ["can_preview" => true, "can_resize" => false],
            "TIFF" => ["can_preview" => false, "can_resize" => false],
            "PSD" => ["can_preview" => false, "can_resize" => false],
            "EPS" => ["can_preview" => false, "can_resize" => false],
            "AI" => ["can_preview" => false, "can_resize" => false],
            "INDD" => ["can_preview" => false, "can_resize" => false],
            "RAW" => ["can_preview" => false, "can_resize" => false]
        ],
        "video" => [
            "MP4" => ["can_preview" => false],
            "MOV" => ["can_preview" => false],
            "WMV" => ["can_preview" => false],
            "AVI" => ["can_preview" => false],
            "AVCHD" => ["can_preview" => false],
            "FLV" => ["can_preview" => false],
            "F4V" => ["can_preview" => false],
            "SWF" => ["can_preview" => false],
            "MKV" => ["can_preview" => false],
            "WEBM" => ["can_preview" => false]
        ],
        "audio" => [
            "MP3" => ["can_preview" => false],
            "OGG" => ["can_preview" => false],
            "AAC" => ["can_preview" => false],
            "FLAC" => ["can_preview" => false],
            "ALAC" => ["can_preview" => false],
            "WAV" => ["can_preview" => false],
            "AIFF" => ["can_preview" => false],
            "DSD" => ["can_preview" => false]
        ],
        "archive" => [
            "ZIP" => ["can_preview" => false],
            "RAR" => ["can_preview" => false],
            "7Z" => ["can_preview" => false],
            "ISO" => ["can_preview" => false],
            "IMG" => ["can_preview" => false],
            "IMA" => ["can_preview" => false],
            "BIN" => ["can_preview" => false],
            "DMG" => ["can_preview" => false],
            "TGZ" => ["can_preview" => false],
            "WAR" => ["can_preview" => false],
            "WIM" => ["can_preview" => false],
            "XAR" => ["can_preview" => false],
            "ZZ" => ["can_preview" => false]
        ],
        "documents" => [
            "DOC" => ["can_preview" => false],
            "DOCX" => ["can_preview" => false],
            "ODT" => ["can_preview" => false],
            "PDF" => ["can_preview" => false],
            "XLS" => ["can_preview" => false],
            "PPT" => ["can_preview" => false],
            "PPTX" => ["can_preview" => false],
            "TXT" => ["can_preview" => false]
        ],
    ];

    // current page
    $GLOBALS['current_page'] = '';

    // last url path
    $GLOBALS['url_path'] = '';

    // date format
    $GLOBALS['dateFormat'] = 'Y-m-d H:i:s';

    // error validation for usage like REST
    $GLOBALS['error_validation'] = null;

    // by this we can reload frontend page make sure use it well unless you will get "TOO MANY REDIRECTS"
    $GLOBALS['need_reload'] = false;

    // meta args list
    $GLOBALS['meta_args_list'] = [
        "post.type" => [
            /* Sample
            "price" => [
                'label' => 'Price',
                "data-filter" => "",
                "data-input" => "numberRange",
                "data-options" => '{"min":0,"max":500000000,"from":0,"to":499000000,"step":10,"prefix":"<span class=\"d-inline-block\"></span> "}',
                "data-name" => "price",
                "data-operator" => "range",
                "data-relation" => "meta:post_type_id:price"
            ],
            "entity" => [
                'label' => 'Entity',
                "data-filter" => "",
                "data-input" => "numberRange",
                "data-options" => '{"min":0,"max":500000000,"from":0,"to":499000000,"step":10,"prefix":"<span class=\"d-inline-block\"></span> "}',
                "data-name" => "entity",
                "data-operator" => "range",
                "data-relation" => "meta:post_type_id:entity"
            ]
           */]
    ];

    // global str
    $GLOBALS['str'] = '';

    // loopBreak global for nested loop
    $GLOBALS['loopbreak'] = false;

    // for x-filed error show complete in js
    $GLOBALS['ananymousFieldError'] = true;
}

function setGlobalsOption($options = [])
{
    $options = $options ? $options : getDefaultOptions();

    // site bio
    $GLOBALS['site_name'] = $options['site_name'];
    $GLOBALS['site_description_raw'] = $options['site_description_raw'];
    $GLOBALS['site_time_zone'] = $options['site_time_zone'];
    $GLOBALS['site_language'] = $options['site_language'];
    $GLOBALS['favicon_url'] = $options['favicon_url'];
    $GLOBALS['theme_color'] = $options['theme_color'];
    $GLOBALS['theme_color_hover'] = $options['theme_color_hover'];

    // record per page (listing dashboard)
    $GLOBALS['record_per_page'] = $options['record_per_page'];

    // post type per page
    $GLOBALS['type_per_page'] = $options['type_per_page'];

    // comment per page
    $GLOBALS['comment_per_page'] = $options['comment_per_page'];

    // comment max depth
    $GLOBALS['comment_max_depth'] = $options['comment_max_depth'];

    // for sending comment must be loggin
    $GLOBALS['comment_must_be_login'] = $options['comment_must_be_login'];

    // max user form attempt
    $GLOBALS['user_max_attempt'] = $options['user_max_attempt'];
    // login and register type
    $GLOBALS['account_type_registeriation'] = $options['account_type_registeriation'];
    // newsletter valid domains
    $GLOBALS['newsletter_valid_domain'] = $options['newsletter_valid_domain'];
    // social media
    $GLOBALS['social_links'] = $options['social_links'];

    // security
    $GLOBALS['captcha_disable'] = $options['captcha_disable'];
    $GLOBALS['site_ssl_force'] = $options['site_ssl_force'];
    $GLOBALS['site_maintenance_activate'] = $options['site_maintenance_activate'];
}

setGlobals();
setGlobalsOption();
setConstants();

function base_do_hook($hookName, $type, ...$args)
{
    $result = null;

    if (empty($GLOBALS['hookList'][$hookName])) {

        if ($type == "filter") {
            $result = $args[0] ?? null;
        }

        return $result;
    }

    $hookArray = $GLOBALS['hookList'][$hookName];

    $hookArray = sortTheArray($hookArray, function ($a, $b) {
        if ($a['priority'] == $b['priority']) return 0;

        return ($a['priority'] < $b['priority']) ? -1 : 1;
    }, 'desc');

    if ($hookArray && $type == "filter") {
        $hookArray = array_reverse($hookArray, true);
    }


    foreach ($hookArray as $theKey => $hookChild) {
        $tArgs = array_slice($args, 0, intval($hookChild['accepted_args']));

        if ($type == "action") {
            $result = call_user_func($hookChild['callback'], ...$tArgs);
        } else if ($type == "filter") {
            if (isset($lastResult)) $tArgs[0] = $lastResult;
            $result = call_user_func($hookChild['callback'], ...$tArgs);
            $lastResult = $result;
        }
    }

    if ($type == "filter") {
        return $result;
    }
}

function base_add_hook($hookName, $type, $callback, $priority = 10, $accepted_args = 1)
{

    $hookData = [
        'type' => $type,
        'callback' => $callback,
        'priority' => $priority,
        'accepted_args' => $accepted_args
    ];

    if (isset($GLOBALS['hookList'][$hookName])) array_push($GLOBALS['hookList'][$hookName], $hookData);
    else $GLOBALS['hookList'][$hookName] = [$hookData];
}

function do_action($hookName, ...$args)
{
    $type = "action";
    return base_do_hook($hookName, $type, ...$args);
}

function add_action($hookName, $callback, $priority = 10, $accepted_args = 1)
{
    $type = "action";
    return base_add_hook($hookName, $type, $callback, $priority, $accepted_args);
}

function apply_filters($hookName, ...$args)
{
    $type = "filter";
    return base_do_hook($hookName, $type, ...$args);
}

function add_filter($hookName, $callback, $priority = 10, $accepted_args = 1)
{
    $type = "filter";
    return base_add_hook($hookName, $type, $callback, $priority, $accepted_args);
}

// Root URL
if (isset($_SERVER['REQUEST_SCHEME'])) {
    define("ROOT_URL", $_SERVER['REQUEST_SCHEME'] . "://" . $_SERVER['SERVER_NAME']);
    define("CURRENT_URL", ROOT_URL . $_SERVER['REQUEST_URI']);
}
