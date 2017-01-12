<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/1/12
 * Time: 13:27
 */


namespace Controller;
use Flight;


class CommonController
{
    public static  function setRoute(){
        Flight::route('POST /file/upload', array(get_called_class(), "uploadFile"));
        Flight::route('POST /file/delete', array(get_called_class(), "delFile"));
    }
    public static function uploadFile(){
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

    public static function delFile(){
        $params = Flight::request() -> data ->getData();
        if (empty($params['attach_name'])) {
            Flight::sendRouteResult(array('error_code' => 42000));
        }
        $master_config = Flight::get('master_config');
        $upload_dir = $master_config['upload_dir'];
        $success = true;
        if (file_exists($upload_dir. $params['attach_name'])) {
            $success = unlink($upload_dir. $params['attach_name']);
        }
        if($success){
            Flight::sendRouteResult(array());
        }else{
            array('error_code' => 50001, 'error_info'=> '删除失败');
        }

    }
}