<?php
namespace Model;
use Flight;
use Logger;
use Tools;
use Exception;
use task\TaskConstant;
use biz;
require_once __DIR__."/../tools/cls_basic_data.php";


class UserModel extends Model {
	public static function getAvailableService($role) {
		$sql = "SELECT
					*
				FROM
					staff
				where  role ='{$role}' and status = 'idle'";
		return Flight::db()->getRow($sql);
	}

	public static function createConsultation($params){
		$data = array(
			'user_id' => $params['user_id'],
			'user_name' => $params['user_name'],
			'doctor_id' => isset($params['doctor_id']) ? $params['doctor_id'] : null,
			'doctor_name' => isset($params['doctor_name']) ? $params['doctor_name'] : null,
			'question_type' => $params['question_type'],
			'question_sub_type' => $params['question_sub_type'],
			'status' => 0,
			'created_time' => date('Y-m-d H:i:s')
		);
		return Flight::db() -> insert('consultation', $data);
	}

	public static function createMessage($params){
		$data = array(
				'consultation_id' => $params['consultation_id'],
				'sender_id' => $params['sender_id'],
				'sender_name' => $params['sender_name'],
				'receiver_id' => $params['receiver_id'],
				'receiver_name' => $params['receiver_name'],
				'msg' => $params['msg'],
				'msg_type' => $params['msg_type'],
				'created_time' => date('Y-m-d H:i:s')
		);
		return Flight::db() -> insert('message', $data);
	}

	public static function getMessageList($params){
		$sql = "select
				m.message_id,
				m.sender_name,
				m.receiver_name,
				m.msg,
				m.msg_type,
				m.created_time";
		$sql_body = " from consultation c
					inner JOIN message m on c.consultation_id = m.consultation_id
					where 1 ";
		if(!empty($params['consultation_id'])){
			$sql_body .= " and c.consultation_id = {$params['consultation_id']}";
		}
		if(!empty($params['user_id'])){
			$sql_body .= " and c.user_id = {$params['user_id']}";
		}
		if(!empty($params['msg_type'])){
			$sql_body .= " and m.msg_type = {$params['msg_type']}";
		}
		if(!empty($params['status'])){
			$sql_body .= " and c.status = {$params['status']}";
		}
		$sql .= $sql_body ." order by m.created_time" .static::paging_clause($params);
		$result['message_list'] = Flight::db() -> getAll($sql);
		$sql ="select count(1) ";
		$sql .= $sql_body;
		$result['total'] = Flight::db() -> getOne($sql);
		return $result;
	}

	public static function evaluateConsultation($consultation_id, $evaluation){
		$now = date('Y-m-d H:i:s');
		$sql = "update consultation
			   set evaluation = {$evaluation},
			   		status = 1,
			   		finish_time = '{$now}'
			   where consultation_id = {$consultation_id}";
		\Logger::getLogger('Route')->info($sql);
		Flight::db() -> exec($sql);
	}
}

?>
