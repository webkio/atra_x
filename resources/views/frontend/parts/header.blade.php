<!DOCTYPE html>
<html lang="{{$GLOBALS['site_language']}}">

<head>

    @php do_action("head_front") @endphp

</head>

<body>
    <!-- Topbar Start -->
    <div class="container-fluid bg-dark py-3">
        <div class="container">
            <div class="row">
                <div class="col-md-6 text-center text-lg-left mb-2 mb-lg-0">
                    <div class="d-inline-flex align-items-center">
                        <?php
                        echo showMenuFrontEndDepth1('menu-1', [

                            "a-3" => [
                                "class" => 'text-white pr-3'
                            ]

                        ]);

                        ?>

                    </div>
                </div>
                <div class="col-md-6 text-center text-lg-right">
                    <div class="d-inline-flex align-items-center">
                    <?= getSocialLinksHTML("d-flex" , "svg" , "btn btn-primary btn-square mr-2" , "social-x" , true , 15) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Topbar End -->


    <!-- Navbar Start -->
    <div class="container-fluid position-relative nav-bar p-0">
        <div class="container position-relative" style="z-index: 9;">
            <nav class="navbar navbar-expand-lg bg-secondary navbar-dark py-3 py-lg-0 pl-3 pl-lg-5">
                <a href="" class="navbar-brand">
                    <h1 class="m-0 display-5 text-white"><span class="text-primary">آترا</span>وب</h1>
                </a>
                <button type="button" class="navbar-toggler" data-toggle="collapse" data-target="#navbarCollapse">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse justify-content-between px-3" id="navbarCollapse">
                    <div class="navbar-nav ml-auto py-0">
                        <?php
                        echo str_replace(["manipulate-item", "dropright", "fa-angle-right", "dropdown-menu position-absolute"], ["", "", "", "dropdown-menu bg-primary"], showMenuFrontEndDepth1("menu-2", [], "menuHeaderCategory"));
                        ?>
                    </div>
                </div>
            </nav>
        </div>
    </div>
    <!-- Navbar End -->


    <!-- Under Nav Start -->
    <div class="container-fluid bg-white py-3">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 text-left mb-3 mb-lg-0">
                    <div class="d-inline-flex text-left">
                        <h1 class="flaticon-office font-weight-normal text-primary m-0 mr-3"></h1>
                        <div class="d-flex flex-column">
                            <h5>آدرس ما</h5>
                            <p class="m-0"><?= getTypeAttr($GLOBALS['custom_options'] , 'cs_company_info_customer_service_address' , "-") ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 text-left text-lg-center mb-3 mb-lg-0">
                    <div class="d-inline-flex text-left">
                        <h1 class="flaticon-email font-weight-normal text-primary m-0 mr-3"></h1>
                        <div class="d-flex flex-column">
                            <h5>ایمیل ما</h5>
                            <p class="m-0"><?= getTypeAttr($GLOBALS['custom_options'] , 'cs_company_info_customer_service_email' , "-") ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 text-left text-lg-right mb-3 mb-lg-0">
                    <div class="d-inline-flex text-left">
                        <h1 class="flaticon-telephone font-weight-normal text-primary m-0 mr-3"></h1>
                        <div class="d-flex flex-column">
                            <h5>شماره تماس</h5>
                            <p class="m-0"><?= getTypeAttr($GLOBALS['custom_options'] , 'cs_company_info_customer_service_phone' , "-") ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Under Nav End -->