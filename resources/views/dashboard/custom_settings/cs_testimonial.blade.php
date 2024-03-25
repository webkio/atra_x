<div class="col-12 border border-success mb-3" style="border-width: 4px !important;" data-group="cs_testimonial">
    <h2 class="text-right mt-3 mb-2"><span class="badge badge-warning d-inline-block"> نظرات </span></h2>
    <div class="row p-2"> 
    <div class="input-wrapper col-12 col-sm-12 col-md-12 col-lg-6 mb-2">
            <label for="cs_testimonial_sub_title" class="{{trnsAlignBlockCls()}}">{{__local(' عنوان')}}</label>
            <input type="text" id="cs_testimonial_sub_title" name="cs_testimonial_sub_title" class="form-control text-right" value="{{getOptionBuiltInValueByKey($DB, "cs_testimonial_sub_title", true)}}" data-label="{{__local('زیر عنوان')}}">
        </div>

        <div class="input-wrapper col-12 col-sm-12 col-md-12 col-lg-6 mb-2">
            <label for="cs_testimonial_sub_extra_title" class="{{trnsAlignBlockCls()}}">{{__local(' توضیح تیتر')}}</label>
            <input type="text" id="cs_testimonial_sub_extra_title" name="cs_testimonial_sub_extra_title" class="form-control text-right" value="{{getOptionBuiltInValueByKey($DB, "cs_testimonial_sub_extra_title", true)}}" placeholder="{{__local('شرح متن')}}" data-label="{{__local('زیر عنوان')}}">
        </div>

        <div class="input-wrapper col-12 mt-4 cs_testimonial_thumbnails">
            <label class="d-block text-right">تصویر</label>
            <center class="thumbnails-preview" id="cs_testimonial_thumbnails_preview">
                <div class="wrapper-thumbnail-preview"></div>
            </center>

            <div id="thumbnails-data">
                <input type="hidden" value="<?= getOptionBuiltInValueByKey($DB, "cs_testimonial_thumbnail") ?>" class="file-input" name="cs_testimonial_thumbnail" id="cs_testimonial_thumbnail" data-label="Logo & Favicon" data-button-opener=".cs_testimonial_thumbnails .openTheFileManager">
            </div>
            <div class="wrapper d-block text-right">
                <input type="button" class="openTheFileManager btn btn-primary" value="انتخاب" data-options='{ "multiple":false, "groupType":"image", "target": "#cs_testimonial_thumbnail" , "onCloseCallback" : "previewImages" , "preview" : true , "previewSelector" : "#cs_testimonial_thumbnails_preview" }'>
            </div>
        </div>


        <div class="input-wrapper clonable-block col-12 col-sm-12 col-md-12 col-lg-12 border mt-2 mb-2 p-4" >
            <h4 class="text-right title-introduce mt-2">آیتم های اسلایدر</h4>

            @foreach(getTypeAttr($GLOBALS['custom_options'] , 'cs_testimonial_element_title' , [null]) as $index => $item_title)
            <div class="row clonable mb-2">
                <?php
                $number = $index + 1;
                
                $item_sub_extra = @getTypeAttr($GLOBALS['custom_options'], 'cs_testimonial_element_sub_extra', [null])[$index];
                $item_description = @getTypeAttr($GLOBALS['custom_options'], 'cs_testimonial_element_description', [null])[$index];
               

                ?>

                <input type="text" id="cs_testimonial_element_{{$number}}_title" name="cs_testimonial_element_title[]" class="form-control clonable-increment-id mb-2" value="{{$item_title}}" placeholder="{{__local('نام و نام خانوادگی')}}" data-label="{{__local('نام و نام خانوادگی')}}">

                <input type="text" id="cs_testimonial_element_{{$number}}_sub_extra" name="cs_testimonial_element_sub_extra[]" class="form-control clonable-increment-id " value="{{$item_sub_extra}}" placeholder="{{__local('سمت')}}" data-label="{{__local('سمت')}}">

                <input type="text" id="cs_testimonial_element_{{$number}}_description" name="cs_testimonial_element_description[]" class="form-control clonable-increment-id " value="{{$item_description}}" placeholder="{{__local('توضیحات')}}" data-label="{{__local('توضیحات')}}">

                



                <div class="action-wrapper w-100 mt-3">
                    <button type="button" class="btn text-success clonable-button-add"><i class="h3 bi-plus-square"></i></button>
                    <button type="button" class="btn text-danger clonable-button-close delete-action json-type"><i class="h3 bi-x-square"></i></button>
                </div>
            </div>
            @endforeach


        </div>


    </div>
</div>



      

       