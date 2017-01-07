<?php
class cls_inventory_tool {
    public function __construct(){}

    /**
     * 进销存
     *
     * @param $transaction_collection_type
     * @param $transaction_collection_data
     * @param $created_user
     * @return array
     */
    public function requestInventory($transaction_collection_type, $transaction_collection_data, $created_user) {
        $url = self::getInventoryApiUrl('inventory_transaction_collection_url');
        $request = array(
            'transaction_collection_type' => $transaction_collection_type,
            'transaction_collection_data' => $transaction_collection_data,
            'created_user' => $created_user
        );
        $response = self::post_json_data($url, json_encode($request));
        return self::parseInventoryResponse($response, "inventory_list");
    }

    /**
     * 预定库存
     *
     * @note
     * request data:
     * [
        {
        "secret_key": "111",
        "facility_id": "5",
        "inventory_status" : "in_stock",
        "inventory_type":"finished",
        "created_user": "testuser",
        "product_id":"302",
        "real_quantity" : "100"
        }
     * ]
     *
     * @param $reserve_list
     * @return array
     */
    public function reserveInventory($reserve_list){
        $url=self::getInventoryApiUrl('inventory_reserve_url');
        $response = self::post_json_data($url, json_encode($reserve_list));
        return self::parseInventoryResponse($response, "product_list");
    }

    /**
     * 释放库存
     *
     * @note
     * request data:
        {
            "secret_key_list":[
                {"secret_key":"111", "product_id":"", "inventory_status":"in_stock", "container_id":"", "batch_sn":""},
                {"secret_key":"222", "product_id":"", "inventory_status":"in_stock", "container_id":"", "batch_sn":""}
            ],
            "created_user": "jwang"
        }
     * @param $secret_key_list
     * @param $created_user
     * @return array
     */
    public function reserveCancelInventory($secret_key_list, $created_user){
        $url=self::getInventoryApiUrl('inventory_cancel_reserve_url');
        $request = array(
            'secret_key_list' => $secret_key_list,
            'created_user' => $created_user
        );

         $response = self::post_json_data($url, json_encode($request));
        return self::parseInventoryResponse($response, "product_list");
    }

    /**
     * 退回入库
     *
     * @param $transaction_collection_data
     * @param $transaction_collection_data
     * @param $created_user
     * @return array
     */
    public function createReturnInInventory($transaction_collection_data, $created_user)
    {
        $url = self::getInventoryApiUrl('create_return_in_inventory_transaction_collection_url');
        $request = array(
            'transaction_collection_data' => $transaction_collection_data,
            'created_user' => $created_user
        );
        $response = self::post_json_data($url, json_encode($request));
        return self::parseInventoryResponse($response, "inventory_list");
    }
    
    public function createPackageToRawMaterial($request_params)
    {
        $url = self::getInventoryApiUrl('package_to_raw_material_url');
        $response = self::post_json_data($url, json_encode($request_params));
        return self::parseInventoryResponse($response, "inventory_list");
    }
    
    public function createSupplierSaleReturn($request_params)
    {
        $url = self::getInventoryApiUrl('create_supplier_sale_return_url');
        $response = self::post_json_data($url, json_encode($request_params));
        return self::parseInventoryResponse($response, "inventory_list");
    }

    /**
     * create inventory batch
     *
     * @param $datas
     * @return array
     */
    public function createInventoryBatch($datas)
    {
        $url = self::getInventoryApiUrl('create_inventory_batch_url');
        $response = self::post_json_data($url, json_encode($datas));
        return self::parseInventoryResponse($response, "inventory_list");
    }


    public function showInventoryMovementList(){
        $url=self::getInventoryApiUrl('get_inventory_movement_url');
        $response=self::get_data($url);
        return self::parseInventoryResponse($response,null);
    }

    /**
     * @param $facility_id
     * @param $product_type
     * @param $inventory_type
     * @param $inventory_status
     * @param null $product_id
     * @param null $quantity_type
     * @param null $is_available_inventory
     * @param string $type
     * @return array|mixed
     * @note api result:
     * {
        product_list: [
            {
                facility_id: "1",
                facility_name: "嘉兴仓",
                product_id: "1",
                product_name: "香蕉",
                pallet_nums: "3",
                quantity: "134.200000",
                real_quantity: "671.000000",
                container_id: "11",
                container_unit_quantity: "5.0000",
                container_unit_code: "case",
                container_unit_code_name: "箱",
                container_code: "607532927444",
                unit_code: "kg",
                unit_code_name: "kg"
            }
        ],
        result: "ok"
    }
     */
    public function showInventoryContainerList($facility_id, $product_type, $inventory_type, $inventory_status,  $product_id = null, $quantity_type = null, $is_available_inventory = null, $type = 'container', $product_packaging_type = null){
        $url=self::getInventoryApiUrl('get_inventory_container_url');
        $ext_params = array(
            'facility_id' => $facility_id,
            'product_type' => $product_type,
            'inventory_type' => $inventory_type,
            'inventory_status' => $inventory_status,
            'type' => $type,
            'product_id' => $product_id,
            'quantity_type' => $quantity_type,
            'product_packaging_type' => $product_packaging_type
        );
        $url = $this->addUrlExtParams($ext_params, $url);
        $response=self::get_data($url);
        return self::parseInventoryResponse($response, 'product_list');
    }

    private function getInventoryApiUrl($key) {
        $config = Flight::get('master_config');
        $url = $config['inventory_config'][$key];
        if (empty($url)) {
            return array('error_code' => '11111' , 'error_info' => '非法inventoryapi路径');
        }
        return $url;
    }

    /**
     * @param $facility_id
     * @param $inventory_status
     * @param $product_type
     * @param $inventory_type
     * @param null $product_id
     * @param null $batch_sn
     * @param null $is_available_inventory
     * @param null $container_id
     * @param null $product_packaging_type
     * @param null $supplies_product_id
     * @return array|mixed
     * @note api result:
     * {
        product_list: [
            {
                inventory_status: "in_stock",
                inventory_type: "raw_material",
                product_id: "2",
                product_name: "苹果",
                unit_code: "kg",
                unit_code_name: "kg",
                quantity: "181.000000",
                real_quantity: "1621.000000"
            }
        ],
        result: "ok"
     }
     */

    public function showInventoryProductList($facility_id, $inventory_status, $product_type, $inventory_type, $product_id=null, $batch_sn=null, $is_available_inventory=null, $container_id=null, $product_packaging_type=null, $supplies_product_id=null){
    	$url=self::getInventoryApiUrl('get_inventory_product_url');
    	$url=str_replace('%facility_id', $facility_id, $url);
    	$url=str_replace('%inventory_status', $inventory_status, $url);
    	$url=str_replace('%product_type', $product_type, $url);
    	$url=str_replace('%inventory_type', $inventory_type, $url);
        $ext_params = array(
        		'product_id' => $product_id, 
        		'batch_sn' => $batch_sn, 
        		'is_available_inventory' => $is_available_inventory, 
        		'container_id' => ($container_id == 0 ? null : $container_id),
        		'product_packaging_type' => ($product_packaging_type == 0 ? null : $product_packaging_type),
        		'supplies_product_id' => $supplies_product_id
        		
        );
        $url = $this->addUrlExtParams($ext_params, $url);
    	$response=self::get_data($url);
    	return self::parseInventoryResponse($response, 'product_list');
    }
    
    /**
     * @param $facility_id
     * @param $inventory_status
     * @param $product_type
     * @param $inventory_type
     * @param null $product_id
     * @param null $batch_sn
     * @param int $is_available_inventory
     * @param null $container_id
     * @return array|mixed
     * @note api result:
     * {
        product_list: [
            {
                inventory_status: "in_stock",
                inventory_type: "raw_material",
                product_id: "2",
                product_name: "苹果",
                unit_code: "kg",
                unit_code_name: "kg",
                quantity: "181.000000",
                real_quantity: "1621.000000"
            }
        ],
        result: "ok"
     }
     */
    public function showInventoryTimeProductList($facility_id, $inventory_status, $product_type, $inventory_type, $product_id=null, $container_id=null,$begin_time=null, $end_time=null,$batch_sn=null, $is_available_inventory=1){
    	$url=self::getInventoryApiUrl('get_inventory_time_product_url');
    	$url=str_replace('%facility_id', $facility_id, $url);
    	$url=str_replace('%inventory_status', $inventory_status, $url);
    	$url=str_replace('%product_type', $product_type, $url);
    	$url=str_replace('%inventory_type', $inventory_type, $url);
        $ext_params = array('product_id' => $product_id, 'container_id' => ($container_id == 0 ? null : $container_id),'batch_sn' => $batch_sn, 'is_available_inventory' => $is_available_inventory,'begin_time'=>$begin_time,'end_time'=>$end_time);
        $url = $this->addUrlExtParams($ext_params, $url);
    	$response=self::get_data($url);
    	return self::parseInventoryResponse($response, 'batch_product_list');
    }

    /**
     * @param $secret_key
     * @param $product_packaging_type
     * @return array|mixed
     * @note api result:
     * {
        "product_list": [
            {
                "facility_id": "1",
                "inventory_status": "in_stock",
                "inventory_type": "raw_material",
                "product_type": "goods",
                "product_id": "1",
                "quantity": "0.000000",
                "real_quantity": "0.000000",
                "locked_quantity": "1.020000",
                "locked_real_quantity": "25.500000"
            }
        ],
        "result": "ok"
        }
     */
    public function showProductListBySecretKey($secret_key, $product_packaging_type = null){
        $url=self::getInventoryApiUrl('get_product_list_by_secret_key_url');
        $url=str_replace('%secret_key', $secret_key, $url);
        $ext_params = array('product_packaging_type' => $product_packaging_type);
        $url = $this->addUrlExtParams($ext_params, $url);
        $response=self::get_data($url);
        return self::parseInventoryResponse($response, 'product_list');
    }

    /**
     * @param $facility_id
     * @param $inventory_status
     * @param $product_type
     * @param $inventory_type
     * @param $product_id
     * @param $secret_key
     * @return array|mixed
     * @note api result:
     * {
            "locked_product_list": [
                {
                    facility_id: "1",
                    inventory_status: "in_stock",
                    inventory_type: "raw_material",
                    product_type: "goods",
                    product_id: "2",
                    product_name: "苹果",
                    unit_code: "kg",
                    unit_code_name: "kg",
                    secret_key: "001-002-20160411-00000012",
                    quantity: "0.000000",
                    real_quantity: "0.000000",
                    locked_quantity: "10.875000",
                    locked_real_quantity: "8.500000"
                }
            ],
            "result": "ok"
    }
     */
    public function showInventoryReserveProductList($facility_id, $inventory_status, $product_type, $inventory_type, $product_id = null, $secret_key = null){
        $url=self::getInventoryApiUrl('get_inventory_reserve_product_url');
        $url=str_replace('%facility_id', $facility_id, $url);
        $url=str_replace('%inventory_status', $inventory_status, $url);
        $url=str_replace('%product_type', $product_type, $url);
        $url=str_replace('%inventory_type', $inventory_type, $url);
        $ext_params = array('product_id' => $product_id, 'secret_key' => $secret_key);
        $url = $this->addUrlExtParams($ext_params, $url);
        $response=self::get_data($url);
        return self::parseInventoryResponse($response, 'locked_product_list');
    }

    /**
     * @param $facility_id
     * @param $inventory_status
     * @param $product_type
     * @param $inventory_type
     * @param null $product_id
     * @param null $batch_sn
     * @param null $is_available_inventory
     * @return array|mixed
     * @note api result:
     * {
            "batch_product_list": [
                {
                    facility_id: "1",
                    inventory_status: "in_stock",
                    inventory_type: "raw_material",
                    product_type: "goods",
                    product_id: "2",
                    product_name: "苹果",
                    unit_code: "kg",
                    unit_code_name: "kg",
                    batch_sn: "1-20160321510001-300-1",
                    quantity: "153.000000",
                    real_quantity: "765.000000"
                }
            ],
            "result": "ok"
    }
     */
    public function showInventoryBatchProductList($facility_id, $inventory_status, $product_type,
                  $inventory_type, $product_id = null, $batch_sn = null, $is_available_inventory = null){
        $url=self::getInventoryApiUrl('get_inventory_batch_product_url');
        $url=str_replace('%facility_id', $facility_id, $url);
        $url=str_replace('%inventory_status', $inventory_status, $url);
        $url=str_replace('%product_type', $product_type, $url);
        $url=str_replace('%inventory_type', $inventory_type, $url);
        $ext_params = array('product_id' => $product_id, 'batch_sn' => $batch_sn, 'is_available_inventory' => $is_available_inventory);
        $url = $this->addUrlExtParams($ext_params, $url);
        $response=self::get_data($url);
        return self::parseInventoryResponse($response, 'batch_product_list');
    }
    /**
     * 获取库存
     */
    public function showInventoryTransactionProductList($params){
        $url=self::getInventoryApiUrl('inventory_transaction_product_list');
        $url = $this->addUrlExtParams($params, $url);
        $response=self::get_data($url);
        return self::parseInventoryResponse($response, '');
    }
    
    /**
     * 获取库存明细
     */
    public function showInventoryTransactionProductContainerList($params){
        $url=self::getInventoryApiUrl('inventory_transaction_product_container_list');
        $url = $this->addUrlExtParams($params, $url);
        $response=self::get_data($url);
        return self::parseInventoryResponse($response, '');
    }
    /**
     * 获取库存预定明细
     */
    public function showLockedProductList($params){
        $url=self::getInventoryApiUrl('locked_product_list');
        $url=str_replace('%facility_id', $params['facility_id'], $url);
        $url=str_replace('%inventory_status', $params['inventory_status'], $url);
        $url=str_replace('%inventory_type', $params['inventory_type'], $url);
        $ext_params = array(
        		'product_id' => isset($params['product_id'])?$params['product_id']:'', 
        		'secret_key' => isset($params['secret_key'])?$params['secret_key']:'', 
        );
        $url = $this->addUrlExtParams($ext_params, $url);
        $response=self::get_data($url);
        return self::parseInventoryResponse($response, '');
    }
    /**
     * 获取库存批次明细
     */
    public function showInventoryTransactionProductFacilityBatch($params){
        $url=self::getInventoryApiUrl('inventory_transaction_product_facility_batch');
        $url = $this->addUrlExtParams($params, $url);
        $response=self::get_data($url);
        return self::parseInventoryResponse($response, '');
    }
    /**
     * 获取库存流水
     */
    public function showInventoryTransactionProductDetailList($params){
        $url=self::getInventoryApiUrl('inventory_transaction_product_detail_list');
        $url = $this->addUrlExtParams($params, $url);
        $response=self::get_data($url);
        return self::parseInventoryResponse($response, '');
    }

    /**
     * @param $transaction_collection_data
     * @param $created_user
     * @return array|mixed
     */
    public function createVarianceInventory($transaction_collection_data, $created_user){
        $url=self::getInventoryApiUrl('get_create_variance_inventory_url');
        $request = array(
            'transaction_collection_data' => $transaction_collection_data,
            'created_user' => $created_user
        );
        $response = self::post_json_data($url, json_encode($request));
        return self::parseInventoryResponse($response, 'variance_data');
    }

    /**
     * quick get secret_key has reserved in in_finished
     *
     * @param $secret_key
     * @return array|mixed
     */
    public function getFinishedSecretKeyReserve($secret_key){
        $url=self::getInventoryApiUrl('get_in_finished_reserve_inventory_url');
        $url=str_replace('%secret_key', $secret_key, $url);
        $response=self::get_data($url);
        return self::parseInventoryResponse($response, 'inventory_list');
    }

    /**
     * add ext params to url
     * @param $ext_params array('name'=>'tom')
     * @param $url
     * @return string
     */
    private function addUrlExtParams($ext_params, $url)
    {
        $url_params = array();
        foreach ($ext_params as $ext_param_name => $ext_param) {
            $ext_param = trim($ext_param);
            if ($ext_param != null) {
                $url_params[] = "{$ext_param_name}={$ext_param}";
            }
        }
        if (!empty($url_params)) {
            $url .= '?' . implode('&', $url_params);
        }

        return $url;
    }

    private function parseInventoryResponse($response, $response_key) {
        if (! isset($response) || ! isset($response['result']) || $response['result'] != "ok") {
            if (isset($response['error_code'])) {
                return $response;
            } else {
                return array('error_code' => '11111' , 'error_info' => '进销存错误:请求未完成或返回格式错误');
            }
        }
        if (!is_array($response)) {
            return array();
        }
        if (empty($response_key)) {
            return $response;
        }
        if (!isset($response[$response_key])) {
            return array('error_code' => '11111' , 'error_info' => '进销存错误:返回格式错误');
        }
        return $response[$response_key];
    }
    public function post_json_data($url, $data) {
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
            \Logger::getLogger("Api")->debug("POST:{$url}, data={$data}, response={$response}");
        } catch (Exception $e) {
            \Logger::getLogger("Api")->debug("Exception:POST, url={$url}, exception_msg={$e->getMessage()}");
            return array('error_code' => '11111', 'error_info' => 'curl_post请求出错');
        }
        return json_decode($response, true);
    }
    
    public function get_data($url) {
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
}

?>
