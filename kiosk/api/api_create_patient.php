<?php
header('Content-Type: application/json');


$servername = "localhost";
$username = "root";
$password = "";
$dbname = "bmi_monitoring";

try {
    $conn = new PDO(
        "mysql:host=$servername;dbname=$dbname;charset=utf8mb4",
        $username,
        $password,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    
    // get data from AJAX
    $full_name = $_POST['full_name'];
    $birthday = $_POST['birthday'];
    $gender = $_POST['gender'];
    $barangay = $_POST['barangay'];
    $medical_con = $_POST['medical_con'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Hash the password securely
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);


    // $full_name = "TEST PATIENT";

        // if (!$full_name) {
        //     echo json_encode([
        //         "status" => "error",
        //         "message" => "Full name is required"
        //     ]);
        //     exit;
        // }
   

    // SQL INSERT
    // "read_students is the table name"
     $sql = "INSERT INTO profile (full_name, birthday, gender, barangay, medical_con, username, password) 
             VALUES (:full_name, :birthday, :gender, :barangay, :medical_con, :username, :password)";

 


    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':full_name' => $full_name,
        ':birthday' => $birthday,
        ':gender' => $gender,
        ':barangay' => $barangay,
        ':medical_con' => $medical_con,
        ':username' => $username,
        ':password' => $hashed_password,
        
        
    ]);

    echo json_encode([
        "status" => "success",
        "message" => "patient added successfully"
    ]);

} catch (PDOException $e) {
    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}
