<?php

namespace Controller;
require_once 'tools/cls_utils_tools.php';
use Flight;
use Tools;
use Model;

/**
* 
*/
class UserController
{
    public static  function setRoute(){
        Flight::route('GET /test1', array(get_called_class(), "Test"));
    }
    /**
     * 获取肤质
     * @param $params
     */
    public static function getSkinType($params){
    }

    /**
     * 获取过敏类型
     * @param $params
     */
    public static function  getSensitivityType($params){
    }

    /**
     * 获取问题类型
     * @param $params
     */
    public static function getQuestionType($params){

    }

    /**
     * 获取饮食习惯
     * @param $params
     */
    public static function getMealHabitType($params){

    }

    /**
     * 获取用户上次姨妈开始结束时间
     * @param $params
     */
    public static function getFriendTime($params){

    }

    /**
     * 添加病例信息
     * @param $params
     */
    public static function addDiagnosis($params){

    }

    /**
     * 分页获取病例列表
     * @param $params
     */
    public static function getDiagnosisList($params){
    }
}

?>

