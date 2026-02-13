<?php
require __DIR__ . '/../../../includes/db.php';

header('Content-Type: application/json; charset=UTF-8');

// p stands for profile

$sql = "
  SELECT
    p.user_id AS UserID,
    p.full_name AS FullName,
    p.gender AS Gender,
    p.birthday AS Birthday,
    COUNT(r.id) AS RecordCount
  FROM profile p
  LEFT JOIN records r ON p.user_id = r.user_id
  GROUP BY p.user_id
  ORDER BY p.user_id DESC
";


try {
  $stmt = $pdo->query($sql);
  $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
  echo json_encode(['data' => $rows], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
  http_response_code(500);
  echo json_encode(['data' => [], 'error' => 'Server error']);
}
