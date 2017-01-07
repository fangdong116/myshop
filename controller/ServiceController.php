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
class ServiceController
{
    public static function setRoute()
    {
        Flight::route('GET /test', function () {
            Flight::sendRouteResult(array("hello world"));
        });
        Flight::route('GET /consultation/getAvailableService', array(get_called_class(), "getAvailableService"));
        Flight::route('POST /consultation/start', array(get_called_class(), "startConsultation"));
        Flight::route('GET /consultation/getMessageList', array(get_called_class(), "getMessageList"));
        Flight::route('POST /consultation/sendMessage', array(get_called_class(), "sendMessage"));
        Flight::route('POST /consultation/evaluate', array(get_called_class(), "evaluate"));
        Flight::route('POST /consultation/finish', array(get_called_class(), "finishConsultation"));
        Flight::route('POST /doctor/createOrder', array(get_called_class(), "createOrder"));
        Flight::route('POST /doctor/editDiagnosis', array(get_called_class(), "editDiagnosis"));
        Flight::route('GET /doctor/getDiagnosis', array(get_called_class(), "getDiagnosis"));
    }

    /**
     * 获取当前在线的客服或医师
     */
    public static function getAvailableService()
    {
        $params = Flight::request()->query->getData();
        $staff = UserModel::getAvailableService($params['role']);
        Flight::sendRouteResult(array("staff" => $staff));
    }


    /**
     *发起咨询
     */
    public static function startConsultation()
    {
        $params = Flight::request()->data->getData();
        if (empty($params['user_id']) || empty($params['question_type'])
                || empty($params['question_sub_type'])
                || (empty($params['doctor_id']) && empty($params['service_id']))) {
            Flight::sendRouteResult(array('error_code' => 42000));
        }
        Flight::db()->start_transaction();
        try {
            $consultation_id = UserModel::createConsultation($params);
            //todo 咨询医生次数减一
            if (!empty($params['doctor_id'])) {

            }
        } catch (\Exception $e) {
            Flight::db()->rollback();
        }
        Flight::db()->commit();
        Flight::sendRouteResult(array("consultation_id" => $consultation_id));
    }

    /**
     * 获取过敏类型
     * @param $params
     */
    public static function  getMessageList()
    {
        $params = Flight::request()->query->getData();
        if (empty($params['user_id']) ) {
            Flight::sendRouteResult(array('error_code' => 42000));
        }
        $params['size'] = empty($params['size']) ? 10 : $params['size'];
        $params['size'] = empty($params['offset']) ? 0 : $params['offset'];

    }

    /**
     * 发送消息
     * @param $params
     */
    public static function sendMessage()
    {
        $params = Flight::request()->data->getData();
        if (empty($params['consultation_id']) || empty($params['sender_id'])
            || empty($params['receiver_id']) || empty($params['msg_type'])
            || empty($params['msg'])) {
            Flight::sendRouteResult(array('error_code' => 42000));
        }
        $message_id = UserModel::createMessage($params);
        Flight::sendRouteResult(array("message_id" => $message_id));
    }

    /**
     * 评价
     * @param $params
     */
    public static function evaluate()
    {


    }

    /**
     * 完结咨询
     * @param $params
     */
    public static function finishConsultation($params)
    {

    }

    /**
     * 创建订单
     * @param $params
     */
    public static function createOrder($params)
    {

    }

    /**
     * 编辑病例
     * @param $params
     */
    public static function editDiagnosis($params)
    {

    }

    /**
     * 获取病例详情
     * @param $params
     */
    public static function getDiagnosis($params)
    {

    }
}

?>

