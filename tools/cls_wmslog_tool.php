<?php
namespace Tools;
use Logger;
use Model;
use Flight;
use Exception;

class ClsWmsLogTools
{
   
    public static function cbGetRouteLogInfo($out_order_sn, $tracking_number) 
    {
        $master_config = Flight::get('master_config');
        $sevice_key = $master_config['service_config']['route']['service_key'];
        $data = array('out_order_sn'=>(string)$out_order_sn,'tracking_number'=>(string)$tracking_number);
        $key = md5(serialize($data).$sevice_key);
        $url = $master_config['service_config']['route']['service_url']."/getRoute?out_order_sn={$out_order_sn}&tracking_number={$tracking_number}&key={$key}";
        $result = json_decode(self::get_data($url),true);
        if (isset($result['data'])) {
           return $result['data']; 
        } else {
            return false;
        }
    }
	
    public static function cbCreateRouteLog($data, $action_type_id, $action_user, $action_note){
        if (empty($data) || empty($action_type_id) || empty($action_note) || empty($action_user)){
            return false;
        }
        return self::_dealRouteLogData($data, $action_type_id, $action_user, $action_note);
    }
    
    private function _dealRouteLogData($data, $action_type_id, $action_user, $action_note)
    {
        $master_config = Flight::get('master_config');
        $url = $master_config['service_config']['route']['service_url'].'/route';
        $key = $master_config['service_config']['route']['service_key'];
        $result = array();
        $time = date("Y-m-d H:i:s",time());
        $result['lastRouteTypeId'] =$action_type_id;
        $result['lastUpdatedUser'] = $action_user;
        $result['lastRouteTime'] = isset($data['last_route_time'])?$data['last_route_time']:$time;
        isset($data['last_route_address']) and $result['lastRouteAddress'] = $data['last_route_address'];
        if ($action_type_id == ClsUtilsTools::C('OrderAction', 'OAT_SHIP_SHIPMENT')) {
            $result['shippingTime'] = $time;
            $result['status'] = 1;
        }
        //单个处理
        if (count($data) == count($data, 1)) {
            isset($data['out_order_sn']) and  $result['outOrderSn'] = $data['out_order_sn'];
            isset($data['tracking_number']) and  $result['trackingNumber'] = $data['tracking_number'];
            $data['status'] = isset($data['status'])?$data['status']:'5';
            $data['order_status'] = isset($data['order_status'])?$data['order_status']:"3";
            $result['formatRouteInfo'][] = array("routeTypeId" => $action_type_id, "routeUser" => $action_user, "routeTime" => $result['lastRouteTime'], "routeRemark" => $action_note, "routeStatus" => $data['status'], "routeOrderStatus" => $data['order_status'], 'routeAddress'=>'',);
            $result['key'] = md5(serialize($result).$key);
            ClsWmsLogTools::post_json_data($url, json_encode($result));
        } else {
         //批量处理
            foreach($data as $value){
                unset($result['key']);
                $result['formatRouteInfo'] = array();
                isset($value['out_order_sn']) and  $result['outOrderSn'] = $value['out_order_sn'];
                isset($value['tracking_number']) and  $result['trackingNumber'] = $value['tracking_number'];
                $value['status'] = isset($value['status'])?$value['status']:'5';
                $value['order_status'] = isset($value['order_status'])?$value['order_status']:"3";
                $result['formatRouteInfo'][] = array("routeTypeId" => $action_type_id, "routeUser" => $action_user, "routeTime" => $result['lastRouteTime'], "routeRemark" => $action_note, "routeStatus" => $value['status'], "routeOrderStatus" => $value['order_status'], 'routeAddress'=>'',);
                $result['key'] = md5(serialize($result).$key);
                ClsWmsLogTools::post_json_data($url, json_encode($result));
            }
        }
        return true;
    }

    public static function post_data($url, $data) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $ret = curl_exec($ch);
        curl_close($ch);
        return $ret;
    }

    public static function post_json_data($url, $data) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER,
        array('Content-Type: application/json; charset=utf-8','Content-Length: ' . strlen($data)));
        ob_start();
        curl_exec($ch);
        $return_content = ob_get_contents();
        ob_end_clean();
        return $return_content;
    }

    public static function get_data($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }

}

