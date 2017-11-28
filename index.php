<?php

    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    header('Content-type: application/json');

    require "./SophieCore/autoload.php";

    $core = new SophieDB\Core;
    // $core->requestHandler();

    print_r(json_encode($core->requestHandler()));
    // var_dump($core->requestHandler());
    
?>  