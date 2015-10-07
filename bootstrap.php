<?php

function autoload($className)
{
    $className = ltrim($className, "\\");
    $fileName = "";
    $namespace = "";
    
    if ($lastNsPos = strrpos($className, "\\")) {
        $namespace = substr($className, 0, $lastNsPos);
        $className = substr($className, $lastNsPos + 1);
        $fileName = str_replace("\\", DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
    }
    $fileName .= str_replace("_", DIRECTORY_SEPARATOR, $className) . ".php";
    
    $dirs = array(
        "lib",
        "modules"
    );
    
    $ok = false;
    
    foreach ($dirs as $dir) {
        if (file_exists($dir . DIRECTORY_SEPARATOR . $fileName)) {
            require_once ($dir . DIRECTORY_SEPARATOR . $fileName);
            $ok = true;
            break;
        }
    }
    
    return $ok;
}
spl_autoload_register("autoload");
?>