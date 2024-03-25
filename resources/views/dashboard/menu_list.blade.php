<?php if(!isset($_GET['export'])){ ?>
<x-dashboard.cpo::header />
<x-dashboard.cpo::sidebar />
<?php } ?>

<?php

$showExportButtons = showExportButtons($DB);
$showTotalRecord = showTotalRecord($DB);
$generateDataTableList = generateDataTableList("menu_list_general",["DB"=> $DB ]);
$links = showPageLink($DB);

if(isset($_GET['export'])){
    exportTheData("export_data_menu_general" , $generateDataTableList , @$_GET['export_type']);
}else{
    require_once getDashboardViewPath("component/form/filter.form.blade.php");
    echo $showTotalRecord;
    echo $generateDataTableList;
    echo $showExportButtons;
    echo $links;
    require_once getDashboardViewPath("component/form/delete.form.blade.php");
}

?>

<?php if(!isset($_GET['export'])){ ?>
<x-dashboard.cpo::footer />
<?php } ?>