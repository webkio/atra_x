<div class="col-12 border border-success mb-3" style="border-width: 4px !important;" data-group="cs_projects">
    <h2 class="text-right mt-3 mb-2"><span class="badge badge-warning d-inline-block">پروژه</span></h2>
    <div class="row p-2">

        <div class="input-wrapper col-12 col-sm-12 col-md-12 col-lg-6 mb-2">
            <label for="cs_projects_sub_title" class="{{trnsAlignBlockCls()}}">{{__local(' عنوان')}}</label>
            <input type="text" id="cs_projects_sub_title" name="cs_projects_sub_title" class="form-control text-right" value="{{getOptionBuiltInValueByKey($DB, "cs_projects_sub_title", true)}}" data-label="{{__local('زیر عنوان')}}">
        </div>

        <div class="input-wrapper col-12 col-sm-12 col-md-12 col-lg-6 mb-2">
            <label for="cs_projects_sub_extra_title" class="{{trnsAlignBlockCls()}}">{{__local(' توضیح تیتر')}}</label>
            <input type="text" id="cs_projects_sub_extra_title" name="cs_projects_sub_extra_title" class="form-control text-right" value="{{getOptionBuiltInValueByKey($DB, "cs_projects_sub_extra_title", true)}}" placeholder="{{__local('شرح متن')}}" data-label="{{__local('زیر عنوان')}}">
        </div>

        <div class="input-wrapper clonable-block col-12 col-sm-12 col-md-12 col-lg-12 border mt-2 mb-2 p-4" data-options='{"afterToggle":"afterToggleClonerHomeCarousel"}'>
            <h4 class="text-right title-introduce mt-2">آیتم های نمونه کار</h4>

            @foreach(getTypeAttr($GLOBALS['custom_options'] , 'cs_projects_element_title' , [null]) as $index => $item_title)
            <div class="row clonable mb-2">
                <?php
                $number = $index + 1;

                $item_thumbnail = @getTypeAttr($GLOBALS['custom_options'], 'cs_projects_element_thumbnails', [null])[$index];
                $item_link = @getTypeAttr($GLOBALS['custom_options'], 'cs_projects_element_link', [null])[$index];
                $item_tag = @getTypeAttr($GLOBALS['custom_options'], 'cs_projects_element_tag', [null])[$index];
                
                ?>

                <div class="input-wrapper clonable-increment-id col-12 mt-4 mb-4" id="cs_projects_element_thumbnails_wrapper_{{$number}}">
                    <label class="d-block text-right">تصویر</label>
                    <center class="thumbnails-preview clonable-increment-id" id="cs_projects_element_thumbnails_preview_{{$number}}">
                        <div class="wrapper-thumbnail-preview"></div>
                    </center>

                    <div id="thumbnails-data">
                        <input type="hidden" value="<?= $item_thumbnail ?>" class="file-input clonable-increment-id clonable-increment-name" name="cs_projects_element_thumbnails[]" id="cs_projects_element_thumbnails_{{$number}}" data-label="تصویر" data-button-opener="#cs_projects_element_thumbnails_wrapper_{{$number}} .openTheFileManager">
                    </div>
                    <div class="wrapper d-block text-right">
                        <button type="button" class="openTheFileManager btn btn-primary" data-options='{ "multiple":false, "groupType":"image", "target": "#cs_projects_element_thumbnails_{{$number}}" , "onCloseCallback" : "previewImages" , "preview" : true , "previewSelector" : "#cs_projects_element_thumbnails_preview_{{$number}}" }'>انتخاب</button>
                    </div>
                </div>

                <input type="text" id="cs_projects_element_{{$number}}_title" name="cs_projects_element_title[]" class="form-control clonable-increment-id mb-2" value="{{$item_title}}" placeholder="{{__local('عنوان')}}" data-label="{{__local('عنوان')}}">

                <input type="text" id="cs_projects_element_{{$number}}_link" name="cs_projects_element_link[]" dir="ltr" class="form-control text-left clonable-increment-id mb-2" value="{{$item_link}}" placeholder="{{__local('لینک')}}" data-label="{{__local('لینک')}}">

                <input type="text" id="cs_projects_element_{{$number}}_tag" name="cs_projects_element_tag[]" class="form-control clonable-increment-id mb-2" value="{{$item_tag}}" placeholder="{{__local('تگ')}}" data-label="{{__local('تگ')}}">


                <div class="action-wrapper w-100 mt-3">
                    <button type="button" class="btn text-success clonable-button-add"><i class="h3 bi-plus-square"></i></button>
                    <button type="button" class="btn text-danger clonable-button-close delete-action json-type"><i class="h3 bi-x-square"></i></button>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>