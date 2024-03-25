<div class="col-12 border border-success mb-3" style="border-width: 4px !important;" data-group="cs_about">
    <h2 class="text-right mt-3 mb-2"><span class="badge badge-warning d-inline-block">درباره ما</span></h2>
    <div class="row p-2"> 
    <div class="input-wrapper col-12 col-sm-12 col-md-12 col-lg-6 mb-2">
            <label for="cs_about_sub_title" class="{{trnsAlignBlockCls()}}">{{__local(' عنوان')}}</label>
            <input type="text" id="cs_about_sub_title" name="cs_about_sub_title" class="form-control text-right" value="{{getOptionBuiltInValueByKey($DB, "cs_about_sub_title", true)}}" data-label="{{__local('زیر عنوان')}}">
        </div>


  
       
       
        <div class="input-wrapper col-12 col-sm-12 col-md-12 col-lg-6 mb-2">
            <label for="cs_about_sub_extra_title" class="{{trnsAlignBlockCls()}}">{{__local(' توضیح تیتر')}}</label>
            <input type="text" id="cs_about_sub_extra_title" name="cs_about_sub_extra_title" class="form-control text-right" value="{{getOptionBuiltInValueByKey($DB, "cs_about_sub_extra_title", true)}}" placeholder="{{__local('شرح متن')}}" data-label="{{__local('زیر عنوان')}}">
        </div>


        <div class="input-wrapper col-12 mt-4">
            <label for="cs_about_description" class="{{trnsAlignBlockCls()}}">{{__local('توضیحات')}}</label>
            <textarea class="editor" name="cs_about_description" id="cs_about_description" data-options='{"height":200}'>{{getOptionBuiltInValueByKey($DB, "cs_about_description" , true)}}</textarea>
            <input type="hidden" id="cs_about_description_raw" name="cs_about_description_raw" data-label="{{__local('توضیحات')}}">
        </div>

        
        <div class="input-wrapper clonable-block col-12 col-sm-12 col-md-12 col-lg-12 border mt-2 mb-2 p-4" >
            <h4 class="text-right title-introduce mt-2">آیتم های اسلایدر</h4>

            @foreach(getTypeAttr($GLOBALS['custom_options'] , 'cs_about_element_title' , [null]) as $index => $item_title)
            <div class="row clonable mb-2">
                <?php
                $number = $index + 1;
                
                $item_icon = @getTypeAttr($GLOBALS['custom_options'], 'cs_about_element_icon', [null])[$index];

                ?>

                <input type="text" id="cs_about_element_{{$number}}_title" name="cs_about_element_title[]" class="form-control clonable-increment-id mb-2" value="{{$item_title}}" placeholder="{{__local('عنوان')}}" data-label="{{__local('عنوان')}}">

                

                <input type="text" id="cs_about_element_{{$number}}_icon" name="cs_about_element_icon[]" class="form-control clonable-increment-id text-left" value="{{$item_icon}}" placeholder="{{__local('آیکون')}}" data-label="{{__local('ایکون')}}">



                <div class="action-wrapper w-100 mt-3">
                    <button type="button" class="btn text-success clonable-button-add"><i class="h3 bi-plus-square"></i></button>
                    <button type="button" class="btn text-danger clonable-button-close delete-action json-type"><i class="h3 bi-x-square"></i></button>
                </div>
            </div>
            @endforeach


        </div>


    </div>
</div>



      

       