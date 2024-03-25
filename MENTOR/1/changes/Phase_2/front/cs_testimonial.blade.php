1- Remove First `foreach`


2- inside `Foreach`

<?php foreach ($GLOBALS['custom_options']['cs_testimonial']['title'] as $itemIndex => $item_title) : ?>
                            <?php
                            $item_description = getTypeAttr($GLOBALS['custom_options']['cs_testimonial']['description'], $itemIndex, getNoImageSrc());
                            $item_sub_extra = $GLOBALS['custom_options']['cs_testimonial']['sub_extra'][$itemIndex];

                            $item_thumbnail = $GLOBALS['custom_options']['cs_testimonial']['thumbnails'][$itemIndex];

                            ?>

                            <div class="d-flex flex-column">
                                <div class="d-flex align-items-center mb-3">
                                    <img class="img-fluid rounded-circle" src="<?= $item_thumbnail ?>" style="width: 60px; height: 60px;" alt="">
                                    <div class="ml-3">
                                        <h5>{{$item_title}}</h5>
                                        <i>{{$item_sub_extra}}</i>
                                    </div>
                                </div>
                                <p class="m-0">{{ $item_description}}</p>
                            </div>
                        <?php endforeach; ?>