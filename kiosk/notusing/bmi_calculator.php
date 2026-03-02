<!DOCTYPE html>
<html lang="en">

<?php
session_start();
// to protect the page 
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

?>

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>BMI Monitoring System</title>
  <link rel="stylesheet" href="../assets/css/icon.css">
  <link rel="stylesheet" href="../assets/css/root.css">
  <link rel="stylesheet" href="../assets/css/login.css">
  <link rel="stylesheet" href="../assets/css/bmi_calculator.css">
</head>

<body>
    <!-- LOGO SECTION -->
  <div class="main">
    <div class="logo"><i class="uil--heart-rate"></i></div>
    <div class="title">BMI Monitoring System</div>
    <div class="subtitle">Know Your Numbers. Monitor Your Progress.</div>
    <div class="subtitle start"> START CALCULATING BMI?</div>
    
    <form class="start_container">
      <button  type="submit"> Start </button>
      <div class="cancel-link">
          <!-- change the link  -->
          <a href="../api/logout.php">Logout</a> 
        </div>
    </form>


  </div>
</body>

</html>