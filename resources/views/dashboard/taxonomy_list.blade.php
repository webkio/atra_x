<?php if(!isset($_GET['export'])){ ?>
<x-dashboard.cpo::header />
<x-dashboard.cpo::sidebar />
<?php } ?>

<?php

$showExportButtons = showExportButtons($DB);
$showTotalRecord = showTotalRecord($DB);
$generateDataTableList = generateDataTableList(getReplaceCallback("taxonomy_list_" . $taxonomy , "taxonomy_list_general"),["DB"=> $DB , "taxonomy_data" => $taxonomy_data]);
$links = showPageLink($DB);

if(isset($_GET['export'])){
    exportTheData(getReplaceCallback("export_data_taxonomy_" . $taxonomy , "export_data_taxonomy_general") , $generateDataTableList , @$_GET['export_type']);
}else{
    require_once getDashboardViewPath("component/form/filter.form.blade.php");
    echo $showTotalRecord;
    echo $generateDataTableList;
    echo $showExportButtons;
    echo $links;
    require_once getDashboardViewPath("component/form/delete.form.blade.php");
    require_once getDashboardViewPath("component/form/type.status.form.blade.php");
}

?>

<?php if(!isset($_GET['export'])){ ?>
<x-dashboard.cpo::footer />
<?php } ?>