<!DOCTYPE html>
<html lang="{{$GLOBALS['site_language']}}" dir="{{$GLOBALS['lang']['direction']}}">
<head>
<?php
    do_action('head_dashboard');
?>
</head>
<body class="{{getCurrentColorMode()}}-mode page-{{dotToUnderline($GLOBALS['current_page'])}} path-{{dotToUnderline($GLOBALS['url_path'])}} {{getUserRoleClassHtml()}}" data-page="{{dotToUnderline($GLOBALS['current_page'])}}">
    <div class="container-fluid">
        <div class="row">