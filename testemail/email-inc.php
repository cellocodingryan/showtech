<?php
// Import PHPMailer classes into the global namespace
// These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
@session_start();
//Load Composer's autoloader
require 'vendor/autoload.php';


function send_mail($recipient,$subject,$message,$attach = "none") {

    try {
        $mail = new PHPMailer(true);
        $mail->IsSMTP();
        $mail->Timeout = 60;
        $mail->SMTPKeepAlive = true;
        $mail->Host = config::get_value("mail_host");
        $mail->Port = 587;
        $mail->SMTPSecure = 'tls';
        $mail->SMTPAuth = true;
        $mail->isHTML(true);
        $mail->Username = config::get_value("mail_username");
        $mail->Password = config::get_value("mail_password");
        $mail->setFrom('cellocodingryan@gmail.com', "Website");
        $mail->addAddress($recipient);
        $mail->Subject = $subject;
        $mail->Body = $message;
        $res = $mail->send();
        echo $res;
    } catch (Exception $e) {
        echo $e;
    }

}