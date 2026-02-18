<?php
require __DIR__ . '/../../includes/db.php';

header('Content-Type: application/json; charset=UTF-8');

try {

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
        exit;
    }

    // Validate required fields
    $required = ['full_name', 'birthday', 'gender', 'barangay', 'username', 'password'];
    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => "Missing field: $field"]);
            exit;
        }
    }

    // Sanitize inputs
    $full_name = trim($_POST['full_name']);
    $birthday  = trim($_POST['birthday']);
    $gender    = trim($_POST['gender']);
    $barangay  = trim($_POST['barangay']);
    $username  = trim($_POST['username']);
    $password  = $_POST['password'];

    // Secure password hashing
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $sql = "
    INSERT INTO profile 
      (full_name, birthday, gender, barangay, username, password)
    VALUES 
      (:full_name, :birthday, :gender, :barangay, :username, :password)
  ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':full_name' => $full_name,
        ':birthday'  => $birthday,
        ':gender'    => $gender,
        ':barangay'  => $barangay,
        ':username'  => $username,
        ':password'  => $hashed_password
    ]);

    echo json_encode([
        'status'  => 'success',
        'message' => 'Patient added successfully'
    ], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {

    http_response_code(500);

    echo json_encode([
        'status' => 'error',
        'message' => 'Server error'
    ]);
}
