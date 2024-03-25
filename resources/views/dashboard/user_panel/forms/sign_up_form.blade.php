@props(['DB' => $attributes->get('DB')])
<?php
$route = getTheRoute("user", "create", []);
$account_type_registeriation = getAllType("account_type_registeriation");
?>
<form action="{{$route}}" method="post" id="main-form">
    @csrf
    <div class="row">
        <div class="user-panel-wrapper col-12 col-sm-12 col-md-12 col-lg-4 col-xl-4">
            <div class="row">

                @if($account_type_registeriation == "email")
                <div class="input-wrapper mt-3 col-12">
                    <label for="email" class="control-label">{{__local('Email')}}</label>
                    <input value="{{getValueFromOldOrDB('email', $DB)}}" type="email" class="form-control text-left on-key-event-check" data-event-name="blur" data-keyup-callback="blurCheckUser" name="email" id="email" data-label="{{__local('Email')}}">
                    <small class="text-danger error-message d-none rounded p-1 mt-1"></small>
                </div>
                @endif

                @if($account_type_registeriation == "phone")
                <div class="input-wrapper mt-3 col-12">
                    <label for="phone" class="control-label">{{__local('phone')}}</label>
                    <input value="{{getValueFromOldOrDB('phone', $DB)}}" type="text" class="form-control text-left on-key-event-check" data-event-name="blur" data-keyup-callback="blurCheckUser" name="phone" id="phone" data-label="{{__local('phone')}}" placeholder="09">
                    <small class="text-danger error-message d-none rounded p-1 mt-1"></small>
                </div>
                @endif

                <div class="input-wrapper mt-3 col-12">
                    <label for="username" class="control-label">{{__local('Username')}}</label>
                    <input value="{{getValueFromOldOrDB('username', $DB)}}" type="text" class="form-control text-left on-key-event-check" data-event-name="blur" data-keyup-callback="blurCheckUser" name="username" id="username" data-label="{{__local('Username')}}">
                    <small class="text-danger error-message d-none rounded p-1 mt-1"></small>
                </div>

                <div class="input-wrapper mt-3 col-12">
                    <label for="fullname" class="control-label">{{__local('Fullname')}}</label>
                    <input value="{{getValueFromOldOrDB('fullname', $DB)}}" type="text" class="form-control" name="fullname" id="fullname" data-label="{{__local('Fullname')}}">
                </div>

                @if($account_type_registeriation == "email")
                <div class="input-wrapper mt-3 col-12">
                    <?php 
                    $cls_password_tip = "";

                    if($GLOBALS['site_language'] == "fa_IR") $cls_password_tip .= " fa-number just-num";

                    ?>
                    <label for="password" class="control-label">{{__local('Password')}} ({{__local('Minimum')}} <span class="{{$cls_password_tip}}">{{getMinimumPassowrdCharacter()}}</span> {{__local('characters')}})</label>
                    <input value="{{getTypePassword($DB)}}" type="password" class="form-control password-eye text-left" name="password" id="password" data-label="{{__local('Password')}}">
                </div>
                @endif


                @php
                    $term = getPostTypeTermData("user","postType");
                @endphp

                @if($term)
                <div class="input-wrapper mt-3 col-12">
                    <label for="terms" class="control-label">{{__local('Terms')}}</label>
                    <div class="form-control overflow-auto" id="terms" data-label="{{__local('Terms')}}">{!!getTypeBody($term)!!}</div>
                </div>
                @endif

                @if (!isCaptchaDisabled("user", "sign_up"))
                <x-dashboard.upanel.forms::captcha_part_form />
                @endif

                <div class="input-wrapper mt-3 col-12 custom-control custom-switch">
                    <input value="yes" {{getValueFromOldOrDB('accept', $DB) ? "checked" : ""}} type="checkbox" class="custom-control-input on-check-action" data-field="#submit-form" data-callback="userPanelCheckTerm" name="accept" id="accept" data-label="Accept">
                    <label for="accept" class="custom-control-label">{{__local('Accept')}}</label>
                </div>

                <div class="input-wrapper mt-3 col-12">
                    <input value="{{__local('Submit')}}" disabled type="submit" class="form-control btn btn-primary font-weight-bold" id="submit-form">
                </div>

                <div class="related-links-user text-center w-100 mt-3">
                    {!!getRelatedUserAnchors(['Sign-in' , 'Reset-Password'])!!}
                </div>

                <?php do_action("user_sign_up_col_full") ?>

            </div>
        </div>


    </div>
</form>