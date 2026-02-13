<?php
// require __DIR__ . '/../../includes/db.php';

$host    = 'localhost';
$db      = 'bmi_monitoring';
$user    = 'root';
$pass    = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
  PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
  PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
  $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
  http_response_code(500);
  exit('Database connection failed.');
}

header('Content-Type: application/json; charset=UTF-8');

$sql = "
  SELECT
    user_id AS UserID,
    full_name AS FullName,
    gender AS Gender,
    birthday AS Birthday
  FROM profile
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
