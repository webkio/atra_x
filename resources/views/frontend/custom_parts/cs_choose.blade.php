 <!-- Features Start -->
 <div class="container-fluid bg-light">
        <div class="container">
            <div class="row">
            <?php
           
           $cs_choose_sub_title = getTypeAttr($GLOBALS['custom_options'], 'cs_choose_sub_title');
           $cs_choose_descripion = getTypeAttr($GLOBALS['custom_options'], 'cs_choose_description');
           $cs_choose_sub_extra_title = getTypeAttr($GLOBALS['custom_options'], 'cs_choose_sub_extra_title');
           $cs_choose_icon = getTypeAttr($GLOBALS['custom_options'], 'cs_choose_icon');
           $cs_choose_title = getTypeAttr($GLOBALS['custom_options'], 'cs_choose_title');
           $cs_choose_link = getTypeAttr($GLOBALS['custom_options'], 'cs_choose_link');
           $cs_choose_thumbnail = getTypeAttr($GLOBALS['custom_options'], 'cs_choose_thumbnail');


          
           ?>
                <div class="col-lg-7 mt-5 py-5 pr-lg-5" >
                    <h6 class="text-primary font-weight-normal text-uppercase mb-3">{{$cs_choose_title}}</h6>
                    <h4 class="mb-4 section-title">{{$cs_choose_sub_title}}</h4>
                    <p class="mb-4"><?=$cs_choose_descripion?></p>
                    <ul class="list-inline">
                    @foreach (getTypeAttr($GLOBALS['custom_options'], 'cs_choose')['title'] as $index=> $item_title)
                    <?php
                   $item_icon =  getTypeAttr($GLOBALS['custom_options'], 'cs_choose')['icon'][$index];
                    
                    
                    ?>
                     
                            <h5><i class="{{$item_icon}} text-primary mr-3"></i>{{$item_title}}</h5>
                        
                
                    </ul>
                    @endforeach
                    <a href="{{$cs_choose_link }}" class="btn btn-primary mt-3 py-2 px-4">اطلاعات بیشتر</a>
                </div>
                <div class="col-lg-5">
                    <div class="d-flex flex-column align-items-center justify-content-center h-100 overflow-hidden">
                        <img class="h-100" src="{{$cs_choose_thumbnail}}" alt="چرا ما">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Features End -->
