<x-dashboard.cpo::header />
<x-dashboard.cpo::sidebar />
<?php
require_once getDashboardViewPath("component/form/delete.form.blade.php");
$route = getTheRoute("comments", $action, $route_args);
?>

<form action="{{$route}}" method="post" id="main-form">
    @csrf
    @if($action == "edit")
    @method('patch')
    @endif
    <div class="row">
        <div class="col-12 col-sm-12 col-md-12 col-lg-8 col-xl-8">
            <div class="row">
                <div class="input-wrapper col-12 col-sm-12 col-md-6">
                    <label for="fullname" class="{{trnsAlignBlockCls()}}">{{__local("Fullname")}}</label>
                    <input class="form-control mb-3" value="{{getValueFromOldOrDB('fullname', $DB)}}" id="fullname" name="fullname" type="text" data-label="{{__local('Fullname')}}" required="required">
                </div>


                <div class="input-wrapper col-12 col-sm-12 col-md-6">
                    <label for="email" class="{{trnsAlignBlockCls()}}">{{__local('Email')}}</label>
                    <input class="form-control mb-3 text-left" value="{{getValueFromOldOrDB('email', $DB)}}" id="email" name="email" type="text" data-label="{{__local('Email')}}" required="required">
                </div>

                <div class="input-wrapper col-12 col-sm-12 col-md-6">
                    <label for="title" class="{{trnsAlignBlockCls()}}">{{__local('Title')}}</label>
                    <input class="form-control mb-3" value="{{getValueFromOldOrDB('title', $DB)}}" id="title" name="title" type="text" data-label="{{__local('Title')}}" required="required" readonly>
                </div>

                <div class="input-wrapper col-12 col-sm-12 col-md-6">
                    <label for="reply_to" class="{{trnsAlignBlockCls()}}">{{__local('Reply To')}}</label>
                    <input class="form-control mb-3" value="{{getTypeAttr(getCommentReplyTo($DB) , 'fullname')}}" id="reply_to" name="reply_to" type="text" data-label="{{__local('Reply To')}}" required="required" readonly>
                </div>

                <div class="input-wrapper col-12 mt-4">
                    <textarea name="content" id="content" class="editor w-100" data-label="{{__local('body content')}}" placeholder="{{__local('Content')}}">{!!getValueFromOldOrDB('content', $DB)!!}</textarea>
                </div>


                <!-- hidden data -->
                <input type="hidden" value="{{getTypeAttr($DB , 'post_type_id')}}" name="post_type_id" id="post_type_id">


                <?php do_action("comment_{$comment_type}_{$action}_col_left" , $DB ??[]) ?>
                <?php do_action("comment_{$action}_col_left", $DB ??[]) ?>

            </div>
        </div>

        <div class="col-12 col-sm-12 col-md-12 col-lg-4 col-xl-4">

            <div class="input-wrapper n-border p-3">
                <label class="{{trnsAlignBlockCls()}}" for="status">{{__local('Status')}}</label>

                <select name="status" class="select2-simple w-100" id="status" data-label="{{__local('Status')}}">
                    <?= join("", getStatusCommentOption(getValueFromOldOrDB('status', $DB))) ?>
                </select>

                <div class="text-left {{trnsAlignCls()}} mt-2">
                    <?php
                    $comment_link = get_post_type_comment_link(getTypeAttr($DB, 'post_type_id'), getTypeID($DB), getTypee($item), getTypee($item) . "_page");
                    ?>
                    @if($comment_link) <div class="wrapper"><label class="mt-2 align-top">{{__local("link")}}:</label><a class="d-inline-block link-info bg-info text-white rounded p-2 mx-2 mb-2" href="{{$comment_link}}" target="_blank"><?= __local("Go To Page") ?></a><button class="btn-clipboard btn align-top mt-1 badge badge-white" data-clipboard-text="{{$comment_link}}" type="button"><i class="bi bi-clipboard-check h5"></i></button></div>@endif
                    <div class="wrapper"><label class="mt-2 align-top">{{__local("IP")}}:</label><label class="mt-2 align-top mx-2" id="commenter_ip"><?= getTypeAttr($DB , "ip") ?></label><button class="btn-clipboard btn badge badge-white ml-2 mt-1" data-clipboard-text="<?= getTypeAttr($DB , "ip") ?>" type="button"><i class="bi bi-clipboard-check h5"></i></button></div>
                </div>

                <div class="action-button-wrapper" data-label="<?= $comment_data['label'] ?>">
                    <?php require_once getDashboardViewPath("component/buttons/add.resource.blade.php") ?>
                </div>
            </div>

            <?php do_action("comment_{$comment_type}_{$action}_col_right", $DB ??[]) ?>
            <?php do_action("comment_{$action}_col_right", $DB ??[]) ?>

        </div>

    </div>
</form>


<x-dashboard.cpo::footer />