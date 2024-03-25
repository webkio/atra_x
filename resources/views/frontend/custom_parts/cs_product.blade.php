<?php

$cs_product_sub_title = getTypeAttr($GLOBALS['custom_options'], 'cs_product_sub_title');
$cs_product_title = getTypeAttr($GLOBALS['custom_options'], 'cs_product_title');

?>
<!-- Blog Start -->
<div class="container-fluid bg-light pt-5">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8 col text-center mb-4">
                <h6 class="text-primary font-weight-normal text-uppercase mb-3">{{$cs_product_title}}</h6>
                <h5 class="mb-4">{{$cs_product_sub_title}}</h5>
            </div>
        </div>
        <div class="row pb-3">
            <?php if (!empty($GLOBALS['custom_options']['cs_product']['title']) && is_countable($GLOBALS['custom_options']['cs_product']['title'])) : ?>
                <?php foreach ($GLOBALS['custom_options']['cs_product']['title'] as $itemIndex => $item_title) : ?>
                    <?php

                    $item_icon = $GLOBALS['custom_options']['cs_product']['icon'][$itemIndex];
                    $item_description = $GLOBALS['custom_options']['cs_product']['description'][$itemIndex];
                    $item_admin_title = $GLOBALS['custom_options']['cs_product']['admin_title'][$itemIndex];
                    $item_designe_name = $GLOBALS['custom_options']['cs_product']['designe_name'][$itemIndex];
                    $item_message = $GLOBALS['custom_options']['cs_product']['message'][$itemIndex];
                 
                    ?>
                    <div class="col-md-4 mb-4">
                        <div class="card border-0 mb-2">
                            <img class="card-img-top" src="" alt="">
                            <div class="card-body bg-white p-4">
                                <div class="d-flex align-items-center mb-3">
                                    <a class="btn btn-primary" href=""><i class="{{$item_icon}}"></i></a>
                                    <h5 class="m-0 ml-3 ">{{$item_title}}</h5>
                                </div>
                                <p><?= $item_description ?></p>
                                <div class="d-flex">
                                    <small class="mr-3"><i class="fa fa-user text-primary"></i> {{$item_admin_title}}</small>
                                    <small class="mr-3"><i class="fa fa-folder text-primary"></i> {{$item_designe_name}}</small>
                                    <small class="mr-3"><i class="fa fa-comments text-primary"></i>{{ $item_message}} </small>
                                </div>
                            </div>
                        </div>
                    </div>

                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
<!-- Blog End -->