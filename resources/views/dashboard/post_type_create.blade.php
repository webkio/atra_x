<?php
$GLOBALS['post.type.type'] = $route_args['type'];
$callback = "ob_post_type_{$route_args['type']}_$action";
if (!is_callable($callback)) {
    if ($action == "create") {
        $callback = 'ob_post_type_general_create';
    } else if ($action == "edit") {
        $callback = 'ob_post_type_general_edit';
    }
}
ob_start($callback)
?>
<x-dashboard.cpo::header />
<x-dashboard.cpo::sidebar />
<?php
require_once getDashboardViewPath("component/form/delete.form.blade.php");
$route = getTheRoute($GLOBALS['current_page'], $action, $route_args);
?>

<form action="{{$route}}" method="post" class="" id="main-form">
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
                <div class="input-wrapper col-12 component-own">
                    <label for="title" class="{{trnsAlignBlockCls()}}">{{__local("Title")}}</label>
                    <input value="{{getValueFromOldOrDB('title', $DB)}}" class="form-control mb-3" id="title" name="title" type="text" data-label="{{__local('Title')}}" placeholder="{{__local('Title')}}" required="required">
                </div>

                <div class="input-wrapper col-9 component-own">
                    <label for="slug" class="control-label {{trnsAlignBlockCls()}}">{{__local("Slug")}}</label>
                    <input value="{{getValueFromOldOrDB('slug', $DB)}}" class="form-control mb-3 {{trnsAlignReverseCls()}}" id="slug" name="slug" type="text" data-label="{{__local('Slug')}}" placeholder="{{__local('Slug')}}" required="required">
                </div>

                <div class="input-wrapper col-12 component-own">
                    <textarea name="body" id="body" class="editor w-100" data-label="{{__local('body content')}}" placeholder="{{__local('What\'s On Your Mind ?')}}">{{getValueFromOldOrDB('body', $DB)}}</textarea>
                    <input type="hidden" name="body_raw" id="body_raw" data-label="{{__local('body content')}}">
                </div>

                <?php

                 // taxonomy
                 $taxonomy_values = getValueFromOldOrDB('taxonomy', $DB);
                 foreach ($post_type_data['taxonomy'] as $taxo) {
                     $slug = $taxo;
                     $current_taxonomy = searchInTaxonomy($taxo);
                     
                     if (is_array($current_taxonomy)) {
 
                         $isSingle = getTypeAttr($current_taxonomy , "is_single" , false);
                         $widgetKey = $isSingle ? "select2TaxonomyWidgetSingle" : "select2TaxonomyWidget";
 
                         $label = $current_taxonomy['label'];
                         // solution idea taxonomy own value
                         echo generateTaxonomySelect2Widget($slug, $label, @$taxonomy_values[$slug] , $widgetKey);
                     }
                 }

                ?>

                <?php do_action("post_type_{$post_type}_{$action}_col_left", $DB) ?>
                <?php do_action("post_type_{$action}_col_left", $DB); ?>

            </div>
        </div>

        <div class="col-12 col-sm-12 col-md-12 col-lg-4 col-xl-4">

            <div class="input-wrapper component-own n-border p-3">
                <label class="{{trnsAlignBlockCls()}}" for="status">{{__local("Status")}}</label>

                <select name="status" class="select2-simple w-100" id="status" data-label="{{__local("Status")}}">
                    <?= join("", getStatusPageOption(getValueFromOldOrDB('status', $DB))) ?>
                </select>
            </div>

            <div class="input-wrapper n-border mt-1 p-3">
                @if($the_ID)

                <div class="text-left {{trnsAlignCls()}} mt-2">
                    <?php $link = getPostTypeLink($DB) ?>
                    @if($post_type_data['status'] == "public")
                    <label>{{__local("link")}}:</label><button class="btn-clipboard btn badge badge-white ml-2" data-clipboard-text="<?= $link ?>" type="button"><i class="bi bi-clipboard-check h5"></i></button> <a class="w-100 d-block link-info bg-info text-white rounded p-2 mb-2" href="<?= $link ?>" target="_blank"><?= $link ?></a>
                    @endif

                    <label>{{__local("Views")}} :</label> <span id="views" class="badge badge-info">{{getTypeCounts($DB, "views")}}</span><br>

                    @if(commentExistsInPostType($post_type_data, "comment"))
                    {!!comment_element_edit_callback("comment" , $DB)!!}
                    @endif

                    @if(commentExistsInPostType($post_type_data, "rating"))
                    {!!comment_element_edit_callback("rating" , $DB)!!}
                    @endif


                </div>
                @endif
                <div class="action-button-wrapper" data-label="<?= $post_type_data['label'] ?>">
                    <?php require_once getDashboardViewPath("component/buttons/add.resource.blade.php") ?>
                </div>
            </div>

            @if($action == "edit" && isSuperAdmin(getCurrentUser()))
            <div class="wrapper n-border p-3 mt-1">
                <label class="{{trnsAlignBlockCls()}}" for="clone">{{__local("Clone")}}</label>
                <select name="clone" class="select2-simple w-100" id="clone" data-label="{{__local("Clone")}}">
                    <?= join("", getClonePageOption(getValueFromOldOrDB('clone', $DB))) ?>
                </select>
                <input class="btn btn-warning mt-3 w-100" data-id-entity={{$the_ID}} data-meta-includes='[]' id="apply_clone" type="button" value="{{__local('Clone it')}}">
            </div>
            @endif

            <?php do_action("post_type_{$post_type}_{$action}_col_right_middle", $DB) ?>
            <?php do_action("post_type_{$action}_col_right_middle", $DB) ?>

            <div class="input-wrapper component-own thumbnails mt-3 n-border p-3">
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

            <?php do_action("post_type_{$post_type}_{$action}_col_right", $DB) ?>
            <?php do_action("post_type_{$action}_col_right", $DB) ?>

        </div>

    </div>
</form>
<x-dashboard.cpo::footer />
<?php ob_end_flush() ?>