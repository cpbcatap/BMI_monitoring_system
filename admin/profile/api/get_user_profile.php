<?php
require __DIR__ . '/../../../includes/db.php';

header('Content-Type: application/json; charset=UTF-8');


$user_id = (int)($_GET['user_id'] ?? 1);

$sql = "
  SELECT
    full_name,
    username,
    gender,
    birthday,
    barangay
  FROM profile
  WHERE user_id = :user_id
";

try {
  $stmt = $pdo->prepare($sql);
  $stmt->execute([':user_id' => $user_id]);
  $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
  echo json_encode(['data' => $rows], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
  http_response_code(500);
  echo json_encode(['data' => [], 'error' => 'Server error']);
}
