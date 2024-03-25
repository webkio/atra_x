<!-- Testimonial Start -->

<?php

$cs_testimonial_sub_title = getTypeAttr($GLOBALS['custom_options'], 'cs_testimonial_sub_title');
$cs_testimonial_sub_extra_title = getTypeAttr($GLOBALS['custom_options'], 'cs_testimonial_sub_extra_title');
$cs_testimonial_thumbnail = getTypeAttr($GLOBALS['custom_options'], 'cs_testimonial_thumbnail');

?>
<div class="container-fluid">
    <div class="container">
        <div class="row">
            <div class="col-md-7 py-5 pr-md-5">
                <h6 class="text-primary font-weight-normal text-uppercase mb-3 pt-5">{{$cs_testimonial_sub_title}}</h6>
                <h5 class="mb-4 section-title">{{$cs_testimonial_sub_extra_title}}</h5>
                <div class="owl-carousel testimonial-carousel position-relative pb-5 mb-md-5">


                    <?php if (!empty($GLOBALS['custom_options']['cs_testimonial']['title']) && is_countable($GLOBALS['custom_options']['cs_testimonial']['title'])) : ?>

                        <?php foreach ($GLOBALS['custom_options']['cs_testimonial']['title'] as $itemIndex => $item_title) : ?>
                            <div data-target="#header-carousel" class="<?= $itemIndex == 0 ? "active" : "" ?>" data-slide-to="<?= $itemIndex ?>"></div>
                        <?php endforeach; ?>

                        <?php foreach ($GLOBALS['custom_options']['cs_testimonial']['title'] as $itemIndex => $item_title) : ?>
                            <?php
                            $item_description = getTypeAttr($GLOBALS['custom_options']['cs_testimonial']['description'], $itemIndex, getNoImageSrc());
                            $item_sub_extra = $GLOBALS['custom_options']['cs_testimonial']['sub_extra'][$itemIndex];

                            ?>

                            <div class="d-flex flex-column">
                                <div class="d-flex align-items-center mb-3">
                                    <img class="img-fluid rounded-circle" src="/static/front-end/assets/img/testimonial-1.jpg" style="width: 60px; height: 60px;" alt="">
                                    <div class="ml-3">
                                        <h5>{{$item_title}}</h5>
                                        <i>{{$item_sub_extra}}</i>
                                    </div>
                                </div>
                                <p class="m-0">{{ $item_description}}</p>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-md-5">
                <div class="d-flex flex-column align-items-center justify-content-center h-100 overflow-hidden">
                    <img class="h-100" src="{{$cs_testimonial_thumbnail}}" alt="">
                </div>
            </div>

        </div>
    </div>
</div>
<!-- Testimonial End -->