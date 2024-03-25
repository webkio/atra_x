@props(['DB' => $attributes->get('DB')])
<?php
$route = getTheRoute("user", "signin", []);
$account_type_registeriation = getAllType("account_type_registeriation");
?>
<form action="{{$route}}" method="post" id="main-form">
    @csrf
    <div class="row">
        <div class="user-panel-wrapper col-12 col-sm-12 col-md-12 col-lg-4 col-xl-4">
            <div class="row">

                <div class="display-1 w-100 text-info">
                    <i class="bi bi-people-fill"></i>
                </div>

                <div class="input-wrapper mt-3 col-12">
                    <label for="client_id" class="control-label">{{__local('Username') . '/' . __local(ucwords($account_type_registeriation))}}</label>
                    <input value="{{getValueFromOldOrDB('client_id', $DB)}}" type="text" class="form-control text-left" name="client_id" id="client_id" data-label="{{__local('Username') . '/' . __local(ucwords($account_type_registeriation))}}">
                </div>

                @if($account_type_registeriation == "email")
                <div class="input-wrapper mt-3 col-12">
                    <label for="password" class="control-label">{{__local('Password')}}</label>
                    <input value="" type="password" class="form-control password-eye text-left" name="password" id="password" data-label="{{__local('Password')}}">
                </div>
                @endif

                @if (!isCaptchaDisabled("user", "sign_in"))
                <x-dashboard.upanel.forms::captcha_part_form/>
                @endif
                
                @if(getPersistDataType() == "cookie")
                <div class="input-wrapper mt-3 col-12 custom-control custom-switch">
                    <input value="yes" {{getValueFromOldOrDB('remember_me', $DB) ? "checked" : ""}} type="checkbox" class="custom-control-input" name="remember_me" id="remember_me" data-label="{{__local('Remember Me')}}">
                    <label for="remember_me" class="custom-control-label">{{__local('Remember Me')}}</label>
                </div>
                @endif

                <div class="input-wrapper mt-3 col-12">
                    <input value="{{__local('Submit')}}" type="submit" class="form-control btn btn-primary font-weight-bold" id="submit-form">
                </div>

                <div class="related-links-user text-center w-100 mt-3">
                    {!!getRelatedUserAnchors(['Sign-up' , 'Reset-Password'])!!}
                </div>

                <?php do_action("user_sign_in_col_full") ?>

            </div>
        </div>
    </div>
</form>