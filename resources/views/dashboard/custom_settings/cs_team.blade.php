<div class="col-12 border border-success mb-3" style="border-width: 4px !important;" data-group="cs_team">
    <h2 class="text-right mt-3 mb-2"><span class="badge badge-warning d-inline-block">تیم آتراوب   </span></h2>
    <div class="row p-2"> 

        <div class="input-wrapper clonable-block col-12 col-sm-12 col-md-12 col-lg-12 border mt-2 mb-2 p-4" data-options='{"afterToggle":"afterToggleClonerHomeCarousel"}'>
            <h4 class="text-right title-introduce mt-2">آیتم ها</h4>

            @foreach(getTypeAttr($GLOBALS['custom_options'] , 'cs_team_element_head' , [null]) as $index => $item_head)
            <div class="row clonable mb-2">
                <?php
                $number = $index + 1;
                
                $item_thumbnail = getTypeAttr($GLOBALS['custom_options'], 'cs_team_element_thumbnails', [null])[$index];
                $item_sub_head = getTypeAttr($GLOBALS['custom_options'], 'cs_team_element_sub_head', [null])[$index];
               

                ?>

                <input type="text" id="cs_team_element_{{$number}}_head" name="cs_team_element_head[]" class="form-control clonable-increment-id mb-2" value="{{$item_head}}" placeholder="{{__local('عنوان')}}" data-label="{{__local('عنوان')}}">
                <input type="text" id="cs_team_element_{{$number}}_sub_head" name="cs_team_element_sub_head[]" class="form-control clonable-increment-id mb-2" value="{{$item_sub_head}}" placeholder="{{__local('عنوان دوم')}}" data-label="{{__local('عنوان دوم')}}">


                <div class="input-wrapper clonable-increment-id col-12 mt-4 mb-4" id="cs_team_element_thumbnails_wrapper_{{$number}}">
                    <label class="d-block text-right">تصویر</label>
                    <center class="thumbnails-preview clonable-increment-id" id="cs_team_element_thumbnails_preview_{{$number}}">
                        <div class="wrapper-thumbnail-preview"></div>
                    </center>

                    <div id="thumbnails-data">
                        <input type="hidden" value="<?= $item_thumbnail ?>" class="file-input clonable-increment-id clonable-increment-name" name="cs_team_element_thumbnails[]" id="cs_team_element_thumbnails_{{$number}}" data-label="تصویر" data-button-opener="#cs_team_element_thumbnails_wrapper_{{$number}} .openTheFileManager">
                    </div>
                    <div class="wrapper d-block text-right">
                        <button type="button" class="openTheFileManager btn btn-primary" data-options='{ "multiple":false, "groupType":"image", "target": "#cs_team_element_thumbnails_{{$number}}" , "onCloseCallback" : "previewImages" , "preview" : true , "previewSelector" : "#cs_team_element_thumbnails_preview_{{$number}}" }'>انتخاب</button>
                    </div>
                </div>

                



                <div class="action-wrapper w-100 mt-3">
                    <button type="button" class="btn text-success clonable-button-add"><i class="h3 bi-plus-square"></i></button>
                    <button type="button" class="btn text-danger clonable-button-close delete-action json-type"><i class="h3 bi-x-square"></i></button>
                </div>
            </div>
            @endforeach


        </div>

    </div>
</div>