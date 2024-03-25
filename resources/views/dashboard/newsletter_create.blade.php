<x-dashboard.cpo::header />
<x-dashboard.cpo::sidebar />
<?php
require_once getDashboardViewPath("component/form/delete.form.blade.php");
$route = "/";
?>

<form action="{{$route}}" method="post" id="main-form">
    @csrf
    @if($action == "edit")
    @method('patch')
    @endif
    <div class="row">
        <div class="col-12 col-sm-12 col-md-12 col-lg-8 col-xl-8">
            <div class="row">
                <div class="input-wrapper col-12">
                    <label for="client_id" class="control-label {{trnsAlignBlockCls()}}">{{__local('Client ID')}}</label>
                    <input value="{{getValueFromOldOrDB('client_id', $DB)}}" class="form-control mb-3 text-left" id="client_id" name="client_id" type="text" data-label="{{__local('Client ID')}}" placeholder="{{__local('Client ID')}}" required="required">
                </div>

                <div class="input-wrapper col-12">
                    <label for="type" class="control-label {{trnsAlignBlockCls()}}">{{__local('Type')}}</label>
                    <input value="{{getNewsletterType()[getValueFromOldOrDB('type', $DB)]?? ''}}" class="form-control mb-3" id="type" name="type" type="text" data-label="{{__local('Type')}}" placeholder="{{__local('Type')}}" readonly>
                </div>

                <?php do_action("newsletter_{$action}_col_left", $DB) ?>

            </div>
        </div>

        <div class="col-12 col-sm-12 col-md-12 col-lg-4 col-xl-4">

            <div class="input-wrapper n-border p-3">               

                @if($DB['ip'])
                <div class="text-left {{trnsAlignCls()}} mt-2">
                    <?php 
                    $ip = $DB['ip'];
                    ?>
                    <div class="wrapper"><label class="mt-2 align-top">{{__local("IP")}}:</label><label class="mt-2 align-top mx-2" id="commenter_ip"><?= getTypeAttr($DB , "ip") ?></label><button class="btn-clipboard btn badge badge-white ml-2 mt-1" data-clipboard-text="<?= getTypeAttr($DB , "ip") ?>" type="button"><i class="bi bi-clipboard-check h5"></i></button></div>
                </div>
                @endif

                <div class="action-button-wrapper" data-label="{{__local('Newsletter')}}">
                    <?php require_once getDashboardViewPath("component/buttons/add.resource.blade.php") ?>
                </div>

                
            </div>
            <?php do_action("newsletter_{$action}_col_right", $DB) ?>

        </div>

    </div>
</form>


<x-dashboard.cpo::footer />