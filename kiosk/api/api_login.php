<?php
session_start();
header('Content-Type: application/json');

$servername = "localhost";
$dbusername = "root";
$dbpassword = "";
$dbname = "bmi_monitoring";

try {
    $conn = new PDO(
        "mysql:host=$servername;dbname=$dbname;charset=utf8mb4",
        $dbusername,
        $dbpassword,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if(empty($username) || empty($password)){
        echo json_encode([
            "status" => "error",
            "message" => "Username and password are required"
        ]);
        exit;
    }

    // Check if user exists
    $sql = "SELECT * FROM profile WHERE username = :username LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':username' => $username]);

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if($user && password_verify($password, $user['password'])){

        // Save session
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['full_name'] = $user['full_name'];

        echo json_encode([
            "status" => "success",
            "message" => "Login successful"
        ]);

    } else {

        echo json_encode([
            "status" => "error",
            "message" => "Invalid username or password"
        ]);

    }

} catch(PDOException $e) {

    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);

}