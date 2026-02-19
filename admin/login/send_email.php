<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

$email_subject = "sample email";
$email_body = "sampleee emaillll body";


sendEmail($email_subject, $email_body);

/**
 * Function to send an email using PHPMailer
 */
function sendEmail($subject, $body)
{
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'innovcentralph@gmail.com';
        $mail->Password = 'emymneyjnzpyizsh';
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;

        $mail->setFrom('innovcentralph@gmail.com');
        $mail->addAddress("jefreytiglaobonyad@gmail.com");

        $mail->isHTML(false); // Send as plain text

        $mail->Subject = $subject;
        $mail->Body = $body;
        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}
