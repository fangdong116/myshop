<?php
namespace Tools;
use Flight;

class ClsMessageTools {
	static public function sendSMS() {
		$args = func_get_args();
		if(func_num_args() == 4) {
			return ClsMessageTools::sendSMS1($args[0], $args[1], $args[2], $args[3]);
		} else if(func_num_args() == 2) {
			return ClsMessageTools::sendSMS2($args[0], $args[1]);
		}

		return false;
	}

	static private function sendSMS1($mobile, $provider, $tpl_name, $tpl_values) {
		$path="config/message.ini";
		$config = parse_ini_file($path, true);
		$url = $config[$provider]['message_url'];
		$message_key = $config[$provider]['message_key'];
		$tpl_id = $config[$provider][$tpl_name];
		$tpl_value_arr = array();
		foreach ($tpl_values as $key => $value) {
			$tpl_value_arr[] = "#".$key."#=".urlencode($value);
		}
		$tpl_value = urlencode(implode("&", $tpl_value_arr));
		$tpl_id =
		$query_data = "mobile={$mobile}&tpl_id={$tpl_id}&tpl_value={$tpl_value}&key={$message_key}";
		$ch = curl_init();
		$url = $url . "?". $query_data;
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $response = curl_exec($ch);
		if ($response == NULL) {
			curl_close($ch);
			/* ClsMessageTools::saveSMS($provider, $mobile, implode("&", $tpl_value_arr), 0); */
			return false;
		}

		$error = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		if($error != "200") {
			curl_close($ch);
			/* ClsMessageTools::saveSMS($provider, $mobile, implode("&", $tpl_value_arr), 0); */
            return false;
		}

		if(strpos($response,"0:") === 0) {
			$response = 0;
		}

		curl_close($ch);
		/* ClsMessageTools::saveSMS($provider, $mobile, implode("&", $tpl_value_arr), 1); */
		return $response;
	}


	static private function sendSMS2($provider, $mobiles=array(), $content) {
		$path="config/message.ini";
		$config = parse_ini_file($path, true);
		$url = $config[$provider]['message_url'];
		$key = $config[$provider]['message_key'];
		$secret = $config[$provider]['message_secret'];
		$session = $config[$provider]['message_session'];
		$postfix = $config[$provider]['message_postfix'];
		$msg = $content . " " . $postfix;

		$send_mobiles = implode(',', $mobiles);
		$post_data = "zh={$key}&mm={$secret}&sms_type={$session}&hm={$send_mobiles}&nr=$msg";

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);

        $response = curl_exec($ch);
		if ($response == NULL) {
			curl_close($ch);
			ClsMessageTools::saveSMSs($provider, $mobiles, $msg, 0);
			return false;
		}

		$error = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		if($error != "200") {
			curl_close($ch);
			ClsMessageTools::saveSMSs($provider, $mobiles, $msg, 0);
            return false;
		}

		if(strpos($response,"0:") === 0) {
			$response = 0;
		}

		curl_close($ch);
		ClsMessageTools::saveSMSs($provider, $mobiles, $msg, 1);
		return $response;
	}

	static private function saveSMS($provider, $mobile, $content, $send_result) {
				/* $sql = "insert into message.message_history */
                /*          (result, type, send_time, dest_mobile, user_id, content, server_name) */
				/* 		values */
                /*          ({$send_result}, 'SINGLE', now(), '{$mobile}', '49', '{$content}', '{$provider}') */
				/* "; */
				/* Flight::db()->query($sql); */
	}

	static private function saveSMSs($provider, $mobiles=array(), $content, $send_result) {
		if (!empty($mobiles)) {
			foreach ($mobiles as $key => $mobile) {
				ClsMessageTools::saveSMS($provider, $mobile, $content, $send_result);
			}
		}
	}
}


