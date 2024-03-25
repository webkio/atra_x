<?php
                $number = $index + 1;

                $item_sub_extra = @getTypeAttr($GLOBALS['custom_options'], 'cs_testimonial_element_sub_extra', [null])[$index];
                $item_description = @getTypeAttr($GLOBALS['custom_options'], 'cs_testimonial_element_description', [null])[$index];
                $item_thumbnail = @getTypeAttr($GLOBALS['custom_options'], 'cs_testimonial_element_thumbnails', [null])[$index];


                ?>

                <div class="input-wrapper clonable-increment-id col-12 mt-4 mb-4" id="cs_testimonial_element_thumbnails_wrapper_{{$number}}">
                    <label class="d-block text-right">تصویر</label>
                    <center class="thumbnails-preview clonable-increment-id" id="cs_testimonial_element_thumbnails_preview_{{$number}}">
                        <div class="wrapper-thumbnail-preview"></div>
                    </center>

                    <div id="thumbnails-data">
                        <input type="hidden" value="<?= $item_thumbnail ?>" class="file-input clonable-increment-id clonable-increment-name" name="cs_testimonial_element_thumbnails[]" id="cs_testimonial_element_thumbnails_{{$number}}" data-label="تصویر" data-button-opener="#cs_testimonial_element_thumbnails_wrapper_{{$number}} .openTheFileManager">
                    </div>
                    <div class="wrapper d-block text-right">
                        <button type="button" class="openTheFileManager btn btn-primary" data-options='{ "multiple":false, "groupType":"image", "target": "#cs_testimonial_element_thumbnails_{{$number}}" , "onCloseCallback" : "previewImages" , "preview" : true , "previewSelector" : "#cs_testimonial_element_thumbnails_preview_{{$number}}" }'>انتخاب</button>
                    </div>
                </div>