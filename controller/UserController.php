<?php

namespace Controller;
require_once 'tools/cls_utils_tools.php';
use Flight;
use Tools;
use Model;
use Model\UserModel;

/**
* 
*/
class UserController
{
    public static  function setRoute(){
        Flight::route('GET /user/getSkinType', array(get_called_class(), "getSkinType"));
        Flight::route('GET /user/getSensitivityType', array(get_called_class(), "getSensitivityType"));
        Flight::route('GET /user/getQuestionType', array(get_called_class(), "getQuestionType"));
        Flight::route('GET /user/getMealHabitType', array(get_called_class(), "getMealHabitType"));
        Flight::route('POST /user/addDiagnosis', array(get_called_class(), "addDiagnosis"));
        Flight::route('GET /user/getDiagnosisList', array(get_called_class(), "getDiagnosisList"));
        Flight::route('POST /user/updateCalendar', array(get_called_class(), "updateCalendar"));

    }
    /**
     * 获取肤质
     */
    public static function getSkinType(){
        $params = Flight::request()->query->getData();
        $result['skin_types'] = UserModel::getTypeList($params, static::getTableNameByType("skin"));
        Flight::sendRouteResult($result);
    }

    /**
     * 获取过敏类型
     * @param $params
     */
    public static function  getSensitivityType(){
        $params = Flight::request()->query->getData();
        $result['sensitivity_types'] = UserModel::getTypeList($params, static::getTableNameByType("sensitivity"));
        Flight::sendRouteResult($result);
    }

    /**
     * 获取问题类型
     * @param $params
     */
    public static function getQuestionType(){
        $params = Flight::request()->query->getData();
        $result['question'] = UserModel::getTypeList($params, static::getTableNameByType("question"));
        Flight::sendRouteResult($result);
    }

    /**
     * 获取饮食习惯
     * @param $params
     */
    public static function getMealHabitType(){
        $params = Flight::request()->query->getData();
        $result['meal_habit'] = UserModel::getTypeList($params, static::getTableNameByType("meal_habit"));
        Flight::sendRouteResult($result);
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
    public static function addDiagnosis(){
        $params = Flight::request()->data->getData();
        if (empty($params['user_id']) || empty($params['user_name'])) {
            Flight::sendRouteResult(array('error_code' => 42000));
        }
        $diagnosis_id = UserModel::addDiagnosis($params);
        Flight::sendRouteResult(array('diagnosis_id' => $diagnosis_id));
    }

    /**
     * 分页获取病例列表
     * @param $params
     */
    public static function getDiagnosisList(){
        $params = Flight::request()->data->getData();
        $diagnosis_list = UserModel::getDiagnosisList($params);
        Flight::sendRouteResult(array('data' => $diagnosis_list));
    }

    private static function getTableNameByType($type){
        $table_name = "";
        switch($type){
            case "skin":
                $table_name = "ims_cs_skin_type";
                break;
            case "sensitivity":
                $table_name = "ims_cs_sensitivity_type";
                break;
            case "question":
                $table_name = "ims_cs_question_type";
                break;
            case "meal_habit":
                $table_name = "ims_cs_meal_habit_type";
                break;
        }
        return $table_name;
    }

    /**
     * 更新日历
     */
    public static function updateCalendar(){
        $params = Flight::request()->data->getData();
        if (empty($params['user_id']) || empty($params['user_name']) || empty($params['list'])) {
            Flight::sendRouteResult(array('error_code' => 42000));
        }
        foreach($params['list'] as $item){
            UserModel::addDiagnosis()
        }
        Flight::sendRouteResult($params);
    }
}

?>

