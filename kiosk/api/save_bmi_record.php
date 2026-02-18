<?php
session_start();
require __DIR__ . '/../../includes/db.php';

header('Content-Type: application/json; charset=UTF-8');

try {
  if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'Method not allowed']);
    exit;
  }

  // Require login
  $user_id = (int)($_SESSION['user_id'] ?? 0);
  if ($user_id <= 0) {
    http_response_code(401);
    echo json_encode(['ok' => false, 'error' => 'Not authenticated']);
    exit;
  }

  // Read POST body
  $height = trim($_POST['height'] ?? '');
  $weight = trim($_POST['weight'] ?? '');
  $bmi = trim($_POST['bmi'] ?? '');
  $classification = trim($_POST['classification'] ?? '');

  if ($height === '' || $weight === '' || $bmi === '' || $classification === '') {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Missing required fields']);
    exit;
  }

  // Basic validation (optional but recommended)
  $height_f = (float)$height;
  $weight_f = (float)$weight;
  $bmi_f = (float)$bmi;

  if ($height_f <= 0 || $weight_f <= 0 || $bmi_f <= 0) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Invalid numeric values']);
    exit;
  }

  // Insert
  $sql = "
    INSERT INTO records (user_id, height, weight, bmi, classification, timestamp)
    VALUES (:user_id, :height, :weight, :bmi, :classification, NOW())
  ";

  $stmt = $pdo->prepare($sql);
  $stmt->execute([
    ':user_id' => $user_id,
    ':height' => $height_f,
    ':weight' => $weight_f,
    ':bmi' => $bmi_f,
    ':classification' => $classification
  ]);

  echo json_encode([
    'ok' => true,
    'insert_id' => (int)$pdo->lastInsertId()
  ], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
  http_response_code(500);
  echo json_encode(['ok' => false, 'error' => 'Server error']);
}
