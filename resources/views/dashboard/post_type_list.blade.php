<?php if(!isset($_GET['export'])){ ?>
<x-dashboard.cpo::header />
<x-dashboard.cpo::sidebar />
<?php } ?>

<?php
$taxonomy = $post_type_data['taxonomy'];
$showExportButtons = showExportButtons($DB);
$showTotalRecord = showTotalRecord($DB);
$generateDataTableList = generateDataTableList(getReplaceCallback("post_type_list_" . $post_type , "post_type_list_general"),["DB"=> $DB , "taxonomy" => $taxonomy, "post_type_data" => $post_type_data]);
$links = showPageLink($DB);

if(isset($_GET['export'])){
    exportTheData(getReplaceCallback("export_data_post_type_" . $post_type , "export_data_post_type_general") , $generateDataTableList , @$_GET['export_type']);
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