<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // pagination bootstrap
        Paginator::useBootstrap();

        // unguard Model
        Model::unguard();

        // dashboard prefix blade components
        Blade::anonymousComponentNamespace("dashboard", "dashboard");
        Blade::anonymousComponentNamespace("dashboard.component", "dashboard.cpo");
        Blade::anonymousComponentNamespace("dashboard.parts", "dashboard.prt");
        Blade::anonymousComponentNamespace("dashboard.parts.form", "dashboard.prt.frm");
        Blade::anonymousComponentNamespace("dashboard.parts.buttons", "dashboard.prt.btn");

        Blade::anonymousComponentNamespace("dashboard.user_panel", "dashboard.upanel");
        Blade::anonymousComponentNamespace("dashboard.user_panel.forms", "dashboard.upanel.forms");


        if (hasTable("options")) {
            // set options from Database
            $options = getOptionBuiltIn();

            $options = [
                // bio
                "site_name" => getOptionBuiltInValueByKey($options, "site_name"),
                "site_description_raw" => getOptionBuiltInValueByKey($options, "site_description_raw"),
                "site_time_zone" => getOptionBuiltInValueByKey($options, "site_time_zone"),
                "site_language" => getOptionBuiltInValueByKey($options, "site_language"),
                "favicon_url" => getOptionBuiltInValueByKey($options, "favicon_url"),
                "theme_color" => getOptionBuiltInValueByKey($options, "theme_color"),
                "theme_color_hover" => getOptionBuiltInValueByKey($options, "theme_color_hover"),

                // listing
                "record_per_page" => getOptionBuiltInValueByKey($options, "record_per_page"),
                "type_per_page" => getOptionBuiltInValueByKey($options, "type_per_page"),

                // comment
                "comment_per_page" => getOptionBuiltInValueByKey($options, "comment_per_page"),
                "comment_max_depth" => getOptionBuiltInValueByKey($options, "comment_max_depth"),
                "comment_must_be_login" => getOptionBuiltInValueByKey($options, "comment_must_be_login"),
                // user
                "user_max_attempt" => getOptionBuiltInValueByKey($options, "user_max_attempt"),
                "account_type_registeriation" => getOptionBuiltInValueByKey($options, "account_type_registeriation"),
                "newsletter_valid_domain" => explode(" ", getOptionBuiltInValueByKey($options, "newsletter_valid_domain")),
                // social media
                "social_links" => collect(json_decode(getOptionBuiltInValueByKey($options, "social_links"), true)),
                // security
                "captcha_disable" => json_decode(getOptionBuiltInValueByKey($options, "captcha_disable"), true),
                "site_ssl_force" => getOptionBuiltInValueByKey($options, "site_ssl_force"),
                "site_maintenance_activate" => getOptionBuiltInValueByKey($options, "site_maintenance_activate"),
            ];

            $options['newsletter_valid_domain'] = $options['newsletter_valid_domain'][0] === "*" ? last($options['newsletter_valid_domain']) : $options['newsletter_valid_domain'];

            $GLOBALS["options"] = $options;

            setGlobalsOption($options);

            $GLOBALS['lang'] = (getLanguageList("*"))[$GLOBALS["site_language"]];

            $GLOBALS['db_settings_set'] = true;

            // set stuff
            set_global_dashboard_nav();
            set_type_entity();

            do_action("init");

            if (isDashboard()) {
                do_action("start_dashboard_init");
            } else {
                do_action("start_front_init");
            }
        }

        // set any http request
        anyHttpRequest();

        // set time zone (becuase cannot access time zone from config/app.php for set data by DB)
        date_default_timezone_set(getAllType("site_time_zone") ?: "UTC");
    }
}
