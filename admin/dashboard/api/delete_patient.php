<?php
// api/delete_patient.php
require __DIR__ . '/../../../includes/db.php';

header('Content-Type: application/json; charset=UTF-8');

try {
  if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
  }

  // Read POST
  $user_id = (int)($_POST['user_id'] ?? 0);
  if ($user_id <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing or invalid user_id']);
    exit;
  }

  // If you want to protect admin/system accounts, uncomment:
  // if ($user_id === 1) { ... }

  // Transaction: delete records then profile (or rely on FK ON DELETE CASCADE)
  $pdo->beginTransaction();

  // If your DB has FK with ON DELETE CASCADE from records -> profile,
  // you can delete ONLY from profile and it will auto delete records.
  // Otherwise, delete records first:
  $stmt = $pdo->prepare("DELETE FROM records WHERE user_id = :user_id");
  $stmt->execute([':user_id' => $user_id]);

  $stmt = $pdo->prepare("DELETE FROM profile WHERE user_id = :user_id");
  $stmt->execute([':user_id' => $user_id]);

  if ($stmt->rowCount() <= 0) {
    $pdo->rollBack();
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'User not found or already deleted']);
    exit;
  }

  $pdo->commit();
  echo json_encode(['success' => true], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
  if ($pdo->inTransaction()) $pdo->rollBack();
  http_response_code(500);
  echo json_encode(['success' => false, 'message' => 'Server error']);
}
