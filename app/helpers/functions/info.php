<?php

#$root = isset($_SERVER['CONTEXT_DOCUMENT_ROOT']) ? $_SERVER['CONTEXT_DOCUMENT_ROOT'] : 0;

$pwd = getcwd();
$spe = SPE;

// fix root
$root = explode($spe, $pwd);
if (count($root) === 1){
    $spe = "\\";
    $root = explode($spe, $pwd);
}
if(strtolower(last($root)) == "public"){
    $root = array_slice($root , 0 , count($root)-1);
}

$root = join($spe , $root);

define("ROOT", $root);
