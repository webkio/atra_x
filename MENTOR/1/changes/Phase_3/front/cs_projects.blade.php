<!-- Projects Start -->
<div class="container-fluid py-5">
    <div class="container py-5">
        <div class="row justify-content-center">
            <?php
            $cs_projects_sub_title = getTypeAttr($GLOBALS['custom_options'], 'cs_projects_sub_title');
            $cs_projects_sub_extra_title = getTypeAttr($GLOBALS['custom_options'], 'cs_projects_sub_extra_title');

            ?>
            <div class="col-lg-6 col-md-8 col text-center mb-4">
                <h6 class="text-primary font-weight-normal text-uppercase mb-3"><?= $cs_projects_sub_title ?></h6>
                <h1 class="mb-4"><?= $cs_projects_sub_extra_title ?></h1>
            </div>
        </div>
        <div class="row">
            <div class="col-12 text-center mb-2">
                <ul class="list-inline mb-4" id="portfolio-flters">
                    <li class="btn btn-outline-primary m-1 active" data-filter="*">All</li>

                    <?php if (!empty($GLOBALS['custom_options']['cs_projects']['tag']) && is_countable($GLOBALS['custom_options']['cs_projects']['tag'])) :
                        $new_tags = array_unique($GLOBALS['custom_options']['cs_projects']['tag'])
                    ?>
                        <?php foreach ($new_tags as $itemIndex => $item_tag) : ?>
                            <?php
                            $number = $itemIndex + 1;
                            ?>
                            <li class="btn btn-outline-primary m-1" data-filter=".<?= md5($item_tag) ?>"><?= $item_tag ?></li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
        <div class="row mx-1 portfolio-container">


            <?php if (!empty($GLOBALS['custom_options']['cs_projects']['title']) && is_countable($GLOBALS['custom_options']['cs_projects']['title'])) : ?>
                <?php foreach ($GLOBALS['custom_options']['cs_projects']['title'] as $itemIndex => $item_title) : ?>
                    <?php
                    $number = $itemIndex + 1;

                    $item_link = $GLOBALS['custom_options']['cs_projects']['link'][$itemIndex];
                    $item_thumbnail = $GLOBALS['custom_options']['cs_projects']['thumbnails'][$itemIndex];
                    $item_tag = $GLOBALS['custom_options']['cs_projects']['tag'][$itemIndex];

                    ?>

                    <div class="col-lg-4 col-md-6 col-sm-12 p-0 portfolio-item <?= md5($item_tag) ?>">
                        <div class="position-relative overflow-hidden">
                            <div class="portfolio-img d-flex align-items-center justify-content-center">
                                <img class="img-fluid" src="<?= $item_thumbnail ?>">
                            </div>
                            <div class="portfolio-text bg-secondary d-flex flex-column align-items-center justify-content-center">
                                <h4 class="text-white mb-4"><?= $item_title ?></h4>
                                <div class="d-flex align-items-center justify-content-center">
                                    <a class="btn btn-outline-primary m-1" href="<?= $item_link ?>">
                                        <i class="fa fa-link"></i>
                                    </a>
                                    <a class="btn btn-outline-primary m-1" href="<?= $item_thumbnail ?>" data-lightbox="portfolio">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                <?php endforeach; ?>
            <?php endif; ?>



        </div>
    </div>
</div>
<!-- Projects End -->