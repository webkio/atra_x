<div class="col-12 border border-primary mb-3" data-group="cs_product">
    <h2 class="text-right mt-3 mb-2"><span class="badge badge-warning d-inline-block"> مقالات آتراوب </span></h2>
    <div class="row p-2">

        <div class="input-wrapper col-12 col-sm-12 col-md-12 col-lg-6 mb-2">
            <label for="cs_product_title" class="{{trnsAlignBlockCls()}}">{{__local('عنوان')}}</label>
            <input type="text" id="cs_product_title" name="cs_product_title" class="form-control text-right" value="{{getOptionBuiltInValueByKey($DB, "cs_product_title", true)}}" data-label="{{__local('عنوان')}}">
        </div>

        <div class="input-wrapper col-12 col-sm-12 col-md-12 col-lg-6 mb-2">
            <label for="cs_product_sub_title" class="{{trnsAlignBlockCls()}}">{{__local('زیر عنوان')}}</label>
            <input type="text" id="cs_product_sub_title" name="cs_product_sub_title" class="form-control text-right" value="{{getOptionBuiltInValueByKey($DB, "cs_product_sub_title", true)}}" data-label="{{__local('زیر عنوان')}}">
        </div>

        <div class="input-wrapper clonable-block col-12 col-sm-12 col-md-12 col-lg-12 border mt-2 mb-2 p-4">
            <h4 class="text-right title-introduce mt-2">آیتم های مقالات</h4>

            @foreach(getTypeAttr($GLOBALS['custom_options'] , 'cs_product_element_title' , [null]) as $index => $item_title)
            <div class="row clonable mb-2">

                <?php
           
                $number = $index + 1;

                $item_icon = @getTypeAttr($GLOBALS['custom_options'], 'cs_product_element_icon', [null])[$index];
                $item_description = @getTypeAttr($GLOBALS['custom_options'], 'cs_product_element_description', [null])[$index];
                $item_admin_title = @getTypeAttr($GLOBALS['custom_options'], 'cs_product_element_admin_title', [null])[$index];
                $item_designe_name = @getTypeAttr($GLOBALS['custom_options'], 'cs_product_element_designe_name', [null])[$index];
                $item_message = @getTypeAttr($GLOBALS['custom_options'], 'cs_product_element_message', [null])[$index];
              
                ?>

                <input type="text" id="cs_product_element_{{$number}}_title" name="cs_product_element_title[]" class="form-control clonable-increment-id mb-2" value="{{$item_title}}" placeholder="{{__local('عنوان')}}" data-label="{{__local('عنوان')}}">
                <input type="text" id="cs_product_element_{{$number}}_icon" name="cs_product_element_icon[]" class="form-control clonable-increment-id mb-2" value="{{$item_icon}}" placeholder="{{__local('آیکون لینک')}}" data-label="{{__local('آیکون لینک')}}">
                <input type="text" id="cs_product_element_{{$number}}_description" name="cs_product_element_description[]" class="form-control clonable-increment-id mb-2" value="{{$item_description}}" placeholder="{{__local('توضیحات')}}" data-label="{{__local('توضیحات')}}">
                <input type="text" id="cs_product_element_{{$number}}_admin_title" name="cs_product_element_admin_title[]" class="form-control clonable-increment-id mb-2" value="{{$item_admin_title}}" placeholder="{{__local('نام ادمین')}}" data-label="{{__local('نام ادمین')}}">
                <input type="text" id="cs_product_element_{{$number}}_designe_name" name="cs_product_element_designe_name[]" class="form-control clonable-increment-id mb-2" value="{{$item_designe_name}}" placeholder="{{__local('نام طراحی')}}" data-label="{{__local('نام طراحی')}}">
                <input type="text" id="cs_product_element_{{$number}}_message" name="cs_product_element_message[]" class="form-control clonable-increment-id mb-2" value="{{$item_message}}" placeholder="{{__local('پیامها')}}" data-label="{{__local('پیامها')}}">

                <div class="action-wrapper w-100 mt-3">
                    <button type="button" class="btn text-success clonable-button-add"><i class="h3 bi-plus-square"></i></button>
                    <button type="button" class="btn text-danger clonable-button-close delete-action json-type"><i class="h3 bi-x-square"></i></button>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>