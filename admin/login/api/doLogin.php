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
  $password = (string)($_POST['password'] ?? '');

  if ($email === '' || $password === '') {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Email and password are required']);
    exit;
  }

  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Invalid email']);
    exit;
  }

  $sql = "
    SELECT id, email, password
    FROM admin_account
    WHERE email = :email
    LIMIT 1
  ";

  $stmt = $pdo->prepare($sql);
  $stmt->execute([':email' => $email]);
  $admin = $stmt->fetch(PDO::FETCH_ASSOC);

  if (!$admin) {
    http_response_code(401);
    echo json_encode(['ok' => false, 'error' => 'Invalid credentials']);
    exit;
  }

  if (!password_verify($password, $admin['password'])) {
    http_response_code(401);
    echo json_encode(['ok' => false, 'error' => 'Invalid credentials']);
    exit;
  }

  // ✅ Login success: set session
  session_regenerate_id(true);
  $_SESSION['admin_id'] = (int)$admin['id'];
  $_SESSION['admin_email'] = $admin['email'];

  echo json_encode(['ok' => true]);
} catch (Throwable $e) {
  http_response_code(500);
  echo json_encode(['ok' => false, 'error' => 'Server error']);
}
