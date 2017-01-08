<?php
namespace Model;
use Flight;
use Tools;

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
				'sender_id' =>  isset($params['sender_id']) ? $params['sender_id'] : null,
				'sender_name' => isset($params['sender_name']) ? $params['sender_name'] : null,
				'receiver_id' => isset($params['receiver_id']) ? $params['receiver_id'] : null,
				'receiver_name' => isset($params['receiver_name']) ? $params['receiver_name'] : null,
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
		Flight::db() -> exec($sql);
	}
	public static function switchToDoctor($consultation_id, $doctor_id, $doctor_name){
		$sql = "update consultation
			   set doctor_id = {$doctor_id},
			   		doctor_name = '{$doctor_name}'
			   where consultation_id = {$consultation_id}";
		Flight::db() -> exec($sql);
	}

	public static function addSystemMessage($msg, $consultation_id){
		$params = array(
			"consultation_id" => $consultation_id,
			"msg" => $msg,
			"msg_type" => "system"
		);
		static::createMessage($params);
	}

	public static function getTypeList($params, $type_table_name){
		$sql = "select * from {$type_table_name} where 1 ";
		if(!empty($params['type_id'])){
			$sql .= " and type_id = {$params['type_id']}";
		}
		if(isset($params['parent_id']) && is_numeric($params['parent_id'])){
			$sql .= " and parent_id = {$params['parent_id']}";
		}
		return Flight::db() -> getAll($sql);
	}

	public static function addDiagnosis($params){
		$data = array(
				'user_id' => $params['user_id'],
				'user_name' =>  $params['user_name'],
				'age' => isset($params['age']) ? $params['age'] : null,
				'sex' => isset($params['sex']) ? $params['sex'] : null,
				'friend_start_date' => isset($params['friend_start_date']) ? $params['friend_start_date'] : null,
				'friend_end_date' => isset($params['friend_end_date']) ? $params['friend_end_date'] : null,
				'skin_type_ids' => isset($params['skin_type_ids']) ? $params['skin_type_ids'] : null,
				'skin_type_ids' => isset($params['skin_type_ids']) ? $params['skin_type_ids'] : null,
				'sensitivity_type_ids' => isset($params['sensitivity_type_ids']) ? $params['sensitivity_type_ids'] : null,
				'question_type_ids' => isset($params['question_type_ids']) ? $params['question_type_ids'] : null,
				'others' => isset($params['others']) ? $params['others'] : null,
				'body_section_ids' => isset($params['body_section_ids']) ? $params['body_section_ids'] : null,
				'start_bed_time' => isset($params['start_bed_time']) ? $params['start_bed_time'] : null,
				'end_bed_time' => isset($params['end_bed_time']) ? $params['end_bed_time'] : null,
				'min_sleep_time' => isset($params['min_sleep_time']) ? $params['min_sleep_time'] : null,
				'max_sleep_time' => isset($params['max_sleep_time']) ? $params['max_sleep_time'] : null,
				'sleep_quality' => isset($params['sleep_quality']) ? $params['sleep_quality'] : null,
				'meal_habit_type_ids' => isset($params['meal_habit_type_ids']) ? $params['meal_habit_type_ids'] : null,
				'water' => isset($params['water']) ? $params['water'] : null,
				'other_habits' => isset($params['other_habits']) ? $params['other_habits'] : null,
				'created_time' => date('Y-m-d H:i:s'),
				'last_updated_user' => $params['user_name']
		);
		return Flight::db() -> insert('diagnosis', $data);
	}

	public static function editDiagnosis($params){
		$data = array(
				'doctor_id' => isset($params['doctor_id']) ? $params['doctor_id'] : null,
				'doctor_name' => isset($params['doctor_name']) ? $params['doctor_name'] : null,
				'doctor_advice' => isset($params['doctor_advice']) ? $params['doctor_advice'] : null,
				'last_updated_user' => $params['doctor_name']
		);
		return Flight::db() -> update('diagnosis', $data, "  diagnosis_id = {$params['diagnosis_id']}");
	}

	public static function getDiagnosis($diagnosis_id){
		$sql = "select * from diagnosis where diagnosis_id = {$diagnosis_id} ";
		$diagnosis = Flight::db() -> getRow($sql);
		if(!isset($diagnosis)){
			return array();
		}
		$diagnosis['skin_types'] = self::getFormatTypeList($diagnosis['skin_type_ids'], 'skin_type');
		$diagnosis['sensitivity_types'] = self::getFormatTypeList($diagnosis['sensitivity_type_ids'], 'sensitivity_type');
		$diagnosis['question_types'] = self::getFormatTypeList($diagnosis['question_type_ids'], 'question_type');
		$diagnosis['meal_habit_types'] = self::getFormatTypeList($diagnosis['meal_habit_type_ids'], 'meal_habit_type');
		unset($diagnosis['skin_type_ids']);
		unset($diagnosis['sensitivity_type_ids']);
		unset($diagnosis['question_type_ids']);
		unset($diagnosis['meal_habit_type_ids']);
		return $diagnosis;
	}

	public static function  getDiagnosisList($params){
		$sql = "SELECT
				diagnosis_id,
				created_time,
				doctor_id,
				doctor_name,
				doctor_advice,
				skin_type_ids,
				sensitivity_type_ids";
		$sql_body = " from diagnosis d where 1 ";
		if(!empty($params['diagnosis_id'])){
			$sql_body .= " and d.diagnosis_id = {$params['diagnosis_id']}";
		}
		if(!empty($params['user_id'])){
			$sql_body .= " and d.user_id = {$params['user_id']}";
		}
		$sql .= $sql_body ." order by d.created_time desc" .static::paging_clause($params);

		$diagnosis_list = Flight::db() -> getAll($sql);
		foreach($diagnosis_list as &$diagnosis){
			$diagnosis['skin_types'] = self::getFormatTypeList($diagnosis['skin_type_ids'], 'skin_type');
			$diagnosis['sensitivity_types'] = self::getFormatTypeList($diagnosis['sensitivity_type_ids'], 'sensitivity_type');
			unset($diagnosis['skin_type_ids']);
			unset($diagnosis['sensitivity_type_ids']);
//			$diagnosis['question_types'] = self::getFormatTypeList($diagnosis['question_type_ids'], 'question_type');
//			$diagnosis['meal_habit_types'] = self::getFormatTypeList($diagnosis['meal_habit_type_ids'], 'meal_habit_type');
		}
		$result['diagnosis_list'] = $diagnosis_list;
		$sql ="select count(1) ";
		$sql .= $sql_body;
		$result['total'] = Flight::db() -> getOne($sql);
		return $result;
	}

	public static function  getFormatTypeList($son_type_ids, $table_name){
		$sql = "select
					p.type_id,
					p.type_desc,
					s.type_id as son_type_id,
					s.type_desc as son_type_desc
				from {$table_name} p
				inner join {$table_name} s on p.type_id = s.parent_id
				where p.parent_id  = 0 and s.type_id in ($son_type_ids)";
		$list = Flight::db() -> getAll($sql);
		$result = array();
		foreach($list as $item){
			$result['type_id'] = $item['type_id'];
			$result['type_desc'] = $item['type_desc'];
			$result['sub_list'][] = array(
				'type_id' => $item['son_type_id'],
				'type_desc' => $item['son_type_desc']
			);
		}
		return $result;
	}
}

?>
