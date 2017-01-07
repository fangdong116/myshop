<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 16-6-29
 * Time: 下午3:39
 */

namespace Tools;
use Flight;

require 'PHPMailer/class.phpmailer.php';
require 'PHPMailer/class.smtp.php';

class ClsMailTool
{
    public static function postMail($subject, $body, $to, $mail_config)
    {
        $mail = new \PHPMailer();
        $mail->CharSet ="UTF-8";//设定邮件编码，默认ISO-8859-1，如果发中文此项必须设置，否则乱码
        $mail->IsSMTP(); // 设定使用SMTP服务
        $mail->SMTPDebug  = 1;                     // 启用SMTP调试功能
        // 1 = errors and messages
        // 2 = messages only
        $mail->SMTPAuth   = true;                  // 启用 SMTP 验证功能
        $mail->SMTPSecure = "ssl";                 // 安全协议，可以注释掉
        $mail->Host       = $mail_config['host'];      // SMTP 服务器
        $mail->Port       = $mail_config['port'];                   // SMTP服务器的端口号
        $mail->Username   = $mail_config['username'];  // SMTP服务器用户名
        $mail->Password   = $mail_config['password'];            // SMTP服务器密码
        $mail->SetFrom($mail_config['from'], $mail_config['from_name']);
        $mail->AddReplyTo($to, 'user');
        $mail->Subject    = $subject;
        $mail->AltBody    = 'text/html';
        $mail->IsHTML ( false ); //设置内容是否为html类型
        $mail->MsgHTML($body);
        $address = $to;
        $mail->AddAddress($address, '');
        if(!$mail->Send()) {
            if (preg_match('/(Mailbox not found or access denied)/', $mail->ErrorInfo) > 0) {
                return array('error_code' => 11111, 'error_info' => '邮件发送失败，请检查邮箱是否存在');                
            }
            return array('error_code' => 11111, 'error_info' => $mail->ErrorInfo);
        }
        return array();
    }

}