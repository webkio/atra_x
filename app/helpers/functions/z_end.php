<?php

// var is in dashboard
$GLOBALS['isDashboard'] = false;

if (getPartOfUrl(0) == "dashboard")
    $GLOBALS['isDashboard'] = true;


// current page
if ($GLOBALS['isDashboard']) {
    $GLOBALS['current_page'] = getPartOfUrl(1);
} else {
    $GLOBALS['current_page'] = getPartOfUrl(0);
}

// last path like index,show,edit
$GLOBALS['url_path'] = getPartOfUrl(-1);


if (defined("ROOT")) {
    // language list
    require_once(ROOT . "/resources/views/dashboard/component/parts/languages_list.blade.php");
}
