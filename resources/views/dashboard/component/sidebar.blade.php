<!-- ========== Left Sidebar Start ========== -->
<aside id="side-nav" class="default-bg-color col-sm-12 col-md-12 col-lg-3 col-xl-3 simple-bar rounded-right pt-4 pb-4">
    @include("dashboard.component.user-overview")
    <nav class="menu-side">
        <?php showdashboardNavMenu(); ?>
    </nav>

    <!-- Form Color Mode & Other Details -->
    <?php
    require_once getDashboardViewPath("component/form/color.mode.form.blade.php");
    $toggleColorMode = getCurrentColorModeToggle("ALL");
    ?>

    <div class="widget-list position-absolute">
        <ul class="active-text nav">
            <li class="mb-3 h3 rounded cursor-pointer d-block position-relative" id="home" title="{{__local('Home')}}"><a class="active-text d-block text-decoration-none" target="_blank" href="/"><i class="ml-5 bi bi-house-door active-text default-color"></i></a></li>
            <li class="mb-3 h3 rounded cursor-pointer d-block position-relative" id="color_mode" title="{{$toggleColorMode['label']}}"><a class="active-text d-block text-decoration-none" href="javascript:changeColorMode()"><small class="h6 text-dark title-introduce position-absolute text-white p-1 rounded">{{$toggleColorMode['label']}}</small><i class="ml-5 bi {{$toggleColorMode['icon-class']}} small active-text default-color"></i></a></li>
            <!-- <li class="mb-3 h3 rounded cursor-pointer d-block position-relative" id="cart"><a class="active-text d-block text-decoration-none" href="/"><small class="h6 position-absolute bg-primary text-white p-1 rounded">15</small><i class="ml-5 bi bi-cart"></i></a></li> -->
        </ul>
    </div>



</aside>

<!-- ========== Left Sidebar End ========== -->

<!-- Right Content -->
<div class="col-sm-12 col-md-12 col-lg-9 col-xl-9 pt-3 right-content wrapper-{{dotToUnderline($GLOBALS['current_page'])}}">