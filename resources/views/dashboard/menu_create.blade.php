<x-dashboard.cpo::header />
<x-dashboard.cpo::sidebar />

<?php
require_once getDashboardViewPath("component/form/delete.form.blade.php");
$route = getTheRoute($GLOBALS['current_page'], $action, $route_args);

?>
<div class="row">
    <div class="col-12 col-sm-12 col-md-12 col-lg-8 col-xl-8 mt-3">
        <div class="card menu-items-wrapper n-border mt-3">
            <div class="card-body">
                <form action="{{$route}}" method="post">
                    @csrf
                    @if($action == "edit")
                    @method('patch')
                    @endif
                    <div class="form-inline">
                        <label class="mr-2 ml-2" for="title">{{__local('Menu Title')}}</label>
                        <input type="text" value="{{getValueFromOldOrDB('title', $DB)}}" class="form-control col-6" name="title" id="title" data-label="{{__local('Menu Title')}}">
                    </div>
                    <hr>

                    <ul id="placehoder-menu" class="d-none">

                    </ul>

                    <div class="menu-drag-wrapper mb-5">
                        <ul class="menu-order-list dadj" data-options='{"autocreate" : true}' data-field="#menu-order-list-json">
                            <!-- Menu Element DB -->
                            <?php

                            $menuItemsJson = getValueFromOldOrDB('menu_items', $DB)??"[]";
                            echo showMenuItems($menuItemsJson , "menuIndexerDashboard");

                            ?>
                            <!-- END Menu Element DB -->
                        </ul>
                        <input type="text" class="d-none" id="menu-order-list-json" data-field="#menu_items" value="">
                        <input type="text" class="d-none" name="menu_items" id="menu_items" value="" data-label="Menu Items">
                    </div>

                    <div class="menu-location mb-3">

                        <select name="slug" id="slug" class="select2-simple" data-label="{{__local('Menu Location')}}">
                            <?php
                            echo getTypeOptionWithReserved(getValueFromOldOrDB('slug', $DB), 'Menu', 'getMenuLocation', "slug", "title", "(x)");
                            ?>
                        </select>

                    </div>

                    <div class="submit-wrapper text-right">
                        @if($action == "edit")
                        <input data-id-form="#delete-form" data-callback="deleteFormActionClick" data-label="Menu" class="btn btn-outline-danger mx-3" onclick="buttonFormAction(event)" id="delete_action" type="button" value="{{__local('Delete')}}">
                        @endif
                        <input type="submit" value="{{__local('Submit')}}" class="btn btn-outline-success">
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-12 col-md-12 col-lg-4 col-xl-4 mt-3">

        <?= generateTaxonomySelect2WidgetAll('select2TaxonomyWidgetMenu'); ?>

        <div class="input-wrapper custom-link-menu mb-4 n-border p-3">
            <label class="control-label {{trnsAlignBlockCls()}}">{{__local('Custom Link')}}</label><br>
            <div class="">
                <label class="mr-2 {{trnsAlignBlockCls()}}" for="menu-name-custom">{{__local('Name')}}</label>
                <input type="text" class="form-control col-10 input-event" id="menu-name-custom"><br>
                <label class="mr-2 {{trnsAlignBlockCls()}} text" for="menu-link-custom">{{__local('Link')}}</label>
                <input type="text" class="form-control col-10 input-event text-left" id="menu-link-custom">
            </div>
            <input type="hidden" class="push-data">

            <div class="text-right mt-3">
                <input type="button" value="{{__local('Add To Menu')}}" class="btn btn-info add-to-menu" data-target=".custom-link-menu .push-data">
            </div>

        </div>

    </div>

</div>



<x-dashboard.cpo::footer />