@props(['DB' => $attributes->get('DB') , 'route_args' => $attributes->get('route_args')])
<?php
$route = getTheRoute("user", "reset.password", $route_args);
$client_id_label = getClientIDLabels(getAllType("account_type_registeriation"));
?>
<form action="{{$route}}" method="post" id="main-form" data-success-redirect="">
    @csrf
    <div class="row">
        <div class="user-panel-wrapper col-12 col-sm-12 col-md-12 col-lg-4 col-xl-4">
            <div class="row">

                <div class="display-1 w-100 text-info">
                    <i class="bi bi-asterisk"></i>
                </div>
                
                <p class="col-12 bg-warning rounded">{{__local("to reset password enter your $client_id_label or Username")}}</p>

                <div class="input-wrapper mt-3 col-12">
                    <input value="{{$route_args['via']}}" type="hidden" class="form-control" name="client_via" id="client_via" data-label="{{__local('Client Via')}}">
                </div>

                <div class="input-wrapper mt-3 col-12">
                    <label for="client_id" class="control-label">{{__local('Username') . '/' . __local($client_id_label)}}</label>
                    <input value="{{old('client_id')}}" type="text" class="form-control text-left on-key-event-check" data-event-name="mouseleave" data-keyup-callback="checkUserResetCredentials" name="client_id" id="client_id" data-label="{{__local('Username') . '/' . __local($client_id_label)}}">
                </div>

                @if (!isCaptchaDisabled("user", "reset_password"))
                <x-dashboard.upanel.forms::captcha_part_form />
                @endif

                <div class="input-wrapper mt-3 col-12">
                    <input value="{{__local('Submit')}}" type="submit" class="form-control btn btn-primary font-weight-bold" id="submit-form">
                </div>

                <div class="related-links-user text-center w-100 mt-3">
                    {!!getRelatedUserAnchors(['Sign-in' , 'Sign-up'])!!}
                </div>

                <?php do_action("user_reset_password_email_col_full") ?>

            </div>
        </div>


    </div>
</form>