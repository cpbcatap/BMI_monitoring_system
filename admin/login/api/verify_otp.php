<?php
session_start();
require __DIR__ . '/../../../includes/db.php';

header('Content-Type: application/json; charset=UTF-8');

try {

  if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'Method not allowed']);
    exit;
  }

  $email = trim($_POST['email'] ?? '');
  $otp   = trim($_POST['otp'] ?? '');

  if ($email === '' || $otp === '') {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Missing email or OTP']);
    exit;
  }

  /* ===============================
     CHECK SESSION OTP
     =============================== */

  $sess_email   = $_SESSION['otp_email'] ?? '';
  $sess_hash    = $_SESSION['otp_code'] ?? '';
  $expires_at   = (int)($_SESSION['otp_expires_at'] ?? 0);
  $tries        = (int)($_SESSION['otp_tries'] ?? 0);

  if (!$sess_email || !$sess_hash) {
    echo json_encode(['ok' => false, 'error' => 'No OTP request found. Please resend OTP.']);
    exit;
  }

  if (strcasecmp($email, $sess_email) !== 0) {
    echo json_encode(['ok' => false, 'error' => 'Email mismatch.']);
    exit;
  }

  if (time() > $expires_at) {
    echo json_encode(['ok' => false, 'error' => 'OTP expired. Please resend OTP.']);
    exit;
  }

  if ($tries >= 5) {
    echo json_encode(['ok' => false, 'error' => 'Too many attempts. Please resend OTP.']);
    exit;
  }

  $_SESSION['otp_tries'] = $tries + 1;

  if (!preg_match('/^\d{6}$/', $otp)) {
    echo json_encode(['ok' => false, 'error' => 'Invalid OTP format']);
    exit;
  }

  if (!password_verify($otp, $sess_hash)) {
    echo json_encode(['ok' => false, 'error' => 'Incorrect OTP']);
    exit;
  }

  /* ===============================
     CHECK IF EMAIL ALREADY EXISTS
     =============================== */

  $sql = "
    SELECT id
    FROM admin_account
    WHERE email = :email
    LIMIT 1
  ";

  $stmt = $pdo->prepare($sql);
  $stmt->execute([':email' => $email]);
  $existing = $stmt->fetch(PDO::FETCH_ASSOC);

  if ($existing) {
    echo json_encode(['ok' => false, 'error' => 'Email already registered']);
    exit;
  }

  /* ===============================
     SUCCESS
     =============================== */

  $_SESSION['otp_verified'] = true;
  $_SESSION['otp_verified_email'] = $email;

  // Clear OTP so it cannot be reused
  unset($_SESSION['otp_code'], $_SESSION['otp_expires_at'], $_SESSION['otp_tries']);

  echo json_encode(['ok' => true]);
} catch (Throwable $e) {
  http_response_code(500);
  echo json_encode(['ok' => false, 'error' => 'Server error']);
}
