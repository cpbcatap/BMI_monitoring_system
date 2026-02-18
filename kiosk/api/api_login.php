<?php
session_start();

require __DIR__ . '/../../includes/db.php';

header('Content-Type: application/json; charset=UTF-8');

try {

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
        exit;
    }

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Username and password are required']);
        exit;
    }

    // Check if user exists
    $sql = "
    SELECT
      user_id,
      username,
      password,
      full_name,
      birthday,
      gender,
      barangay
    FROM profile
    WHERE username = :username
    LIMIT 1
  ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([':username' => $username]);

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {

        // Save session
        $_SESSION['user_id']   = $user['user_id'];
        $_SESSION['username']  = $user['username'];
        $_SESSION['full_name'] = $user['full_name'];
        $_SESSION['birthday']  = $user['birthday'];
        $_SESSION['gender']    = $user['gender'];
        $_SESSION['barangay']  = $user['barangay'];

        echo json_encode([
            'status'  => 'success',
            'message' => 'Login successful'
        ], JSON_UNESCAPED_UNICODE);
    } else {

        http_response_code(401);
        echo json_encode([
            'status'  => 'error',
            'message' => 'Invalid username or password'
        ], JSON_UNESCAPED_UNICODE);
    }
} catch (Throwable $e) {

    http_response_code(500);
    echo json_encode([
        'status'  => 'error',
        'message' => 'Server error'
    ], JSON_UNESCAPED_UNICODE);
}
