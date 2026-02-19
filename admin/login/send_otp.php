<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

session_start();
header('Content-Type: application/json; charset=UTF-8');

// ===== REQUIRE SAME AS WORKING FILE =====
require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

try {

  if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'Method not allowed']);
    exit;
  }

  $email = trim($_POST['email'] ?? '');

  if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Invalid email']);
    exit;
  }

  // Generate OTP
  $otp = (string) random_int(100000, 999999);

  // Store in session
  $_SESSION['otp_email'] = $email;
  $_SESSION['otp_code'] = password_hash($otp, PASSWORD_DEFAULT);
  $_SESSION['otp_expires_at'] = time() + (5 * 60); // 5 minutes
  $_SESSION['otp_tries'] = 0;

  $email_subject = "Your OTP for BMI Monitoring System";
  $email_body =
    "Your OTP is: $otp\n\n" .
    "This code will expire in 5 minutes.\n\n" .
    "If you did not request this, please ignore this email.";

  $sent = sendEmail($email, $email_subject, $email_body);

  if (!$sent) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'Failed to send OTP']);
    exit;
  }

  echo json_encode(['ok' => true]);
  exit;
} catch (Throwable $e) {
  http_response_code(500);
  echo json_encode(['ok' => false, 'error' => 'Server error']);
  exit;
}


/**
 * Send Email Function (Based on your working version)
 */
function sendEmail($recipient, $subject, $body)
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

    $mail->setFrom('innovcentralph@gmail.com', 'BMI Monitoring System');
    $mail->addAddress($recipient); // dynamic recipient

    $mail->isHTML(false);

    $mail->Subject = $subject;
    $mail->Body = $body;

    $mail->send();
    return true;
  } catch (Exception $e) {
    return false;
  }
}
