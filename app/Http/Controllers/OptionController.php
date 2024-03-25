<?php

namespace App\Http\Controllers;

use App\Models\Option;

class OptionController extends Controller
{
    public function edit()
    {           
      
        $options = getOptionBuiltIn();
        do_action("settings_edit_action", $options);

        // make it global for accessing title and other data
        generateGlobalTitle(new Option);

        return view("dashboard.settings" , [
            'DB' => $options,
            'action' => __FUNCTION__,
            'route_args' => []
        ]);
    }

    public function update()
    {
        
        $inputs = cleanTheArray(request()->post(), false);

        // switches
        $inputs['comment_must_be_login'] = intval(isset($inputs['comment_must_be_login']));
        $inputs['site_ssl_force'] = intval(isset($inputs['site_ssl_force']));
        $inputs['site_maintenance_activate'] = intval(isset($inputs['site_maintenance_activate']));

        do_action("before_settings_" . __FUNCTION__ . "_check", $inputs);

        // error handling
        $error = validationUserFormInputs($inputs);
        if ($error['message']) {
            return triggerServerError($error);
        }

        // fix slash
        $inputs['favicon_url'] = slashFixerPath($inputs['favicon_url']);

        // check thumbnail exists
        $checkFileRes = checkFileExistsByMessage($inputs , "favicon_url");
        if(is_object($checkFileRes)){
            return $checkFileRes;
        }

        // check social links
        $res = checkSocialLinks($inputs['social_links']);
        if(is_object($res)) return $res;

        do_action("before_settings_resize_favicon_" . __FUNCTION__);
        
        // resize favicon
        $thumbnail_url_res = genereateSubImageByCondition($inputs['favicon_url'],"public/static/images/favicon" , getFaviconSizes() , [512 , 512] , "image/png");
        if(is_string($thumbnail_url_res)){
            return triggerServerError(getUserMessageValidate($thumbnail_url_res . " (x-field)" , ["favicon_url"]));
        }

        do_action("after_settings_resize_favicon_" . __FUNCTION__ , $thumbnail_url_res);
        do_action("after_settings_" . __FUNCTION__ . "_check", $inputs);
        
        
        // update settings
        $Option = new Option;
        $Option->updateGrouply([
            "site_name" => $inputs['site_name'],
            "site_description_raw" => $inputs['site_description_raw'],
            "site_time_zone" => $inputs['site_time_zone'],
            "site_language" => $inputs['site_language'],
            "favicon_url" => $inputs['favicon_url'],
            "theme_color" => $inputs['theme_color'],
            "theme_color_hover" => $inputs['theme_color_hover'],
            "record_per_page" => $inputs['record_per_page'],
            "type_per_page" => $inputs['type_per_page'],
            "comment_per_page" => $inputs['comment_per_page'],
            "comment_max_depth" => $inputs['comment_max_depth'],
            "comment_must_be_login" => $inputs['comment_must_be_login'],
            "user_max_attempt" => $inputs['user_max_attempt'],
            "account_type_registeriation" => $inputs['account_type_registeriation'],
            "newsletter_valid_domain" => $inputs['newsletter_valid_domain'],
            "social_links" => $inputs['social_links'],
            "captcha_disable" => $inputs['captcha_disable'],
            "site_ssl_force" => $inputs['site_ssl_force'],
            "site_maintenance_activate" => $inputs['site_maintenance_activate'],
            "cookie_policy" => $inputs['cookie_policy'],
        ]);

        do_action("settings_successfully_" . __FUNCTION__ . "d", $GLOBALS['options'], $inputs);

        // generate manifest
        $manifest_content = addSiteManifestFile();

        do_action("settings_manifest_successfully_" . __FUNCTION__ . "d", $manifest_content);
        
        return redirect(getTypeEditLink($Option, $GLOBALS['current_page'], []));
    }
}
