<x-dashboard.cpo::header />
<x-dashboard.cpo::sidebar />
<?php
$route = getTheRoute($GLOBALS['current_page'], $action, $route_args);
?>

<form action="{{$route}}" method="post" id="main-form">
    @csrf
    @if($action == "edit")
    @method('patch')
    @endif
    <div class="row">
        <div class="tabs col-12">
            <!-- Nav Tab -->
            <ul class="nav nav-tabs nav-fill nav-pills p-0" role="tablist">
                <li class="nav-item">
                    <a class="nav-link " data-toggle="tab" href="#setting-tag-1" role="tab">
                        <span class="d-block">{{__local('Bio')}}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#setting-tag-2" role="tab">
                        <span class="d-block">{{__local('items Count')}}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#setting-tag-3" role="tab">
                        <span class="d-block">{{__local('Comments')}}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#setting-tag-4" role="tab">
                        <span class="d-block">{{__local('User')}}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#setting-tag-5" role="tab">
                        <span class="d-block">{{__local('Social Media')}}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#setting-tag-6" role="tab">
                        <span class="d-block">{{__local('Security')}}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" data-toggle="tab" href="#setting-tag-7" role="tab">
                        <span class="d-block">{{__local('تنظیمات سفارشی')}}</span>
                    </a>
                </li>

            </ul>
            <!-- END Nav Tab -->

            <!-- Tab panes -->
            <div class="tab-content p-3 text-muted">
                <div class="tab-pane " id="setting-tag-1" role="tabpanel">
                    <div class="row">
                        <div class="input-wrapper col-12 col-sm-12 col-md-12 col-lg-6">
                            <label for="site_name" class="{{trnsAlignBlockCls()}}">{{__local('Site Name')}}</label>
                            <input type="text" id="site_name" name="site_name" class="form-control" value="{{getOptionBuiltInValueByKey($DB, "site_name", true)}}" data-label="{{__local('Site Name')}}">
                        </div>

                        <div class="input-wrapper col-12 col-sm-12 col-md-12 col-lg-6">
                            <label for="site_time_zone" class="{{trnsAlignBlockCls()}}">{{__local('Timezone')}}</label>
                            <select class="select2 select2-simple auto-data-value" id="site_time_zone" name="site_time_zone" data-value="{{getOptionBuiltInValueByKey($DB, "site_time_zone" , true)}}" data-options='{ "minimumResultsForSearch": false }' data-label="{{__local('Timezone')}}">
                                @include("dashboard.component.parts.timezones_list")
                            </select>
                        </div>

                        <div class="input-wrapper col-12 col-sm-12 col-md-12 col-lg-6 mt-4">
                            <label for="site_language" class="{{trnsAlignBlockCls()}}">{{__local('Language')}}</label>
                            <select class="select2 select2-simple auto-data-value" id="site_language" name="site_language" data-value="{{getOptionBuiltInValueByKey($DB, "site_language" , true)}}" data-options='{ "minimumResultsForSearch": false }' data-label="{{__local('Language')}}">
                                {!!join("\n", getLanguageListOption())!!}
                            </select>
                        </div>

                        <div class="input-wrapper col-12 mt-4">
                            <label for="site_description" class="{{trnsAlignBlockCls()}}">{{__local('Short Description')}}</label>
                            <textarea class="editor" name="site_description" id="site_description" data-options='{"height":200}'>{{getOptionBuiltInValueByKey($DB, "site_description_raw" , true)}}</textarea>
                            <input type="hidden" id="site_description_raw" name="site_description_raw" data-label="{{__local('Short Description')}}">
                        </div>

                        <div class="input-wrapper col-12 mt-4 thumbnails">
                            <label for="" class="{{trnsAlignBlockCls()}}">{{str_replace('x-size' , '512x512' , __local('Logo & Favicon (must be PNG and x-size)'))}}</label>
                            <center class="thumbnails-preview-logo-setting" id="thumbnails-preview">
                                <div class="wrapper-thumbnail-preview">
                                </div>
                            </center>

                            <div id="thumbnails-data">
                                <input type="hidden" value="/<?= getOptionBuiltInValueByKey($DB, "favicon_url") ?>" class="file-input" name="favicon_url" id="favicon_url" data-label="Logo & Favicon" data-button-opener=".thumbnails .openTheFileManager">
                            </div>
                            <div class="wrapper {{trnsAlignBlockCls()}}">
                                <input type="button" class="openTheFileManager btn btn-primary" value="{{__local('Select')}}" data-options='{ "multiple":false, "groupType":"image", "type" : "png", "target": "#favicon_url" , "onCloseCallback" : "previewImages" , "preview" : true , "previewSelector" : "#thumbnails-preview" }'>
                            </div>
                        </div>

                        <div class="input-wrapper col-11 col-sm-11 col-md-11 col-lg-7 mt-4 color-picker input-group colorpicker-element">
                            <label class="w-100 {{trnsAlignBlockCls()}}" for="">{{__local('Theme Color')}}</label>
                            <input type="text" name="theme_color" id="theme_color" class="form-control input-lg text-left" value="{{getOptionBuiltInValueByKey($DB, "theme_color" , true)}}" data-label="{{__local('Theme Color')}}">
                            <span class="input-group-append">
                                <span class="input-group-text colorpicker-input-addon"><i></i></span>
                            </span>
                        </div>


                        <div class="input-wrapper col-11 col-sm-11 col-md-11 col-lg-7 mt-4 color-picker input-group colorpicker-element">
                            <label class="w-100 {{trnsAlignBlockCls()}}" for="">{{__local('Theme Color (Hover)')}}</label>
                            <input type="text" name="theme_color_hover" id="theme_color_hover" class="form-control input-lg text-left" value="{{getOptionBuiltInValueByKey($DB, "theme_color_hover" , true)}}" data-label="{{__local('Theme Color (Hover)')}}">
                            <span class="input-group-append">
                                <span class="input-group-text colorpicker-input-addon"><i></i></span>
                            </span>
                        </div>

                    </div>
                </div>

                <div class="tab-pane" id="setting-tag-2" role="tabpanel">
                    <div class="row">
                        <div class="input-wrapper col-12 col-sm-12 col-md-12 col-lg-6">
                            <label for="record_per_page" class="{{trnsAlignBlockCls()}}">{{__local('Record Per Page (Dashboard Listing)')}}</label>
                            <input type="number" min="1" step="1" id="record_per_page" name="record_per_page" class="form-control text-left" value="{{getOptionBuiltInValueByKey($DB, "record_per_page" , true)}}" data-label="{{__local('Record Per Page (Dashboard Listing)')}}">
                        </div>

                        <div class="input-wrapper col-12 col-sm-12 col-md-12 col-lg-6">
                            <label for="type_per_page" class="{{trnsAlignBlockCls()}}">{{__local('*_Type Per Page (Front End Item Listing)')}}</label>
                            <input type="number" min="1" step="1" id="type_per_page" name="type_per_page" class="form-control text-left" value="{{getOptionBuiltInValueByKey($DB, "type_per_page" , true)}}" data-label="{{__local('*_Type Per Page (Front End Item Listing)')}}">
                        </div>

                    </div>
                </div>

                <div class="tab-pane" id="setting-tag-3" role="tabpanel">
                    <div class="row">

                        <div class="input-wrapper col-12 col-sm-12 col-md-12 col-lg-6">
                            <label for="comment_per_page" class="{{trnsAlignBlockCls()}}">{{__local('Comment Per Page')}}</label>
                            <input type="number" min="1" step="1" id="comment_per_page" name="comment_per_page" class="form-control text-left" value="{{getOptionBuiltInValueByKey($DB, "comment_per_page" , true)}}" data-label="{{__local('Comment Per Page')}}">
                        </div>

                        <div class="input-wrapper col-12 col-sm-12 col-md-12 col-lg-6">
                            <label for="comment_max_depth" class="{{trnsAlignBlockCls()}}">{{__local('Max Comment Depth')}}</label>
                            <input type="number" min="1" step="1" id="comment_max_depth" name="comment_max_depth" class="form-control text-left" value="{{getOptionBuiltInValueByKey($DB, "comment_max_depth" , true)}}" data-label="{{__local('Max Comment Depth')}}">
                        </div>

                        <div class="input-wrapper col-12 col-sm-12 col-md-12 col-lg-6 pt-4 mt-1 {{trnsAlignBlockCls()}}">
                            <div class="custom-control custom-switch">
                                @php $checked = getOptionBuiltInValueByKey($DB, "comment_must_be_login" , true) ? "checked" : ""; @endphp
                                <input type="checkbox" {{$checked}} class="custom-control-input" name="comment_must_be_login" id="comment_must_be_login" data-label="{{__local('users must be logged-in to send comment')}}">
                                <label class="custom-control-label" for="comment_must_be_login">{{__local('users must be logged-in to send comment')}}</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tab-pane" id="setting-tag-4" role="tabpanel">
                    <div class="row">

                        <div class="input-wrapper col-12 col-sm-12 col-md-12 col-lg-6">
                            <label for="account_type_registeriation" class="{{trnsAlignBlockCls()}}">{{__local('Account Type Registeriation')}}</label>
                            <select class="select2 select2-simple auto-data-value" id="account_type_registeriation" name="account_type_registeriation" data-value="{{getOptionBuiltInValueByKey($DB, "account_type_registeriation" , true)}}" data-options='{ "minimumResultsForSearch": -1 }' data-label="{{__local('Account Type Registeriation')}}">
                                {!!join("\n", getAccountTypeRegisteriationOption())!!}
                            </select>
                            <p class="bg-warning text-dark font-weight-bold p-2 rounded {{trnsAlignBlockCls()}}">{{__local("By choosing this option, the method of user registration/login will be different, choose carefully")}}</p>
                        </div>

                        <div class="input-wrapper col-12 col-sm-12 col-md-12 col-lg-6">
                            <label for="user_max_attempt" class="{{trnsAlignBlockCls()}}">{{__local('User Max Attempt (Limitation When User Enter Wrong Disposable Code)')}}</label>
                            <input type="number" min="1" step="1" id="user_max_attempt" name="user_max_attempt" class="form-control text-left" value="{{getOptionBuiltInValueByKey($DB, "user_max_attempt" , true)}}" data-label="{{__local('User Max Attempt (Limitation When User Enter Wrong Disposable Code)')}}">
                        </div>

                        <div class="input-wrapper col-12 col-sm-12 col-md-12 col-lg-12 mt-4">
                            <label for="newsletter_valid_domain" class="{{trnsAlignBlockCls()}}">{{__local('enter List of Valid Domains (if you want to set all domain enter *)')}} <b>({{__local('Newsletter')}})</b></label>
                            <textarea dir="ltr" id="newsletter_valid_domain" name="newsletter_valid_domain" class="form-control hi-175 text-left" data-label="{{__local('List of Valid Domains')}}" placeholder="example : gmail.com ask.com">{{getOptionBuiltInValueByKey($DB, "newsletter_valid_domain" , true)}}</textarea>
                        </div>
                    </div>
                </div>

                <div class="tab-pane" id="setting-tag-5" role="tabpanel">
                    <div class="row">
                        <div class="input-wrapper col-sm-12 col-md-12 col-lg-12 col-xl-6">
                            @php
                            $social_links = getOptionBuiltInValueByKey($DB, "social_links" , true) ?? [];
                            if($social_links){
                            $social_links = json_decode($social_links , true) ?? [];
                            }

                            $encoded_json = json_encode($social_links);

                            if($encoded_json == "\"\""){
                            $encoded_json = null;
                            }

                            @endphp
                            <p class="bg-warning text-dark rounded p-1 {{trnsAlignCls()}}">{!!__local(getCloneSettingsEmptyMessage('social media'))!!}</p>

                            <input type="hidden" name="social_links" class="the-json" value="{{$encoded_json}}" data-label="JSON ONE">
                            <div class="clonable-block" data-options='{"afterToggle" : "afterToggleClonerSocial" , "afterDelete" : "afterClonerDelete" , "pureEvent" : [{"selector" : ".child-element","event" : "input change importFileURL","callback" : "makeJsonByDataFields"}]}'>
                                <div class="wrapper {{trnsAlignBlockCls()}}">
                                    <input type="button" class="btn btn-danger mb-3" id="remove-elements" data-label="{{__local('Social Media')}}" value="{{__local('Empty Social Media')}}">
                                </div>
                                @php $k=0; @endphp
                                @foreach($social_links as $item_link)
                                <div class="clonable child-element thumbnails-social-media rounded border border-secondary p-2 mb-2" data-template-id="thumbnails-social-media-x" id="thumbnails-social-media-{{$k+1}}">
                                    @php $titleOne = $item_link['title']; @endphp
                                    <label for="{{$titleOne['id']}}" class="clonable-increment-for {{trnsAlignBlockCls()}}">{{__local('Title')}}</label>
                                    <input type="text" class="form-control clonable-increment-id title-one input-one" value="{{$titleOne['value']}}" data-group-id="title" data-field="title" id="{{$titleOne['id']}}" data-label="{{__local('Title')}}"><br>

                                    @php $urlOne = $item_link['url']; @endphp
                                    <label for="{{$urlOne['id']}}" class="clonable-increment-for {{trnsAlignBlockCls()}}">{{__local('URL')}}</label>
                                    <input type="url" class="form-control clonable-increment-id url-one input-one text-left" value="{{$urlOne['value']}}" data-group-id="url" data-field="url" id="{{$urlOne['id']}}" data-label="{{__local('URL')}}"><br>

                                    @php $iconOne = $item_link['icon']; @endphp
                                    <label for="" class="clonable-increment-for {{trnsAlignBlockCls()}}">{{__local('icon')}}</label>
                                    <center class="thumbnails-preview on-empty-all thumbnails-preview-logo-setting clonable-increment-id" id="preview-{{$iconOne['id']}}">
                                        <div class="wrapper-thumbnail-preview">
                                        </div>
                                    </center>

                                    <div id="thumbnails-data">
                                        <input type="text" value="{{$iconOne['value']}}" class="d-none file-input clonable-increment-id icon-one input-one" data-group-id="icon" data-field="icon" id="{{$iconOne['id']}}" data-button-opener="#thumbnails-social-media-{{$k+1}} .openTheFileManager" data-label="{{__local('icon')}}">
                                    </div>
                                    <div class="wrapper {{trnsAlignBlockCls()}}">
                                        <button type="button" class="openTheFileManager btn btn-primary" data-options='{ "multiple":false, "groupType":"image", "type" : "svg", "target": "#{{$iconOne['id']}}" , "onCloseCallback" : "previewImages" , "preview" : true , "previewSelector" : "#preview-{{$iconOne['id']}}" }'>{{__local('Select')}}</button>
                                    </div>

                                    <div class="action-wrapper w-100 mt-3">
                                        <button type="button" class="btn text-success clonable-button-add"><i class="h3 bi-plus-square"></i></button>
                                        <button type="button" class="btn text-danger clonable-button-close delete-action json-type"><i class="h3 bi-x-square"></i></button>
                                    </div>
                                </div>
                                @php $k++; @endphp
                                @endforeach
                            </div>

                        </div>
                    </div>
                </div>

                <div class="tab-pane" id="setting-tag-6" role="tabpanel">
                    <div class="row">

                        <div class="input-wrapper json-switch-value border rounded p-4 col-12 {{trnsAlignBlockCls()}}">
                            @php $captchaList = json_decode(getOptionBuiltInValueByKey($DB, "captcha_disable" , true),true); @endphp
                            <input type="hidden" class="the-value-data" name="captcha_disable" id="captcha_disable" value="{{json_encode($captchaList)}}" data-label="{{__local('Disable Captcha in Form')}}">
                            <label for="">{{__local('Disable Captcha in Form')}}</label>
                            <div class="row">
                                <div data-page="user" class="data-page-one col-12">
                                    <h2 class="border-bottom">{{__local('User')}}</h2>
                                    <div class="child-element custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input the-value" id="sign_in">
                                        <label class="custom-control-label" for="sign_in">{{__local('Disable Sign In')}}</label>
                                    </div>

                                    <div class="child-element custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input the-value" id="sign_up">
                                        <label class="custom-control-label" for="sign_up">{{__local('Disable Sign Up')}}</label>
                                    </div>

                                    <div class="child-element custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input the-value" id="reset_password">
                                        <label class="custom-control-label" for="reset_password">{{__local('Disable Reset Password')}}</label>
                                    </div>

                                </div>
                            </div>

                        </div>


                        <div class="input-wrapper col-12 col-sm-12 col-md-12 col-lg-6 pt-4 mt-1 {{trnsAlignBlockCls()}}">
                            <div class="custom-control custom-switch">
                                @php $checked = getOptionBuiltInValueByKey($DB, "site_ssl_force" , true) ? "checked" : ""; @endphp
                                <input type="checkbox" {{$checked}} class="custom-control-input" name="site_ssl_force" id="site_ssl_force" data-label="{{__local('Force HTTP To HTTPS')}}">
                                <label class="custom-control-label" for="site_ssl_force">{{__local('Force HTTP To HTTPS')}}</label>
                            </div>
                        </div>

                        <div class="input-wrapper col-12 col-sm-12 col-md-12 col-lg-6 pt-4 mt-1 {{trnsAlignBlockCls()}}">
                            <div class="custom-control custom-switch">
                                @php $checked = getOptionBuiltInValueByKey($DB, "site_maintenance_activate" , true) ? "checked" : ""; @endphp
                                <input type="checkbox" {{$checked}} class="custom-control-input" name="site_maintenance_activate" id="site_maintenance_activate" data-label="{{__local('Activate Maintenance Mode')}}">
                                <label class="custom-control-label" for="site_maintenance_activate">{{__local('Activate Maintenance Mode')}}</label>
                            </div>
                        </div>

                        <div class="input-wrapper col-12 p-1 mt-4">
                            <label for="cookie_policy" class="{{trnsAlignBlockCls()}}">{{__local('Cookie Policy Message')}} {{__local('(to disable enter ####)')}}</label>
                            <textarea class="editor" name="cookie_policy" id="cookie_policy" data-options='{"height":200}'>{{getOptionBuiltInValueByKey($DB, "cookie_policy" , true)}}</textarea>
                            <input type="hidden" id="cookie_policy_raw" name="cookie_policy_raw" data-label="{{__local('Cookie Policy Message')}}">
                        </div>


                    </div>
                </div>
                <div class="tab-pane active" id="setting-tag-7" role="tabpanel">
                    <div class="row">

                        @include('dashboard.custom_settings.cs_company_info')
                        @include('dashboard.custom_settings.cs_top_banners_home')
                        @include('dashboard.custom_settings.cs_about')
                        @include('dashboard.custom_settings.cs_services')
                        @include('dashboard.custom_settings.cs_choose')
                        @include('dashboard.custom_settings.cs_team')
                        @include('dashboard.custom_settings.cs_testimonial')
                        @include('dashboard.custom_settings.cs_product')
                        
                     

                        
                        

                    </div>
                </div>

            </div>
            <!-- END Tab panes -->
        </div>

    </div>
    <div class="submit-wrapper text-center">
        <input type="submit" value="{{__local('Update')}}" class="btn btn-lg btn-success">
    </div>
</form>

<x-dashboard.cpo::footer />