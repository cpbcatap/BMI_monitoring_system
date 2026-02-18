<?php
require __DIR__ . '/../../includes/db.php';
header('Content-Type: application/json; charset=UTF-8');

try {
  // Optional: filter by user_id (recommended)
  $user_id = 1;

  $sql = "
    SELECT
      id AS ID,
      height AS Height,
      weight AS Weight,
      bmi AS BMI,
      classification AS Class,
      timestamp AS Timestamp,
      user_id AS UserID
    FROM records
  ";

  $params = [];
  if ($user_id > 0) {
    $sql .= " WHERE user_id = :user_id ";
    $params[':user_id'] = $user_id;
  }

  // Correct ordering: order by newest record, not user_id
  $sql .= " ORDER BY id DESC, id DESC LIMIT 1";

  $stmt = $pdo->prepare($sql);
  $stmt->execute($params);
  $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

  echo json_encode(['ok' => true, 'data' => $rows], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
  http_response_code(500);
  echo json_encode(['ok' => false, 'data' => [], 'error' => 'Server error']);
}
