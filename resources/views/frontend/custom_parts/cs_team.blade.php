 <!-- Team Start -->
 <div class="container-fluid bg-light">
     <div class="container">
         <div class="row">
             <div class="col-md-4 col-sm-6">
                 <div class="py-5 px-4 h-100 bg-primary d-flex flex-column align-items-center justify-content-center">
                     <h3 class="text-white font-weight-normal text-uppercase mb-3">تیم آتراوب</h3>
                     <h1 class="mb-0 text-center">آشنایی با تیم ما</h1>
                 </div>
             </div>
             <div class="col-md-8 col-sm-6 p-0 py-sm-5">
                 <div class="owl-carousel team-carousel position-relative p-0 py-sm-5">
                     <?php if (!empty($GLOBALS['custom_options']['cs_team']['head']) && is_countable($GLOBALS['custom_options']['cs_team']['head'])) : ?>
                         <?php foreach ($GLOBALS['custom_options']['cs_team']['head'] as $itemIndex => $item_head) : ?>
                             <?php
                                $item_sub_head = $GLOBALS['custom_options']['cs_team']['sub_head'][$itemIndex];
                                $item_thumbnail = getTypeAttr($GLOBALS['custom_options']['cs_team']['thumbnails'], $itemIndex, getNoImageSrc());
                                ?>
                             <div class="team d-flex flex-column text-center mx-3">
                                 <div class="position-relative">
                                     <img class="img-fluid w-100" src="{!!$item_thumbnail!!}" alt="تیم آتراوب">
                                     <div class="team-social d-flex align-items-center justify-content-center w-100 h-100 position-absolute">
                                         <a class="btn btn-outline-primary text-center mr-2 px-0" style="width: 38px; height: 38px;" href="#"><i class="fab fa-twitter"></i></a>
                                         <a class="btn btn-outline-primary text-center mr-2 px-0" style="width: 38px; height: 38px;" href="#"><i class="fab fa-facebook-f"></i></a>
                                         <a class="btn btn-outline-primary text-center px-0" style="width: 38px; height: 38px;" href="#"><i class="fab fa-linkedin-in"></i></a>
                                     </div>
                                 </div>
                                 <div class="d-flex flex-column bg-secondary text-center py-3">
                                     <h5 class="text-white">{!!$item_sub_head !!}</h5>
                                     <p class="m-0">{!!$item_head!!}</p>
                                 </div>
                             </div>
                         <?php endforeach; ?>
                     <?php endif; ?>
                 </div>
             </div>

         </div>
     </div>
 </div>
 <!-- Team End -->