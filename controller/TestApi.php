<?php

namespace Controller;
require_once 'task/TaskEngine.php';
require_once 'tools/cls_utils_tools.php';

use Flight;
use Logger;
use Tools;
use Model;
use EventHandling;
use Task;

/**
* 
*/
class TestApi
{
    static public function setRoute(){
        Flight::route('GET /TestApi/run',function(){
            Flight::sendRouteResult(array());
        });
    }
}

?>

