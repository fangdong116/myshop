<?php

namespace Controller;
require_once 'tools/cls_utils_tools.php';
use Flight;
use Tools;
use Model;

/**
* 
*/
class TestApi
{
    public static  function setRoute(){
        Flight::route('GET /test',function(){
            Flight::sendRouteResult(array("hello world"));
        });
        Flight::route('GET /test1', array(get_called_class(), "Test"));
    }

    public static function Test(){
        Flight::sendRouteResult(array("hello world"));
    }
}

?>

