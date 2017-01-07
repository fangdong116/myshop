<?php

$dir = __DIR__;

foreach(glob($dir.'/*.php') as $file)
{
    if (file_exists($file)) {
        require_once $file;
		$class = 'controller\\' . basename($file, ".php");
		if(class_exists($class) && method_exists($class,'setRoute')) {
			$class::setRoute();
		}
    }
}

?>
