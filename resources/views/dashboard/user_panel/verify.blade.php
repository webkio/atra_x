<x-dashboard.cpo::header />

<!-- Right Content -->
<div class="col-12 pt-3">

    <?php

    $route_success = getTheRoute("user", "signin.form", []);
    ?>

    <form action="" method="post" id="main-form" data-success-redirect="{{$route_success}}" onkeypress="if(event.keyCode == 13){$('#submit-form').trigger('click');return false;}">
        @csrf
        <div class="row">
            <div class="user-panel-wrapper col-12 col-sm-12 col-md-12 col-lg-4 col-xl-4">
                <div class="row">

                    <div class="display-1 w-100 text-info">
                        <i class="bi bi-file-lock2-fill"></i>
                    </div>
                    <?php
                    $_otp_message = OTPType('description')[getTypee($DB) . "_" .  getTypeAttr($_GET , "action")] ?? "";
                    $otp_message = $_otp_message ? $_otp_message : OTPType('description')[getTypee($DB)];
                    $expire_seconds = diffUnixTwoDate(dateToUnixTime(getTypeDateExpired($DB)));
                    ?>
                    <p class="bg-warning font-weight-bold rounded col-12">{!!getOTPMessageForm(getTypeAttr($DB , 'via'),$otp_message , $expire_seconds)!!}</p>

                    <div class="input-wrapper mt-3 col-12">
                        <label for="client_code" class="control-label">{{__local("Code")}}</label>
                        <input value="{{$hexdec}}" type="number" class="form-control text-left on-key-event-check" data-event-name="keyup" data-keyup-callback="keyupCheckPinCode" name="client_code" id="client_code" data-label="Code">
                    </div>

                    <div class="input-wrapper col-12">
                        <input value="{{$route_args['id']}}" type="hidden" class="form-control" name="id" id="id" data-label="ID">
                    </div>

                    <div class="input-wrapper mt-3 col-12">
                        <input value="{{__local('Submit')}}" disabled type="submit" class="form-control btn btn-primary font-weight-bold" id="submit-form">
                    </div>

                    <div class="related-links-user text-center w-100 mt-3">
                        {!!getRelatedUserAnchors(['Sign-in' , 'Sign-up' , 'Reset-Password'])!!}
                    </div>

                    <?php do_action("user_verify_col_full") ?>

                </div>
            </div>


        </div>
    </form>

    @if($hexdec)
    <script>
        window.onload = function() {
            const clientCodeDOM = $("#client_code");
            const submitDOM = $("#submit-form");
            setTimeout(function() {
                clientCodeDOM.trigger("keyup");
                submitDOM.trigger("click")
            }, 1000)
        };
    </script>
    @endif
    <x-dashboard.cpo::footer />