<x-dashboard.cpo::header />
<x-dashboard.cpo::sidebar />
<?php
require_once getDashboardViewPath("component/form/delete.form.blade.php");
$route = getTheRoute("redirect", $action, $route_args);
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
                    <label for="title" class="control-label {{trnsAlignBlockCls()}}">{{__local("Title")}}</label>
                    <input value="{{getValueFromOldOrDB('title', $DB)}}" class="form-control mb-3" id="title" name="title" type="text" data-label="{{__local('Title')}}" placeholder="{{__local('Title')}}" required="required">
                </div>

                <div class="input-wrapper col-12">
                    <label for="from" class="control-label {{trnsAlignBlockCls()}}">{{__local("From Path")}}</label>
                    <input value="{{getValueFromOldOrDB('from', $DB)}}" dir="ltr" class="form-control text-left mb-3" id="from" name="from" type="text" data-label="{{__local('From Path')}}" placeholder="{{__local('/post/1/my-first-post')}}" required="required">
                </div>

                <div class="input-wrapper col-12">
                    <label for="to" class="control-label {{trnsAlignBlockCls()}}">{{__local("To Path")}}</label>
                    <input value="{{getValueFromOldOrDB('to', $DB)}}" dir="ltr" class="form-control text-left mb-3" id="to" name="to" type="text" data-label="{{__local('To Path')}}" placeholder="{{__local('/post/2/my-second-post')}}" required="required">
                </div>

                <?php do_action("redirect_{$action}_col_left", $DB) ?>

            </div>
        </div>

        <div class="col-12 col-sm-12 col-md-12 col-lg-4 col-xl-4">

            <div class="input-wrapper n-border p-3">
                <label class="title-introduce {{trnsAlignBlockCls()}}" for="http_code">{{__local("HTTP CODE")}}</label>

                <select name="http_code" class="select2-simple w-100" id="http_code" data-label="{{__local('HTTP CODE')}}">
                    <?= join("", getHttpCodeRedirectOption(getValueFromOldOrDB('http_code', $DB))) ?>
                </select>

                @if($action == "edit")
                <div class="text-left {{trnsAlignCls()}} mt-3">
                    <?php
                    $link = str_replace("/*" , "/101" , "/{$DB['from']}");
                    ?>
                    <label>{{__local("link")}}:</label><a class="link-info bg-info text-white rounded p-2 mx-2 mb-2" href="<?= $link ?>" target="_blank"><?= __local("Show Redirect") ?></a><button class="btn-clipboard btn badge badge-white align-top" data-clipboard-text="<?= $link ?>" type="button"><i class="bi bi-clipboard-check h5"></i></button>

                </div>
                @endif

                <div class="action-button-wrapper" data-label="{{__local('Redirect')}}">
                    <?php require_once getDashboardViewPath("component/buttons/add.resource.blade.php") ?>
                </div>
            </div>


            <?php do_action("redirect_{$action}_col_right", $DB) ?>

        </div>

    </div>
</form>


<x-dashboard.cpo::footer />