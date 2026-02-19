<?php
require __DIR__ . '/../../../includes/db.php';

header('Content-Type: application/json; charset=UTF-8');

try {
  if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['data' => [], 'error' => 'Method not allowed']);
    exit;
  }

  $user_id = (int)($_GET['user_id'] ?? 0);
  if ($user_id <= 0) {
    http_response_code(400);
    echo json_encode(['data' => [], 'error' => 'Missing user_id']);
    exit;
  }

  $sql = "
    SELECT
      r.id AS ID,
      r.timestamp AS Timestamp,
      r.weight AS Weight,
      r.height AS Height,
      r.bmi AS BMI,
      r.classification AS Class
    FROM records r
    WHERE r.user_id = :user_id
    ORDER BY r.id DESC
  ";

  $stmt = $pdo->prepare($sql);
  $stmt->execute([':user_id' => $user_id]);

  $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

  echo json_encode(['data' => $rows], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
  http_response_code(500);
  echo json_encode(['data' => [], 'error' => 'Server error']);
}
