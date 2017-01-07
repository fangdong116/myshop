<?php

/**
 * Created by PhpStorm.
 * User: qxu <qxu@ibenben.com>
 * Date: 2016/6/28
 * Time: 18:57
 */

class cls_shipment_service_tool
{
    public function __construct(){}

    /**
     * @param $postdata
     * @return array|string
     * 发运包裹
     *
     * $post_data = array(
     *      'order_id'=>'391437714',
     *      'shipping_id'=>'1',
     *      'product_id'=>'33850',
     *      'facility_id'=>'1',
     *      'real_quantity'=>'1',
     *      'user_id'=>'524',
     *      'user_name'=>'qxu',
     * );
     *
     * $response = array(
     *      'result'=>'ok'
     * );
     *
     */
    public function shipShipment($postdata){
        $url = self::getShipmentServiceApiUrl("ship_shipment");
        if(is_array($url) && isset($url['error_code'])){
            return $url;
        }
        return self::retryPost($url, $postdata);
    }

    /**
     * 更新订单
     *
     * @param $order_id
     * @param $flag
     * @return array|string
     */
    public static function updateShipment($order_id, $flag){
        $postdata = array('order_id' => $order_id, 'flag' => $flag);
        $url = self::getShipmentServiceApiUrl("update_shipment");
        if(is_array($url) && isset($url['error_code'])){
            return $url;
        }
        return self::retryPost($url, $postdata);
    }
    
    /**
     * 创建打印
     *
     * @param $task_id
     * @return array|string
     */
    public static function createPrint($task_id){
    	$postdata = array('task_id' => $task_id);
    	$url = self::getShipmentServiceApiUrl("create_print");
    	if(is_array($url) && isset($url['error_code'])){
    		return $url;
    	}
        return self::retryPost($url, $postdata);
    }

    /**
     * 取消打印
     *
     * @param $order_ids
     * @param $request_id
     * @return array|string
     */
    public static function cancelPrint($order_ids, $request_id){
        $postdata = array('request_id' => $request_id, 'flag' => 'cancel_print', 'request_data' => array('out_order_sns' => $order_ids));
        $url = self::getShipmentServiceApiUrl("request_express");
        if(is_array($url) && isset($url['error_code'])){
            return $url;
        }
        return self::retryPost($url, $postdata);
    }

    /**
     * addOrderAction
     *
     * @param $postdata
        action_type_id(必须)
        order_id(必须)
        out_order_sn
        tracking_number
        facility_id
        shipping_id
        status(必须)
        route_status(必须)
        route_order_status(必须)
        route_address
        action_note(必须)
        user
        time
     * @return array|string
     */
    public static function addOrderAction($postdata){
        $url = self::getShipmentServiceApiUrl("add_order_action");
        if(is_array($url) && isset($url['error_code'])){
            return $url;
        }
        return self::retryPost($url, $postdata);
    }
    /**
     * 
     * 销售出库后回传oms
     * @param unknown $postdata
     * @return Ambigous <multitype:, string, multitype:string >|Ambigous <multitype:, multitype:string , unknown>
     */
    public static function sendOmsShippedInfo($postdata){
        $url = self::getShipmentServiceApiUrl("send_oms_shipped_info");
        if(is_array($url) && isset($url['error_code'])){
            return $url;
        }
        return self::retryPost($url, $postdata);
    }

    /**
     * @param $key
     * @return array|string
     * 根据key获取调用URL
     */
    private static function getShipmentServiceApiUrl($key) {
        $config = Flight::get('master_config');
        if (!isset($config['shipment_service_config'][$key]) || !isset($config['shipment_service_config']['shipment_base_url']) ) {
            return array('error_code' => '11111' , 'error_info' => '非法的shipmentServiceApi路径');
        }
        $url = $config['shipment_service_config']['shipment_base_url'].$config['shipment_service_config'][$key];
        return $url;
    }

    public static function transferShipment($transfer_order_id){
    	$postdata = array('transfer_order_id' => $transfer_order_id);
    	$url = self::getShipmentServiceApiUrl("transfer_shipment");
    	if(is_array($url) && isset($url['error_code'])){
    		return $url;
    	}
    	return self::retryPost($url, $postdata);
    }

    public static function transferAllocatingCenter()
    {
        return self::addAsynTask('transfer_allocating_center', array());
    }

    public static function addAsynTask($task_name, $params){
        $postdata = array(
                "task_name"=>$task_name,
                "params"=> $params
        );
        $url = self::getShipmentServiceApiUrl("add_async_task");
        if (is_array($url) && isset($url['error_code'])) {
            return $url;
        }
        return self::retryPost($url, $postdata);
    }
    
    /**
     * @param $url
     * @param $data
     * @return array|mixed
     * post请求提交数据
     */
    public static function post_data($url, $data) {
        $timeout = 10; // 超时时间10s
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json; charset=utf-8',
                    'Content-Length: ' . strlen($data))
            );
            ob_start();
            curl_exec($ch);
            $response = ob_get_contents();
            ob_end_clean();
            \Logger::getLogger("Api")->debug("POST:{$url}, data={$data}, response={$response}");
        } catch (Exception $e) {
            \Logger::getLogger("Api")->debug("Exception:POST, url={$url}, exception_msg={$e->getMessage()}");
            return array('error_code' => '11111', 'error_info' => 'curl_post请求出错');
        }
        return json_decode($response, true);
    }

    /**
     * @param $url
     * @return array|mixed
     * GET请求获取数据
     */
    public static function get_data($url) {
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            $response = curl_exec($ch);
            curl_close($ch);
            \Logger::getLogger("Api")->debug("GET:{$url}, response = {$response}");
        } catch (Exception $e) {
            \Logger::getLogger("Api")->debug("Exception:GET,url={$url}, exception_msg={$e->getMessage()}");
            return array('error_code' => '11111', 'error_info' => 'curl_get请求出错');
        }
        return json_decode($response, true);
    }

    /**
     * @param $response
     * @param $response_key
     * @return array
     * 解析ShipmentService的结果
     */
    private static function parseShipmentServiceResponse($response, $response_key=null) {
        if (! isset($response) || ! isset($response['result']) || $response['result'] != "ok") {
            if (isset($response['error_code'])) {
                return $response;
            } else {
                return array('error_code' => '11111' , 'error_info' => 'shipmentService错误:请求未完成或返回格式错误');
            }
        }
        if (!is_array($response)) {
            return array();
        }
        if (empty($response_key)) {
            return $response;
        }
        if (!isset($response[$response_key])) {
            return array('error_code' => '11111' , 'error_info' => 'shipmentService错误:返回格式错误');
        }
        return $response[$response_key];
    }

    /**
     * @param $url
     * @param $data
     * @param null $response_key
     * @param int $retry_times
     * @return array
     */
    private function retryPost($url, $data, $response_key = null, $retry_times = 3)
    {
        $master_config = Flight::get('master_config');
        $data['api_key'] = $master_config['shipment_service_config']['api_key'];
        $left_times = $retry_times;
        while ($left_times > 0) {
            $response = self::post_data($url,json_encode($data));
            if (isset($response['result']) && $response['result'] == "ok") {
               return self::parseShipmentServiceResponse($response, $response_key);
            }
            sleep(2);
            $left_times--;
        }

        return array('error_code' => '11111' , 'error_info' => 'shipmentService请求不成功');
    }
}