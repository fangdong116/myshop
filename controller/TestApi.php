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
        Flight::route('POST /test1', array(get_called_class(), "Test"));
    }

    public static function Test(){
        $files = Flight::request() -> files ->getData();
        $store_paths = array();
        $master_config = Flight::get('master_config');
        $upload_dir = $master_config['upload_dir'];
        if(!file_exists($upload_dir)){
            mkdir($upload_dir);
        }
        foreach($files as $file){
            if($file['size'] > 2000000){
                Flight::sendRouteResult(array('error_code' => 50001));
            }
            if($file['error'] > 0){
                Flight::sendRouteResult(array('error_code' => 50001, 'error_info'=> $file['error']));
            }
            if (file_exists($upload_dir. $file["name"]))
            {
                $first_token  = strtok($file["name"], '.');
                $second_token = strtok($first_token);
                $file["name"] = $first_token . "_". time() . '.'.$second_token;
            }
            move_uploaded_file($file["tmp_name"], $upload_dir . $file["name"]);
            $store_paths[] = array("attach_path" => $upload_dir, "attach_name" => $file["name"]);
        }
        Flight::sendRouteResult(array("store_paths" => $store_paths));
    }
}

?>

