<?php if (!isset($_GET['export'])) { ?>
    <x-dashboard.cpo::header />
    <x-dashboard.cpo::sidebar />
<?php } ?>

<?php

$showExportButtons = showExportButtons($DB);
$showTotalRecord = showTotalRecord($DB);
$generateDataTableList = generateDataTableList(getReplaceCallback("comment_list_" . $comment, "comment_list_general"), ["DB" => $DB, "comment_data" => $comment_data]);
$links = showPageLink($DB);

if (isset($_GET['export'])) {
    exportTheData(getReplaceCallback("export_data_comment_" . $comment, "export_data_comment_general"), $generateDataTableList, @$_GET['export_type']);
} else {
    require_once getDashboardViewPath("component/form/filter.form.blade.php");
    echo $showTotalRecord;
    echo $generateDataTableList;
    echo $showExportButtons;
    echo $links;
    require_once getDashboardViewPath("component/form/delete.form.blade.php");


    require_once getDashboardViewPath("component/form/type.status.form.blade.php");
    require_once getDashboardViewPath("component/form/confirm.and.reply.form.blade.php");
}

?>

<?php if (!isset($_GET['export'])) { ?>
    <x-dashboard.cpo::footer />
<?php } ?>