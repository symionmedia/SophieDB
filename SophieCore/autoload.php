<?php

spl_autoload_register(function($class) {
    # Explode the Class into namespace and classname
    $class = explode('\\', $class);
    # then require the class. Located in the folder: /classes/NAMESPACE/CLASSNAME.php
    require "./SophieCore/classes/" . $class[count($class) - 2] . "/" . $class[count($class) - 1] . ".php";
});

?>