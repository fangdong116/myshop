<?php
class cls_route_service_tool
{
    public function __construct(){}

    public function getShipmentTime($tracking_number_list){
        $url = self::getApiUrl("shipment_time_url");
        if(is_array($url) && isset($url['error_code'])){
            return $url;
        }
        $request_data = array();
        $request_data['tracking_numbers'] = $tracking_number_list;
        $request_data['key'] = self::getKey($request_data);
        $response = self::post_data($url,json_encode($request_data));
        return self::parseResponse($response, 'data');
    }

    
    /**
     * @param $key
     * @return array|string
     * 根据key获取调用URL
     */
    private static function getApiUrl($key) {
        $config = Flight::get('master_config');
        if (!isset($config['service_config']['route'][$key]) || !isset($config['service_config']['route']['service_url']) ) {
            return array('error_code' => '11111' , 'error_info' => '非法的expressServiceApi路径');
        }
        $url = $config['service_config']['route']['service_url'].$config['service_config']['route'][$key];
        return $url;
    }
    
    /**
     * @param $postData
     * @return array|string
     * 获取调用接口时，使用的加密Key
     */
    private static function getKey($postData){
        $service_key = Flight::get('master_config');
        $service_key = $service_key['service_config']['route']['service_key'];
        if(empty($service_key)){
            return array('error_code' => '11111' , 'error_info' => '非法service Key');
        }
        ksort($postData);
        return md5(serialize($postData).$service_key);
    }

    /**
     * @param $url
     * @param $data
     * @return array|mixed
     * post请求提交数据
     */
    public static function post_data($url, $data) {
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json; charset=utf-8',
                    'Content-Length: ' . strlen($data))
            );
            ob_start();
            curl_exec($ch);
            $response = ob_get_contents();
            ob_end_clean();
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
     */
    private static function parseResponse($response, $response_key=null) {
        if (! isset($response) || ! isset($response['result']) || $response['result'] != "ok") {
            if (isset($response['error_code'])) {
                return $response;
            } else {
                return array('error_code' => '11111' , 'error_info' => 'routeService错误:请求未完成或返回格式错误');
            }
        }
        if (!is_array($response)) {
            return array();
        }
        if (empty($response_key)) {
            return $response;
        }
        if (!isset($response[$response_key])) {
            return array('error_code' => '11111' , 'error_info' => 'routeService错误:返回格式错误');
        }
        return $response[$response_key];
    }
}