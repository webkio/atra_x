<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\File;
use App\Models\Menu;
use App\Models\OTP;
use App\Models\PostType;
use App\Models\PostTypeMeta;
use App\Models\PostTypesTaxonomy;
use App\Models\Taxonomy;
use App\Models\User;
use App\Models\View;
use App\Models\HistoryAction;
use App\Models\Option;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Menu::truncate();
        PostTypesTaxonomy::truncate();
        PostTypeMeta::truncate();
        Taxonomy::truncate();
        PostType::truncate();
        View::truncate();
        Comment::truncate();
        File::truncate();
        User::truncate();
        OTP::truncate();
        HistoryAction::truncate();
        Option::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // favicon path list
        $favicon_url_main = getFaviconPath();
        $favicon_url = "public/static/images/favicon/default/favicon-512.png";
        $favicon_url_default = $favicon_url_main["favicon_url_default"];
        $favicon_url_current = $favicon_url_main["favicon_url_current"];

        // favicon action remove and copy
        removeFiles($favicon_url_current , ["default"]);
        copyFiles($favicon_url_default , $favicon_url_current);


        User::create([
            "username" => "arman",
            "email" => "arman@gm.com",
            "phone" => "system",
            "fullname" => "arman zarin",
            "password" => bcrypt("1234"),
            "role" => "super_admin",
            "status" => "active"
        ]);

        // #option site bio
        Option::create([
            "key" => "site_name",
            "value" => "RpdCMS",
            "group_key" => "built_in_option"
        ]);

        Option::create([
            "key" => "site_description_raw",
            "value" => "a Powerful Cms For Advanced Blogger , Shop Admins , etc ...",
            "group_key" => "built_in_option"
        ]);

        Option::create([
            "key" => "site_time_zone",
            "value" => "Asia/Tehran",
            "group_key" => "built_in_option"
        ]);

        Option::create([
            "key" => "site_language",
            "value" => "fa_IR",
            "group_key" => "built_in_option"
        ]);

        Option::create([
            "key" => "favicon_url",
            "value" => $favicon_url,
            "group_key" => "built_in_option"
        ]);

        Option::create([
            "key" => "theme_color",
            "value" => "#01af37",
            "group_key" => "built_in_option"
        ]);

        Option::create([
            "key" => "theme_color_hover",
            "value" => "rgba(190, 250, 209, 0.8)",
            "group_key" => "built_in_option"
        ]);

        // #option dashboard listing per page 
        Option::create([
            "key" => "record_per_page",
            "value" => 10,
            "group_key" => "built_in_option"
        ]);

        // #option front end type per page 
        Option::create([
            "key" => "type_per_page",
            "value" => 9,
            "group_key" => "built_in_option"
        ]);

        // #option comment
        Option::create([
            "key" => "comment_per_page",
            "value" => 15,
            "group_key" => "built_in_option"
        ]);

        Option::create([
            "key" => "comment_max_depth",
            "value" => 3,
            "group_key" => "built_in_option"
        ]);

        Option::create([
            "key" => "comment_must_be_login",
            "value" => false,
            "group_key" => "built_in_option"
        ]);

        // #option user
        Option::create([
            "key" => "account_type_registeriation",
            "value" => "email",
            "group_key" => "built_in_option"
        ]);

        Option::create([
            "key" => "user_max_attempt",
            "value" => 3,
            "group_key" => "built_in_option"
        ]);

        Option::create([
            "key" => "newsletter_valid_domain",
            "value" => '*',
            "group_key" => "built_in_option"
        ]);


         // #option social links
        Option::create([
            "key" => "social_links",
            "value" => '[{"title":{"value":"","id":"social-title-14104-1658750456"},"url":{"value":"","id":"social-url-7432-1658750456"},"icon":{"value":"","id":"social-icon-11770-1658750456"}}]',
            "group_key" => "built_in_option"
        ]);
        

        // #option security
        Option::create([
            "key" => "captcha_disable",
            "value" => '{"user":{"sign_in":false,"sign_up":false,"reset_password":false}}',
            "group_key" => "built_in_option"
        ]);

        Option::create([
            "key" => "site_ssl_force",
            "value" => '0',
            "group_key" => "built_in_option"
        ]);

        Option::create([
            "key" => "site_maintenance_activate",
            "value" => '0',
            "group_key" => "built_in_option"
        ]);
        

        Option::create([
            "key" => "cookie_policy",
            "value" => 'We use cookies to ensure you have the best browsing experience on our website. By using our site, you acknowledge that you have read and understood our <a href="#">Cookie Policy</a>',
            "group_key" => "built_in_option"
        ]);

        
        // add manifest file
        addSiteManifestFile();

    }
}
