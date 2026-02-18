<?php
require __DIR__ . '/../../includes/db.php';

header('Content-Type: application/json; charset=UTF-8');

$sql = "
  SELECT
    id AS ID,
    height AS Height,
    weight AS Weight,
    bmi AS BMI,
    classification AS Class,
    timestamp AS Timestamp
  FROM records
  ORDER BY user_id DESC
";

try {
  $stmt = $pdo->query($sql);
  $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
  echo json_encode(['data' => $rows], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
  http_response_code(500);
  echo json_encode(['data' => [], 'error' => 'Server error']);
}
