 <!-- About Start -->
 <div class="container-fluid bg-light">
        <div class="container">
            <div class="row">
            <?php
           
                $cs_about_sub_title = getTypeAttr($GLOBALS['custom_options'], 'cs_about_sub_title');
                $cs_about_descripion = getTypeAttr($GLOBALS['custom_options'], 'cs_about_description');
                $cs_about_sub_extra_title = getTypeAttr($GLOBALS['custom_options'], 'cs_about_sub_extra_title');
				$cs_about_icon = getTypeAttr($GLOBALS['custom_options'], 'cs_about_icon');
				$cs_about_title = getTypeAttr($GLOBALS['custom_options'], 'cs_about_title');

               
                ?>
                <div class="col-lg-5">
                    <div class="d-flex flex-column align-items-center justify-content-center bg-primary h-100 py-5 px-3">
                        <i class="flaticon-brickwall display-1 font-weight-normal text-secondary mb-3"></i>
                        <h4 class="display-3 mb-3">12</h4>
                        <h1 class="m-0">  سال تجربه</h1>
                    </div>
                </div>
                <div class="col-lg-7 m-0 my-lg-5 pt-5 pb-5 pb-lg-2 pl-lg-5">
                    <h6 class="text-primary font-weight-normal text-uppercase mb-3"><?= $cs_about_sub_title?></h6>
                    <h3 class="mb-4 section-title"><?= $cs_about_sub_extra_title?></h3>
                    <p><?= $cs_about_descripion?></p>
                    <div class="row py-2">
                   
                    @foreach (getTypeAttr($GLOBALS['custom_options'], 'cs_about')['title'] as $index=> $item_title)
                    <?php
                   $item_icon =  getTypeAttr($GLOBALS['custom_options'], 'cs_about')['icon'][$index];
                    
                    
                    ?>
                        <div class="col-sm-6">
                            <div class="d-flex align-items-center mb-4">
                                <h1 class="<?=  $item_icon?> font-weight-normal text-primary m-0 mr-3"></h1>
                                <h5 class=" m-0"><?= $item_title?></h5>
                            </div>
                        </div>

                     @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- About End -->
 