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

  // Must be OTP-verified first
  $email = trim($_SESSION['otp_verified_email'] ?? '');
  $is_verified = (bool)($_SESSION['otp_verified'] ?? false);

  if (!$is_verified || $email === '') {
    http_response_code(401);
    echo json_encode(['ok' => false, 'error' => 'OTP not verified']);
    exit;
  }

  // Read password from POST
  $password = (string)($_POST['password'] ?? '');
  if (strlen($password) < 8) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Password must be at least 8 characters']);
    exit;
  }

  // Check if email already exists
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
    http_response_code(409);
    echo json_encode(['ok' => false, 'error' => 'Email already registered']);
    exit;
  }

  // Hash password
  $password_hash = password_hash($password, PASSWORD_DEFAULT);
  if ($password_hash === false) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'Failed to hash password']);
    exit;
  }

  // Insert into DB
  $sql = "
    INSERT INTO admin_account (email, password)
    VALUES (:email, :password)
  ";
  $stmt = $pdo->prepare($sql);
  $stmt->execute([
    ':email' => $email,
    ':password' => $password_hash
  ]);

  // Clear OTP verification session so it can't be reused
  unset(
    $_SESSION['otp_verified'],
    $_SESSION['otp_verified_email'],
    $_SESSION['otp_email'],
    $_SESSION['otp_code'],
    $_SESSION['otp_expires_at'],
    $_SESSION['otp_tries']
  );

  echo json_encode(['ok' => true]);
} catch (Throwable $e) {
  http_response_code(500);
  echo json_encode(['ok' => false, 'error' => 'Server error']);
}
