<div class="col-12 border border-success mb-3" style="border-width: 4px !important;" data-group="cs_company_info">
    <h2 class="text-right mt-3 mb-2"><span class="badge badge-warning d-inline-block">اطلاعات عمومی شرکت</span></h2>
    <div class="row p-2">


        <div class="input-wrapper col-12 col-sm-12 col-md-12 col-lg-6 mb-4 thumbnails-cs_company_info_logo">
            <label for="cs_company_info_logo" class="{{trnsAlignBlockCls()}}">{{__local('لوگو')}}</label>
            <center class="thumbnails-preview-logo-setting-cs_company_info_logo" id="thumbnails-preview-cs_company_info_logo">
                <div class="wrapper-thumbnail-preview">
                </div>
            </center>

            <div id="thumbnails-data">
                <input type="hidden" value="{{getOptionBuiltInValueByKey($DB, "cs_company_info_logo", true)}}" class="file-input" name="cs_company_info_logo" id="cs_company_info_logo" data-label="Logo" data-button-opener=".thumbnails-cs_company_info_logo .openTheFileManager">
            </div>
            <div class="wrapper {{trnsAlignBlockCls()}}">
                <input type="button" class="openTheFileManager btn btn-primary" value="{{__local('Select')}}" data-options='{ "multiple":false, "groupType":"image", "type" : "png", "target": "#cs_company_info_logo" , "onCloseCallback" : "previewImages" , "preview" : true , "previewSelector" : "#thumbnails-preview-cs_company_info_logo" }'>
            </div>
        </div>

        <div class="input-wrapper col-12 col-sm-12 col-md-12 col-lg-6 mb-4">
            <label for="cs_company_info_customer_service_phone" class="{{trnsAlignBlockCls()}}">{{__local('شماره تماس')}}</label>
            <input type="text" id="cs_company_info_customer_service_phone" name="cs_company_info_customer_service_phone" class="form-control text-left" dir="ltr" value="{{getOptionBuiltInValueByKey($DB, "cs_company_info_customer_service_phone", true)}}" data-label="{{__local('شماره تلفن')}}">
        </div>

        <div class="input-wrapper col-12 col-sm-12 col-md-12 col-lg-6 mb-4">
            <label for="cs_company_info_customer_service_email" class="{{trnsAlignBlockCls()}}">{{__local('ایمیل ما ')}}</label>
            <input type="text" id="cs_company_info_customer_service_email" name="cs_company_info_customer_service_email" class="form-control text-left" dir="ltr" value="{{getOptionBuiltInValueByKey($DB, "cs_company_info_customer_service_email", true)}}" data-label="{{__local('ایمیل')}}">
        </div>

        <div class="input-wrapper col-12 col-sm-12 col-md-12 col-lg-6 mb-4">
            <label for="cs_company_info_customer_service_address" class="{{trnsAlignBlockCls()}}">{{__local('آدرس ما ')}}</label>
            <input type="text" id="cs_company_info_customer_service_address" name="cs_company_info_customer_service_address" class="form-control text-left" dir="ltr" value="{{getOptionBuiltInValueByKey($DB, "cs_company_info_customer_service_address", true)}}" data-label="{{__local('آدرس')}}">
        </div>



        <div class="input-wrapper col-12 col-sm-12 col-md-12 col-lg-12 mb-4">
            <label for="cs_company_info_title_footer" class="{{trnsAlignBlockCls()}}">{{__local('عنوان فوتر')}}</label>
            <input type="text" id="cs_company_info_title_footer" name="cs_company_info_title_footer" class="form-control" value="{{getOptionBuiltInValueByKey($DB, "cs_company_info_title_footer", true)}}" data-label="{{__local('عنوان فوتر')}}">
        </div>




        <div class="input-wrapper clonable-block col-12 col-sm-12 col-md-12 col-lg-12 border border-info mt-2 mb-2 p-4" data-options='{}' style="border-width: 4px !important;">
            <h4 class="text-right title-introduce mt-2">آیتم های فوتر</h4>

            <div class="input-wrapper col-12 col-sm-12 col-md-12 col-lg-12 mb-4">
                <label for="cs_company_info_title_footer" class="{{trnsAlignBlockCls()}}">{{__local('عنوان فوتر')}}</label>
                <input type="text" id="cs_company_info_title_footer" name="cs_company_info_title_footer" class="form-control" value="{{getOptionBuiltInValueByKey($DB, "cs_company_info_title_footer", true)}}" data-label="{{__local('عنوان فوتر')}}">
            </div>
            <div class="input-wrapper col-12 col-sm-12 col-md-12 col-lg-6 mb-4">
                <label for="cs_company_info_customer_service_phone" class="{{trnsAlignBlockCls()}}">{{__local('شماره تماس')}}</label>
                <input type="text" id="cs_company_info_customer_service_phone" name="cs_company_info_customer_service_phone" class="form-control text-left" dir="ltr" value="{{getOptionBuiltInValueByKey($DB, "cs_company_info_customer_service_phone", true)}}" data-label="{{__local('شماره تلفن')}}">
            </div>

            <div class="input-wrapper col-12 col-sm-12 col-md-12 col-lg-6 mb-4">
                <label for="cs_company_info_customer_service_email" class="{{trnsAlignBlockCls()}}">{{__local('ایمیل ما ')}}</label>
                <input type="text" id="cs_company_info_customer_service_email" name="cs_company_info_customer_service_email" class="form-control text-left" dir="ltr" value="{{getOptionBuiltInValueByKey($DB, "cs_company_info_customer_service_email", true)}}" data-label="{{__local('ایمیل')}}">
            </div>

            <div class="input-wrapper col-12 col-sm-12 col-md-12 col-lg-6 mb-4">
                <label for="cs_company_info_customer_service_address" class="{{trnsAlignBlockCls()}}">{{__local('آدرس ما ')}}</label>
                <input type="text" id="cs_company_info_customer_service_address" name="cs_company_info_customer_service_address" class="form-control text-left" dir="ltr" value="{{getOptionBuiltInValueByKey($DB, "cs_company_info_customer_service_address", true)}}" data-label="{{__local('آدرس')}}">
            </div>
        </div>
        
        <div class="input-wrapper col-12 mt-4">
            <label for="cs_company_info_copyright_footer" class="{{trnsAlignBlockCls()}}">{{__local('توضیحات کپی رایت فوتر')}}</label>
            <textarea class="editor" name="cs_company_info_copyright_footer" id="cs_company_info_copyright_footer" data-options='{"height":200}'>{{getOptionBuiltInValueByKey($DB, "cs_company_info_copyright_footer" , true)}}</textarea>
        </div>

    </div>
</div>