    <!-- Footer Start -->
    <div class="container-fluid bg-dark text-white py-5 px-sm-3 px-md-5">
        <div class="row pt-5">
            <div class="col-lg-3 col-md-6 mb-5">
                <h4 class="text-primary mb-4"> <?= getTypeAttr($GLOBALS['custom_options'] , 'cs_company_info_title_footer' , "-") ?></h4>
                <p><i class="fa fa-map-marker-alt mr-2"></i><?= getTypeAttr($GLOBALS['custom_options'] , 'cs_company_info_customer_service_address' , "-") ?></p>
                <p><i class="fa fa-phone-alt mr-2"></i><?= getTypeAttr($GLOBALS['custom_options'] , 'cs_company_info_customer_service_phone' , "-") ?></p>
                <p><i class="fa fa-envelope mr-2"></i><?= getTypeAttr($GLOBALS['custom_options'] , 'cs_company_info_customer_service_email' , "-") ?></p>
                <div class=" d-flex justify-content-start mt-4 ">
                <?= getSocialLinksHTML("d-flex" , "svg" , "btn btn-primary btn-square mr-2" , "social-x" , true , 15) ?>
                    
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-5">
                <h4 class="text-primary mb-4">{{getTypeTitle(getMenuBySlug('menu-4')->first())}}</h4>
                <div class="d-flex flex-column justify-content-start">
                    <?= menuHeaderDepth1AppendArrow(showMenuFrontEndDepth1('menu-4', [
                        "a-3" => [
                            "class" => 'text-white mb-2'
                        ]
                    ]));
                    ?>
            
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-5">
                <h4 class="text-primary mb-4">{{getTypeTitle(getMenuBySlug('menu-5')->first())}}</h4>
                <div class="d-flex flex-column justify-content-start">
                <?= menuHeaderDepth1AppendArrow(showMenuFrontEndDepth1('menu-5', [
                        "a-3" => [
                            "class" => 'text-white mb-2'
                        ]
                    ]));
                    ?>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-5">
                <h4 class="text-primary mb-4 text-center">عضویت در خبر نامه</h4>
                <?= getNewsletterForm("email") ?>
            </div>
        </div>
        <div class="container border-top border-secondary pt-5">
            <div class="m-0 text-center text-white">
               <?= getTypeAttr($GLOBALS['custom_options'] , 'cs_company_info_copyright_footer' , "-") ?>
                </div>
        </div>
    </div>
    <!-- Footer End -->


    <!-- Back to Top -->
    <a href="#" class="btn btn-lg btn-primary back-to-top"><i class="fa fa-angle-double-up"></i></a>

    @php do_action("footer_front") @endphp
    </body>

    </html>