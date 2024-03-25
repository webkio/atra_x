<x-dashboard.cpo::header />
<x-dashboard.cpo::sidebar />
<?php
require_once getDashboardViewPath("component/form/delete.form.blade.php");
$route = getTheRoute("taxonomy", $action, $route_args);


?>

<form action="{{$route}}" method="post" id="main-form">
    @csrf
    @if($action == "edit")
    @method('patch')
    @endif
    <div class="row">
        <?php
        $hasThubnail = ($the_ID && $DB->thumbnail_url);
        $thumb_url = getValueFromOldOrDB('thumbnail_url', $DB);
        $thumbnail = $thumb_url ? "value={$thumb_url}" : "data-placeholder=true";
        ?>
        <div class="col-12 col-sm-12 col-md-12 col-lg-8 col-xl-8">
            <div class="row">

                <div class="input-wrapper col-12">
                    <label for="title" class="control-label {{trnsAlignBlockCls()}}">{{__local("Title")}}</label>
                    <input value="{{getValueFromOldOrDB('title', $DB)}}" class="form-control mb-3" id="title" name="title" type="text" data-label="{{__local('Title')}}" placeholder="{{__local('Title')}}" required="required">
                </div>

                <div class="input-wrapper col-9">
                    <label for="slug" class="control-label {{trnsAlignBlockCls()}}">{{__local("Slug")}}</label>
                    <input value="{{getValueFromOldOrDB('slug', $DB)}}" class="form-control {{trnsAlignReverseCls()}} mb-3" id="slug" name="slug" type="text" data-label="{{__local('Slug')}}" placeholder="{{__local('Slug')}}" required="required">
                </div>

                <div class="input-wrapper col-12">
                    <textarea name="body" id="body" class="editor w-100" data-label="{{__local('body content')}}" placeholder="{{__local('What\'s On Your Mind ?')}}">{{getValueFromOldOrDB('body', $DB)}}</textarea>
                    <input type="hidden" name="body_raw" id="body_raw" data-label="{{__local('body content')}}">
                </div>

                <?php do_action("taxonomy_type_{$taxonomy_type}_{$action}_col_left", $DB) ?>
                <?php do_action("taxonomy_type_{$action}_col_left", $DB) ?>

            </div>
        </div>

        <div class="col-12 col-sm-12 col-md-12 col-lg-4 col-xl-4">

            <div class="input-wrapper n-border p-3">
                <label class="{{trnsAlignBlockCls()}}" for="status">{{__local("Status")}}</label>

                <select name="status" class="select2-simple w-100" id="status" data-label="{{__local('Status')}}">
                    <?= join("", getStatusPageOption(getValueFromOldOrDB('status', $DB))) ?>
                </select>

                @if($the_ID && $taxonomy_data['status'] == "public")
                <div class="text-left {{trnsAlignCls()}} mt-2">
                    <?php
                    $link = getTaxonomyLink($DB);
                    ?>
                    <label>{{__local("link")}}:</label><button class="btn-clipboard btn badge badge-white ml-2" data-clipboard-text="<?= $link ?>" type="button"><i class="bi bi-clipboard-check h5"></i></button> <a class="w-100 d-block link-info bg-info text-white rounded p-2 mb-2" href="<?= $link ?>" target="_blank"><?= $link ?></a>
                </div>
                @endif

                <div class="action-button-wrapper" data-label="<?= $taxonomy_data['label'] ?>">
                    <?php require_once getDashboardViewPath("component/buttons/add.resource.blade.php") ?>
                </div>
            </div>

            <div class="input-wrapper thumbnails mt-3 n-border p-3">
                <label class="control-label {{trnsAlignBlockCls()}}">{{__local("Thumbnail")}}</label>
                <center id="thumbnails-preview">
                    <div class="wrapper-thumbnail-preview">
                    </div>
                </center>
                <div class="w-100 mt-5"></div>
                <div id="thumbnails-data">
                    <input type="hidden" {{$thumbnail}} class="file-input" name="thumbnail_url" id="thumbnail_url" data-button-opener=".thumbnails .openTheFileManager">
                </div>
                <div class="wrapper {{trnsAlignBlockCls()}}">
                    <label class="control-label">{{__local('Select Image')}}</label>
                    <input type="button" class="openTheFileManager btn btn-primary" value="{{__local('Select')}}" data-options='{ "multiple":false, "groupType":"image", "target": "#thumbnail_url" , "onCloseCallback" : "previewImages" , "preview" : true , "previewSelector" : "#thumbnails-preview" }'>
                </div>
            </div>

            <?php do_action("taxonomy_type_{$taxonomy_type}_{$action}_col_right", $DB) ?>
            <?php do_action("taxonomy_type_{$action}_col_right", $DB) ?>

        </div>

    </div>
</form>


<x-dashboard.cpo::footer />