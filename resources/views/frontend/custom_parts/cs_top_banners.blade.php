<!-- Carousel Start -->
<div class="container-fluid p-0">
    <div id="header-carousel" class="carousel slide" data-ride="carousel">
        <?php if (!empty($GLOBALS['custom_options']['cs_top_banners_home_carousel']['title']) && is_countable($GLOBALS['custom_options']['cs_top_banners_home_carousel']['title'])) : ?>
            <ol class="carousel-indicators">
                <?php foreach ($GLOBALS['custom_options']['cs_top_banners_home_carousel']['title'] as $itemIndex => $itemTitle) : ?>
                    <li data-target="#header-carousel" class="<?= $itemIndex == 0 ? "active" : "" ?>" data-slide-to="<?= $itemIndex ?>"></li>
                <?php endforeach; ?>
            </ol>
            <div class="carousel-inner">

                <?php foreach ($GLOBALS['custom_options']['cs_top_banners_home_carousel']['title'] as $itemIndex => $itemTitle) : ?>
                    <?php
                    $itemThumbnails = getTypeAttr($GLOBALS['custom_options']['cs_top_banners_home_carousel']['thumbnails'], $itemIndex, getNoImageSrc());
                    $itemDescription = $GLOBALS['custom_options']['cs_top_banners_home_carousel']['description'][$itemIndex];
                    $itemLink = $GLOBALS['custom_options']['cs_top_banners_home_carousel']['link'][$itemIndex];
                    ?>
                    <div class="carousel-item <?= $itemIndex == 0 ? "active" : "" ?>" style="height:800px;">
                        <img class="w-100" src="<?= $itemThumbnails ?>" alt="Image">
                        <div class="carousel-caption d-flex flex-column align-items-center justify-content-center">
                        <div class="p-3" style="max-width: 800px;">
                                <h1 class="text-primary text-uppercase font-weight-normal mb-md-3"><?= $itemTitle ?></h1>
                                <p class="mx-md-5 px-5 animate__animated animate__bounceIn"><?= $itemDescription ?></p>
                                <a class="btn btn-primary py-md-3 px-md-5 mt-2 mt-md-4" href="<?= $itemLink ?>">مشاهده بیشتر</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
              

            </div>
        <?php endif; ?>
    </div>
    <!-- Carousel End -->