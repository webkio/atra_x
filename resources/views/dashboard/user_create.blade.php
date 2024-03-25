<x-dashboard.cpo::header />
<x-dashboard.cpo::sidebar />

<?php
require_once getDashboardViewPath("component/form/delete.form.blade.php");

$route = getTheRoute("user", $action, $route_args);
$account_type_registeriation = getAllType("account_type_registeriation");
?>

<form action="{{$route}}" method="post" id="main-form">
    @csrf
    @if($action == "edit")
    @method('patch')
    @endif
    <div class="row">
        <?php
        $status_db = getTypeStatus($DB);
        $role_db = getTypeRole($DB);

        ?>
        <div class="col-12 col-sm-12 col-md-12 col-lg-8 col-xl-8">
            <div class="row">

                @if($account_type_registeriation == "email")
                <div class="input-wrapper mt-1 col-12 col-sm-12 col-md-6">
                    <label for="email" class="control-label {{trnsAlignBlockCls()}}">{{__local('Email')}}</label>
                    <input value="{{getValueFromOldOrDB('email', $DB)}}" type="email" class="form-control text-left" name="email" id="email" data-label="{{__local('Email')}}">
                    @if(getTypeVerified($DB , "email") != true && $DB)
                    <small class="bg-warning mt-2 d-block p-1 {{trnsAlignCls()}}">{!! messageNotVerifiedYet('email') !!}</small>
                    @elseif($DB)
                    <small class="bg-warning mt-2 d-block p-1 {{trnsAlignCls()}}">{{__local('if changed you will logout to verify your account')}}</small>
                    @endif
                </div>
                @endif

                @if($account_type_registeriation == "phone")
                <div class="input-wrapper mt-1 col-12 col-sm-12 col-md-6">
                    <label for="phone" class="control-label {{trnsAlignBlockCls()}}">{{__local('phone')}}</label>
                    <input value="{{getValueFromOldOrDB('phone', $DB)}}" type="phone" class="form-control text-left" name="phone" id="phone" data-label="{{__local('phone')}}">
                 
                    @if(getTypeVerified($DB , "phone") != true && $DB)
                    <small class="bg-warning mt-2 d-block p-1 {{trnsAlignCls()}}">{!! messageNotVerifiedYet('phone') !!}</small>
                    @elseif($DB)
                    <small class="bg-warning mt-2 d-block p-1 {{trnsAlignCls()}}">{{__local('if changed you will logout to verify your account')}}</small>
                    @endif
                </div>
                @endif

                <div class="input-wrapper mt-1 col-12 col-sm-12 col-md-6">
                    <label for="username" class="control-label {{trnsAlignBlockCls()}}">{{__local('Username')}}</label>
                    <input value="{{getValueFromOldOrDB('username', $DB)}}" type="text" class="form-control text-left" name="username" id="username" data-label="{{__local('Username')}}">
                </div>

                <div class="input-wrapper mt-1 col-12 col-sm-12 col-md-<?= $account_type_registeriation == "phone" ? "12" : "6" ?>">
                    <label for="fullname" class="control-label {{trnsAlignBlockCls()}}">{{__local('Fullname')}}</label>
                    <input value="{{getValueFromOldOrDB('fullname', $DB)}}" type="text" class="form-control" name="fullname" id="fullname" data-label="{{__local('Fullname')}}">
                </div>

                @if($account_type_registeriation == "email")
                <div class="input-wrapper mt-1 mb-3 col-12 col-sm-12 col-md-6">
                    <label for="password" class="control-label {{trnsAlignBlockCls()}}">{{__local('Password')}}</label>
                    <input value="{{getTypePassword($DB)}}" type="password" class="form-control password-eye text-left" name="password" id="password" data-label="{{__local('Password')}}">
                    @if($the_ID)
                    <small class="bg-warning position-absolute mt-2 p-1 d-block {{trnsAlignCls()}}">{{getNoticePasswordChange()}}</small>
                    @endif
                </div>
                @endif

                

                @if($the_ID)
                <div class="input-wrapper mt-5 col-12 col-sm-12 col-md-6 color-picker colorpicker-element position-relative">
                    <label for="theme_color" class="control-label {{trnsAlignBlockCls()}}">{{__local('Theme Color')}}</label>
                    <input value="{{getValueFromOldOrDB('theme_color', $DB)}}" type="text" class="form-control text-left" name="theme_color" id="theme_color" data-label="{{__local('Theme Color')}}">
                    <span class="input-group-append position-absolute" style="right: 16px; bottom: 0;">
                        <span class="input-group-text colorpicker-input-addon"><i></i></span>
                    </span>
                </div>

                <div class="input-wrapper mt-5 col-12 col-sm-12 col-md-6 color-picker colorpicker-element position-relative">
                    <label for="theme_color_hover" class="control-label {{trnsAlignBlockCls()}}">{{__local('Theme Color (Hover)')}}</label>
                    <input value="{{getValueFromOldOrDB('theme_color_hover', $DB)}}" type="text" class="form-control text-left" name="theme_color_hover" id="theme_color_hover" data-label="{{__local('Theme Color (Hover)')}}">
                    <span class="input-group-append position-absolute" style="right: 16px; bottom: 0;">
                        <span class="input-group-text colorpicker-input-addon"><i></i></span>
                    </span>
                </div>
                @endif

                <div class="input-wrapper role mt-1 col-12 col-sm-12 col-md-6">
                    <label for="role" class="control-label {{trnsAlignBlockCls()}}"><span class="d-inline-block">{{__local('Role')}}</span> <?php if ($role_db) : ?><small><b class="bg-primary text-white p-1 rounded">{{getUserRoles()[$role_db]}}</b></small><?php endif; ?></label>

                    <select name="role" id="role" class="select2 select2-simple" data-label="{{__local('Role')}}">
                        <?= join("", getUserRolesOption(getValueFromOldOrDB('role', $DB), $levels)) ?>
                    </select>
                </div>

                <div class="input-wrapper col-12 mt-4">
                    <label for="description" class="{{trnsAlignBlockCls()}}">{{__local('Description')}}</label>
                    <textarea name="description" id="description" class="form-control w-100" rows="7" placeholder="{{__local('Description')}}">{{getValueFromOldOrDB('description', $DB)}}</textarea>
                </div>

                <?php do_action("user_add_col_left") ?>

            </div>
        </div>

        <div class="col-12 col-sm-12 col-md-12 col-lg-4 col-xl-4">

            <div class="input-wrapper n-border p-3" data-label="{{__local('User')}}">
                <label class="{{trnsAlignBlockCls()}}" for="status">{{__local('Status')}}</label>
                <select name="status" id="status" class="select2-simple w-100" data-label="{{__local('Status')}}">
                    <?= join("", getStatusUserOption(getValueFromOldOrDB('status', $DB), $status_s)) ?>
                </select>
                <?php if ($status_db) : ?>
                    <small class="mt-2 d-block {{trnsAlignCls()}}"><span class="d-inline-block default-color">{{__local('current status')}} : </span> <b class="bg-primary text-white p-1 rounded">{{getStatusUser()[$status_db]}}</b></small>
                <?php endif; ?>
                <?php require_once getDashboardViewPath("component/buttons/add.resource.blade.php"); ?>
            </div>

            <div class="input-wrapper thumbnails mt-3 n-border p-3">
                <label class="control-label {{trnsAlignBlockCls()}}">{{__local('Preview')}}</label>
                <center id="thumbnails-preview">
                    <div class="wrapper-thumbnail-preview">
                        <img class="thumbnail-preview rounded-circle" width="100" src="{{getUserGravatarByEmail(getTypeAttr($DB, 'email' , null) , 100)}}">
                    </div>
                </center>

            </div>

            <?php do_action("user_add_col_right") ?>

        </div>

    </div>
</form>


<x-dashboard.cpo::footer />