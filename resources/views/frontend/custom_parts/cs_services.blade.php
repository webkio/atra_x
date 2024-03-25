 <!-- Services Start -->
 <div class="container-fluid py-5">
     <div class="container py-5">
         <div class="row">

             <?php

                $cs_services_sub_title = getTypeAttr($GLOBALS['custom_options'], 'cs_services_sub_title');
                $cs_services_descripion = getTypeAttr($GLOBALS['custom_options'], 'cs_services_description');
                $cs_services_sub_extra_title = getTypeAttr($GLOBALS['custom_options'], 'cs_services_sub_extra_title');
                $cs_services_icon = getTypeAttr($GLOBALS['custom_options'], 'cs_services_icon');
                $cs_services_link = getTypeAttr($GLOBALS['custom_options'], 'cs_services_link');
                $cs_services_under_title = getTypeAttr($GLOBALS['custom_options'], 'cs_services_under_title');

                ?>

             <div class="col-lg-6 pr-lg-5">
                 <h6 class="text-primary font-weight-normal text-uppercase mb-3">{{$cs_services_sub_title}}</h6>
                 <h3 class="mb-4 section-title">{{$cs_services_sub_extra_title}}</h3>
                 <p><?= $cs_services_descripion ?></p>
                 <a href="{{ $cs_services_link}}" class="btn btn-primary mt-3 py-2 px-4">مشاهده بیشتر</a>
             </div>


             <div class="col-lg-6 p-0 pt-5 pt-lg-0">
                 <div id="header-carousel" class="owl-carousel service-carousel position-relative" data-ride="carousel">
                     <?php if (!empty($GLOBALS['custom_options']['cs_services']['title']) && is_countable($GLOBALS['custom_options']['cs_services']['title'])) : ?>

                         <?php foreach ($GLOBALS['custom_options']['cs_services']['title'] as $itemIndex => $itemTitle) : ?>
                             <div data-target="#header-carousel" class="<?= $itemIndex == 0 ? "active" : "" ?>" data-slide-to="<?= $itemIndex ?>"></div>
                         <?php endforeach; ?>

                         <?php foreach ($GLOBALS['custom_options']['cs_services']['title'] as $itemIndex => $itemTitle) : ?>
                             <?php
                                $item_icon = getTypeAttr($GLOBALS['custom_options']['cs_services']['icon'], $itemIndex, getNoImageSrc());
                                $item_under_title = $GLOBALS['custom_options']['cs_services']['under_title'][$itemIndex];

                                ?>

                             <div class=" d-flex flex-column text-center bg-light mx-3 p-4">
                                 <h5 class="<?= $item_icon ?> display-3 font-weight-normal text-primary mb-3"></h5>
                                 <h5><small class="mb-3"><?= $itemTitle ?></small></h5>
                                 <p><small class="m-0"><?= $item_under_title ?></small></p>
                             </div>
                         <?php endforeach; ?>
                     <?php endif; ?>
                 </div>

             </div>

         </div>

     </div>

 </div>