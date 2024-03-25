<?php

function mRealPath($path, $dir = null)
{
    $dir = $dir === null ? __DIR__ . "//" : $dir;
    return realpath($dir . "{$path}");
}

function anyHttpRequestList(){
    return [
        "anyHttp_forceHttps",
        "redirectionAction"
    ];
}

function anyHttpRequest(){
    $list = anyHttpRequestList();
    foreach($list as $cbk){
        if(is_callable($cbk)){
            $cbk();
        }
    }
}

$pathFunctions = mRealPath("functions");
$files = scandir($pathFunctions);

$files = array_filter($files, function ($element) {
    return $element != "." && $element != "..";
});

$files = array_values($files);

foreach ($files as $file) {
    $path = mRealPath("{$pathFunctions}/{$file}", "");
    if (!is_file($path)) continue;
    
    require_once $path;
}


