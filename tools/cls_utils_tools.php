<?php
namespace Tools;

use Flight;
use Exception;
use Logger;
use task\TaskConstant;
use model;

class ClsUtilsTools
{
	static public $error_array;
	static public $domain_array;
	static public $constant_array;
	static public $wechatMessage;

	static public function getRealIp() {
		if(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
			$IP = $_SERVER['HTTP_X_FORWARDED_FOR'];
		}elseif(!empty($_SERVER['HTTP_CLIENT_IP'])){
			$IP = getenv('HTTP_CLIENT_IP');
		}elseif(!empty($_SERVER['REMOTE_ADDR'])){
			$IP = $_SERVER['REMOTE_ADDR'];
		}elseif($_SERVER['HTTP_VIA']){
			$IP = $_SERVER['HTTP_VIA'];
		}else{
			$IP = null;
		}

		return trim(substr($IP,strpos($IP," ")));
	}

	static public function createGuid () {
        if (function_exists('com_create_guid')){
            return com_create_guid();
        }else{
            mt_srand((double)microtime()*10000);//optional for php 4.2.0 and up.
            $charid = strtoupper(md5(uniqid(rand(), true)));
            $hyphen = chr(45);// "-"
            $uuid = //chr(123)// "{"
                substr($charid, 0, 8)
                .substr($charid, 8, 4)
                .substr($charid,12, 4)
                .substr($charid,16, 4)
                .substr($charid,20,12);
                //.chr(125);// "}"
            return $uuid;
        }
    }

	static public function isValidGuid ($guid) {
		return !empty($guid) && preg_match('/^\{?[A-Z0-9]{32}\}?$/', $guid);
	}

	static public function array2string (&$data) {
		if(is_array($data)){
			foreach ($data as $key => &$value) {
				ClsUtilsTools::array2string($value);
			}
		}else{
			$data = $data===null ? "" : $data;
			$data = (string)$data;
		}
	}

	static public function generateSmsVertCode(){
        $c = "0123456789";
        $l = 4;
        $rand = "";
        srand((double)microtime()*1000000);

        for($i=0; $i<$l; $i++) {
            $rand.= $c[rand()%strlen($c)];
        }

        return $rand;
    }

	static public function isAllowCrossDomain($domain) {
		$temp = ClsUtilsTools::$domain_array['allow'];
		$domains = explode(",", $temp['domain_name']);
		return in_array($domain, $domains);
	}

	static public function getErrorInfo ($error_code, $arg1=null, $arg2=null, $arg3=null, $arg4=null, $arg5=null) {
		$language = 'cn';
		$errors = ClsUtilsTools::$error_array[$language];
		if (isset($errors[$error_code])) {
			$string = $errors[$error_code];
		} else {
			$string = $errors['50000'];
		}
		
		if (!is_null($arg1)) {
			// insert argument(s) into string
			$string = sprintf($string, $arg1, $arg2, $arg3, $arg4, $arg5);
		}

		return $string;
	}
    static public function getTaskConfigInfo ($config_name, $default_value = '') {
		$configs = Flight::get('task_config');
		if (isset($configs[$config_name])) {
			$string = $configs[$config_name];
		} else {
			$string = $default_value;
		}
		return $string;
	}

	/**
     * 将一个字串中含有全角的数字字符、字母、空格或'%+-()'字符转换为相应半角字符
     *
     * @access public
     * @param string $str
     *            待转换字串
     *
     * @return string $str 处理后字串
     */
    public static function makeSemiangle($str)
    {
        $arr = array(
            '０' => '0',
            '１' => '1',
            '２' => '2',
            '３' => '3',
            '４' => '4',
            '５' => '5',
            '６' => '6',
            '７' => '7',
            '８' => '8',
            '９' => '9',
            'Ａ' => 'A',
            'Ｂ' => 'B',
            'Ｃ' => 'C',
            'Ｄ' => 'D',
            'Ｅ' => 'E',
            'Ｆ' => 'F',
            'Ｇ' => 'G',
            'Ｈ' => 'H',
            'Ｉ' => 'I',
            'Ｊ' => 'J',
            'Ｋ' => 'K',
            'Ｌ' => 'L',
            'Ｍ' => 'M',
            'Ｎ' => 'N',
            'Ｏ' => 'O',
            'Ｐ' => 'P',
            'Ｑ' => 'Q',
            'Ｒ' => 'R',
            'Ｓ' => 'S',
            'Ｔ' => 'T',
            'Ｕ' => 'U',
            'Ｖ' => 'V',
            'Ｗ' => 'W',
            'Ｘ' => 'X',
            'Ｙ' => 'Y',
            'Ｚ' => 'Z',
            'ａ' => 'a',
            'ｂ' => 'b',
            'ｃ' => 'c',
            'ｄ' => 'd',
            'ｅ' => 'e',
            'ｆ' => 'f',
            'ｇ' => 'g',
            'ｈ' => 'h',
            'ｉ' => 'i',
            'ｊ' => 'j',
            'ｋ' => 'k',
            'ｌ' => 'l',
            'ｍ' => 'm',
            'ｎ' => 'n',
            'ｏ' => 'o',
            'ｐ' => 'p',
            'ｑ' => 'q',
            'ｒ' => 'r',
            'ｓ' => 's',
            'ｔ' => 't',
            'ｕ' => 'u',
            'ｖ' => 'v',
            'ｗ' => 'w',
            'ｘ' => 'x',
            'ｙ' => 'y',
            'ｚ' => 'z',
            '（' => '(',
            '）' => ')',
            '〔' => '[',
            '〕' => ']',
            '【' => '[',
            '】' => ']',
            '〖' => '[',
            '〗' => ']',
            '“' => '[',
            '”' => ']',
            '‘' => '[',
            '’' => ']',
            '｛' => '{',
            '｝' => '}',
            '《' => '<',
            '》' => '>',
            '％' => '%',
            '＋' => '+',
            '—' => '-',
            '－' => '-',
            '～' => '-',
            '：' => ':',
            '。' => '.',
            '、' => ',',
            '，' => '.',
            '、' => '.',
            '；' => ',',
            '？' => '?',
            '！' => '!',
            '…' => '-',
            '‖' => '|',
            '”' => '"',
            '’' => '`',
            '‘' => '`',
            '｜' => '|',
            '〃' => '"',
            '　' => ' '
        );

        return strtr($str, $arr);
    }

	public static function C($part, $keyname){
        return ClsUtilsTools::$constant_array[$part][$keyname];
    }

    static public function checkStringMatchRegex($string, $regex,$errorWhenNotMatch=42000){
        //int preg_match ( string $pattern , string $subject [, array &$matches [, int $flags = 0 [, int $offset = 0 ]]] )
        $temp =preg_match($regex, $string);
        if(empty($temp)){
            if($errorWhenNotMatch != false){
                Flight::sendRouteResult(array('error_code'=>$errorWhenNotMatch));
            }else{
                return false;
            }
        }else{
            return true;
        }
    }
    
    static public function checkStringNotNull($string,$errorWhenNotMatch=42000){
    	if(!isset($string)){
    		if($errorWhenNotMatch != false){
    			Flight::sendRouteResult(array('error_code'=>$errorWhenNotMatch));
    		}else{
    			return false;
    		}
    	}else{
    		return true;
    	}
    }
    
    /**
     * 生成条码串
     * @param unknown $seed
     * @return string
     */
    public static function generateBarCode($seed){
    	$tmp = substr(hash("md5",$seed), 9, 9);
    	mt_srand((double) microtime() * 1000000);
    	return substr(hexdec($tmp), -6, 6).str_pad((mt_rand(1, 999999)), 6, '0', STR_PAD_LEFT);
    }

    static public function arrayToObject (&$array)
    {
        ArrayConvert::main($array);
    }

    /**
     * Convert json string $jsonString into the given $object instance.
     *
     * @param string $jsonString   JSON string
     * @param object $classWithNamespace Object to convert $jsonString into, and the namespace is the same with directory
     *
     * @return object Mapped object is returned.
     */
    static public function jsonToObject($jsonString, $classWithNamespace) {
        try {
            $temp = explode("\\", $classWithNamespace);
            require_once 'jsonmapper/src/JsonMapper.php';
            $mapper = new \JsonMapper();
            $classObject = $mapper->map(json_decode($jsonString), new $classWithNamespace());
            if (empty($classObject) || ! is_object($classObject)) {
                throw new Exception("classObject is empty or not object");
            }
            return $classObject;
        } catch (Exception $e) {
            Logger::getLogger('Route')->error("jsonToObject exception message begin");
            Logger::getLogger('Route')->error("jsonString " . $jsonString);
            Logger::getLogger('Route')->error("classWithNamespace " . $classWithNamespace);
            Logger::getLogger('Route')->error("exception " . $e);
            Logger::getLogger('Route')->error("jsonToObject exception message end");
        }
        return null;
    }

    /**
     * 数字格式化处理
     *
     * @note
     * 保留6位小数
     *
     * @param $number
     * @return double
     */
    public static function formatNumber($number, $default = -6)
    {
        $total = substr(sprintf("%.12f", $number), 0, $default);
        return doubleval($total);
    }


    public static function number2String($number){
        return substr(sprintf("%.12f", $number), 0, -6);
    }
    /**
     * 在一定误差范围内比较两个数字是否相等
     *
     * @param float $check_number
     * @param float $target_number
     * @param float $equal_range
     * @return bool
     */
    public static function checkNumberEqualByRange($check_number, $target_number, $equal_range=0.00001)
    {
        $status = false;
        if(abs(self::formatNumber($check_number - $target_number)) <= self::formatNumber($equal_range)) {
            $status = true;
        }

        return $status;
    }

    public static function checkNumberBiggerByRange($check_number, $target_number, $equal_range=0.00001)
    {
        $status = false;
        if(self::formatNumber($check_number - $target_number) > self::formatNumber($equal_range)) {
            $status = true;
        }

        return $status;
    }

    public static function checkNumberSmallerByRange($check_number, $target_number, $equal_range=0.00001)
    {
        $status = false;
        if(self::formatNumber($check_number - $target_number) < -self::formatNumber($equal_range)) {
            $status = true;
        }

        return $status;
    }


    public static function checkWeightEquation($gross_weight, $net_weight, $pallet_weight, $quantity, $container_unit_weight){
        $cal_net_weight =  ClsUtilsTools::formatNumber($gross_weight - $pallet_weight - $quantity * $container_unit_weight, -2);
        if($net_weight == $cal_net_weight){
            return array();
        }else{
            return array('error_code'=> '70015', 'error_info' => "重量方程校验失败，请检查码托盘参数,输入净重{$gross_weight},计算净重{$cal_net_weight}");
        }
    }
    /**
     * filter array from Object->toArray()
     *
     * @param $data
     * @return array
     */
    public static function filterArray($data)
    {
        if (!empty($data) && is_array($data))
        {
            foreach ($data as $key => $item)
            {
                if (is_array($data[$key]) || is_object($data[$key]))
                {
                    unset($data[$key]);
                }
            }
        }

        return $data;
    }

    /**
     * set data
     *
     * @param array $infos
     * @param $id
     * @param array $data
     */
    public static function setData(array &$infos, $id, array $data)
    {
        if (!isset($infos[$id])) {
            $infos[$id] = $data;
        }
    }

    /**
     * get data
     *
     * @param array $infos
     * @param int $id
     * @return array
     */
    public static function getData(array $infos, $id)
    {
        $result = array();
        if (isset($infos[$id])) {
            $result = $infos[$id];
        }

        return $result;
    }

    /**
     * get data  and  set data if not exist
     *
     * @param array $infos
     * @param int $id
     * @param array $data
     * @return array
     */
    public static function getAndSetData(array &$infos, $id, $data = array())
    {
        self::setData($infos, $id, $data);
        $result = self::getData($infos, $id);

        return $result;
    }

    /**
     * get new array from array field
     *
     * @note $data = array(
     *              0 => array('name'=>'Tom','age'=>12),
     *              1 => array('name'=>'Jim','age'=>15)
     * )
     * => $field('name')
     * array('Tom','Jim')
     * @param array $datas
     * @param $field
     * @return array
     */
    public static function getFieldsFromArray(array $datas, $field)
    {
        $result = array();
        foreach ($datas as $item)
        {
            $result[] = $item[$field];
        }

        return $result;
    }

    /**
     * get sub array from array fields
     *
     * @note $data = array(
     *              0 => array('name'=>'Tom','age'=>12),
     *              1 => array('name'=>'Jim','age'=>15)
     * )
     * => $fields('name')
     * array(
     *  0 => array('name'=>'Tom'),
     *  1 => array('name'=>'Jim')
     * )
     * @param array $datas
     * @param array $fields
     * @return array
     */
    public static function getSubArrayFromArray(array $datas, array $fields)
    {
        $result = array();
        foreach ($datas as $item)
        {
            $result[] = array_intersect_key($item, $fields);
        }

        return $result;
    }
    /**
     * convert array to key-value array
     *
     * @note $data = array(
     *              0 => array('name'=>'Tom','age'=>12),
     *              1 => array('name'=>'Jim','age'=>15)
     * )
     * => $key('name'), $value('age')
     * array('Tom'=>12, 'Jim'=>15)
     * @param array $datas
     * @param $key
     * @param $value
     * @return array
     */
    public static function convertArrayToKeyValue(array $datas, $key, $value)
    {
        $result = array();
        foreach ($datas as $item)
        {
            $result[$item[$key]] = $item[$value];
        }

        return $result;
    }


    /**
     * convert array to key-value array
     *
     * @note $data = array(
     *              0 => array('height'=>'220','quantity'=>12, 'level' => 1),
     *              1 => array('height'=>'320','quantity'=>15, 'level' => 1)
     *              2 => array('height'=>'320','quantity'=>15, 'level' => 2)
     * )
     * => $keys['height', 'level'], $delimiter('_'), $value('quantity')
     * array('220_1'=>12, '320_1'=>15, '320_2'=>15)
     * @param array $datas
     * @param array $keys
     * @param $value
     * @param $delimiter
     * @return array
     */
    public static function convertArrayToUnionKeyValue(array $datas, $keys, $delimiter, $value)
    {
        $result = array();
        foreach ($datas as $item)
        {
            $out_arrs = array_intersect_key($item, array_fill_keys($keys, 0));
            $out_vals = array_values($out_arrs);
            $key = implode($delimiter, $out_vals);
            $result[$key] = $item[$value];
        }

        return $result;
    }

    /**
     * convert array to key-array
     *
     * @note $data = array(
     *              0 => array('name'=>'Tom','age'=>12),
     *              1 => array('name'=>'Jim','age'=>15)
     * )
     * => $key('name')
     * array('Tom' => array('name'=>'Tom','age'=>12), 'Jim' => array('name'=>'Jim','age'=>15))
     * @param array $datas
     * @param $key
     * @return array
     */
    public static function convertArrayToKeyArray(array $datas, $key)
    {
        $result = array();
        foreach ($datas as $item)
        {
            $result[$item[$key]] = $item;
        }

        return $result;
    }

    public static function divideArrayBykey(array $datas, $key){
        $result = array();
        foreach($datas as $item){
            $result[$item[$key]][] = $item;
        }
        return $result;
    }

    /**
     * merge two array , only select need_array part of fields
     *
     * @note
     * $array_master => array(
     *              0 => array('name'=>'Tom','age'=>12),
     *              1 => array('name'=>'Jim','age'=>15)
     * )
     * $need_master => array(
     *              0 => array('name'=>'Tom','height'=>152),
     *              1 => array('name'=>'Jim','height'=>159)
     * )
     * =>$master_link_key('name'), $need_link_key('name'), $need_fields(array('height'))
     * array(
     *              0 => array('name'=>'Tom', 'age'=>12, 'height'=>152),
     *              1 => array('name'=>'Jim', 'age'=>12, 'height'=>159)
     * )
     * @param array $master_array
     * @param array $need_array
     * @param $master_link_key
     * @param $need_link_key
     * @param array $need_fields
     * @return array
     */
    public static function mergeArrayByFields(array &$master_array, array $need_array, $master_link_key, $need_link_key, array $need_fields)
    {
        foreach ($master_array as &$master_item)
        {
            foreach ($need_array as $need_item)
            {
                if ($master_item[$master_link_key] == $need_item[$need_link_key])
                {
                    $master_item = array_merge($master_item, array_intersect_key($need_item, array_fill_keys($need_fields, 0)));
                    continue;
                }
            }
        }
    }

    /**
     * the simple method for set error standout
     *
     * @param array|string|object $data
     * @param int $error_code
     * @param bool $return 是否返回错误码
     * @return void|array
     */
    public static function setEmptyError($data, $error_code, $return = false)
    {
        if (empty($data)) {
            $result = array('error_code' => $error_code);
            if ($return === false) {
                Flight::sendRouteResult($result);
            } else {
                return $result;
            }
        }
    }

    /**
     * 检查返回的值包含的错误信息
     *
     * @param $result
     */
    public static function checkResultError($result)
    {
       if (is_array($result) && array_key_exists('error_code', $result)) {
           Flight::sendRouteResult($result);
       }
    }

    /**
     * 检查参数
     *
     * @param array $need_params
     * @param array $request_data
     */
    public static function checkParams($need_params, $request_data)
    {
        $status = true;

        // check null
        if (empty($request_data)) {
            $status = false;
        }

        // check the param exist
        if ($status == true && !empty($request_data))
        {
            foreach ($need_params as $item)
            {
            	if (!array_key_exists($item, $request_data) 
            		|| !isset($request_data[$item]) 
            			|| is_null($request_data[$item]) 
            				|| $request_data[$item] === '')
                {
                    $status = false;
                    Logger::getLogger('basic') ->debug(array('缺少参数' => $item));
                    break;
                }
                if (in_array($item, array('facility_id', 'task_id', 'product_id', 'container_id', 'plan_case_num', 'transform_product_id')))
                {
                    Flight::checkParamMatchRegex($request_data[$item], '/^[0-9.]+$/');
                }
                if ($item == 'date')
                {
                    $is_date = strtotime($request_data[$item]) ? true:false;
                    if ($is_date === false)
                    {
                        Flight::sendRouteResult(array('error_code' => 42070));
                    }
                }
            }
        }

        if ($status == false) {
            Flight::sendRouteResult(array('error_code' => 70001));  // missing params
        }
    }

    public static function checkUserEmail($email)
    {
        if (preg_match('/^([a-zA-Z0-9_\.\-])+\@(yiran\.com|ibenben\.com)$/', $email) == 0) {
            Flight::sendRouteResult(array('error_code' => 11111, 'error_info' => '邮箱格式错误'));
        }
    }

    public static function convertStrToInQuery($origin_str)
    {
        $result = '';
        if (!empty($origin_str)) {
            $str_arrs = explode(',', $origin_str);            
            if (!empty($str_arrs)) {
                foreach ($str_arrs as $key => &$val) {
                    if (empty($val)) {
                        unset($str_arrs[$key]);
                    }
                    $val = "'" . $val . "'";
                }
                $result = implode(',', $str_arrs);
            }
        }
        return $result;
    }

    /**
     * @note 检查task
     */
	public static function checkTask($task){
		if(!isset($task) || !is_object($task)){
			Flight::sendRouteResult(array('error_code' => 73002));
		}
		if(is_array($task) && array_key_exists('error_code', $task)){
			Flight::sendRouteResult($task);
		}
	}

    /**
     * 检查两个时间大小
     *
     * @note
     * format (2016-04-19)
     *
     * true: $date1 > $date2
     *
     * @param $date1
     * @param $date2
     * @return bool
     */
    public static function checkTwoTime($date1, $date2)
    {
        $date1_stamp = strtotime($date1);
        $date2_stamp = strtotime($date2);
        return $date1_stamp > $date2_stamp;
    }
    
    /**
     * 检查两个时间大小
     *
     * @note
     * format (2016-04-19)
     *	如果有时间是小于06:00:00，则将其加上24:00:00
     * true: $time1 > $time2
     *
     * @param $time1
     * @param $time2
     * @return bool
     */
    public static function compareTwoTimes($time1, $time2)
    {
        $time1_stamp = strtotime($time1)-strtotime("00:00:00");
        $time2_stamp = strtotime($time2)-strtotime("00:00:00");
        $time6_hour = strtotime("06:00:00")-strtotime("00:00:00");
        if($time1_stamp <= $time6_hour){
        	$time1_stamp += 24 * 3600;
        }
        if($time2_stamp <= $time6_hour){
        	$time2_stamp += 24 * 3600;
        }
        return $time1_stamp > $time2_stamp;
    }

    /**
     * search a value/array from a 2d array
     *
     * @note
     * $array_data => array(
     *              0 => array('name'=>'Tom','age'=>12),
     *              1 => array('name'=>'Jim','age'=>15)
     * )
     * $condition => array ('name' => 'Jim'), $key=>'age'
     * result : 15
     * @param array $array_data
     * @param array $condition
     * @param string $key
     * @return string
     */
    public static function searchArrayByCondition($array_data, $condition, $key = '')
    {
        foreach ($array_data as $item)
        {
            $matched = true;
            foreach ($condition as $field => $value)
            {
                if ($item[$field] != $value)
                {
                    $matched = false;
                    break;
                }
            }
            if ($matched === true)
            {
                if (!empty($key))
                {
                    return $item[$key];
                    break;
                }
                return $item;
                break;
            }
        }

        return '';
    }


    /**
     * 获取排序字符串
     *
     * @param $sort_string
     * must like "+created_time,-product_id"
     * @return array
     */
    public static function explodeSortString($sort_string)
    {
        if (empty($sort_string))
        {
            return array();
        }
        $sort_origin_array = explode(',', $sort_string);
        $sort_array = array();
        foreach ($sort_origin_array as $sort_origin_item)
        {
            if (preg_match('/^([+|-])(\w+)$/', $sort_origin_item, $matchs) == 1)
            {
                $sort_type = ($matchs[1] == '+') ? 'ASC' : 'DESC';
                $sort_array[] = array('field' => $matchs[2], 'sort_type' => $sort_type, 'sort_string' => $matchs[2] . ' ' . $sort_type);
            }
        }

        return $sort_array;
    }

    public static function checkTaskIsCancel($task_status)
    {
        if ($task_status == TaskConstant::$TASK_STATUS_CANCEL) {
            Flight::sendRouteResult(array('error_code' => 70022));
        }
    }
	public static function getHourStr($str){
		$result = '';
        $list = explode (":",$str);
        if(empty($list) || !is_array($list)){
        	return $result;
        }
        $result = $list[0];
        if(!empty($list[1]) && intval($list[1]) > 0){
                $result .= ":".$list[1];
        }
        return $result;
	}
}

class ArrayConvert
{
    private static function conversion (&$array)
    {
        $array = (object)$array;
    }
 
    private static function loop (&$array)
    {
        while (list($key, $value) = each($array)) {
            if (is_array($value)) {
                ArrayConvert::loop($array[$key]);
                ArrayConvert::conversion($array[$key]);
            }
        }
        ArrayConvert::conversion($array);
    }
 
    public static function main (&$array)
    {
        if(empty($array)){
            $kkk=array();
            $array = (object)$kkk;
        }else{
            ArrayConvert::loop($array);
        }
    }
}

ClsUtilsTools::$error_array=parse_ini_file("config/errorcode.ini", true);
//ClsUtilsTools::$domain_array=parse_ini_file("config/domain.ini", true);
ClsUtilsTools::$constant_array=parse_ini_file("config/constants.ini", true);
?>
