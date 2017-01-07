<?php
class cls_wms_service_tool
{
    public function __construct(){}

    public function getData($url){
        $base_url = self::getApiUrl("base_url");
        if(is_array($base_url) && isset($base_url['error_code'])){
            return $base_url;
        }
        $url = $base_url . $url;
        $response = self::get_data($url);
        return self::parseResponse($response, null);
    }

    public function postData($url, $data){
        $base_url = self::getApiUrl("base_url");
        if(is_array($base_url) && isset($base_url['error_code'])){
            return $base_url;
        }
        $url = $base_url . $url;
        $response = self::post_data($url,json_encode($data));
        return self::parseResponse($response, null);
    }

    /**
     * @param $key
     * @return array|string
     * 根据key获取调用URL
     */
    private static function getApiUrl($key) {
        $config = Flight::get('master_config');
        if (!isset($config['wms_service_config'][$key])) {
            return array('error_code' => '11111' , 'error_info' => '非法的mmsApi路径');
        }
        $url = $config['wms_service_config'][$key];
        return $url;
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
            \Logger::getLogger("Api")->debug("Exception wms_service:POST, url={$url}, exception_msg={$e->getMessage()}");
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
            \Logger::getLogger("Api")->debug("Exception wms_service:GET,url={$url}, exception_msg={$e->getMessage()}");
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
        if (! isset($response) || empty($response) || !is_array($response) || ! isset($response['result']) || $response['result'] != "ok") {
            if (isset($response['error_message'])) {
                return array('error_code' => '11111' , 'error_info' => "wms_serviceapi错误:{$response['error_code']},{$response['error_message']}", 'error_result' => json_encode($response));
            } else {
                return array('error_code' => '11111' , 'error_info' => 'wms_serviceapi错误:请求未完成或返回格式错误', 'error_result' => json_encode($response));
            }
        }
        if (!is_array($response)) {
            return array();
        }
        if (empty($response_key)) {
            return $response;
        }
        if (!isset($response[$response_key])) {
            return array('error_code' => '11111' , 'error_info' => 'wms_serviceapi错误:返回格式错误','error_result' => json_encode($response));
        }
        return $response[$response_key];
    }
}