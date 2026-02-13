<?php
require __DIR__ . '/../../../includes/db.php';

header('Content-Type: application/json; charset=UTF-8');

try {
  // Total patients (profile rows)
  $total_patients = (int)$pdo->query("SELECT COUNT(*) FROM profile")->fetchColumn();

  // Total records
  $total_records = (int)$pdo->query("SELECT COUNT(*) FROM records")->fetchColumn();

  // Today's checks (records created today)
  $stmt = $pdo->prepare("
    SELECT COUNT(*)
    FROM records
    WHERE DATE(`timestamp`) = CURDATE()
  ");
  $stmt->execute();
  $todays_checks = (int)$stmt->fetchColumn();

  echo json_encode([
    'ok' => true,
    'data' => [
      'total_patients' => $total_patients,
      'total_records'  => $total_records,
      'todays_checks'  => $todays_checks
    ]
  ], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
  http_response_code(500);
  echo json_encode([
    'ok' => false,
    'data' => [
      'total_patients' => 0,
      'total_records'  => 0,
      'todays_checks'  => 0
    ],
    'error' => 'Server error'
  ], JSON_UNESCAPED_UNICODE);
}
