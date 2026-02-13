<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>BMI Monitoring System</title>
  <link rel="stylesheet" href="../assets/css/icon.css">
  <link rel="stylesheet" href="../assets/css/root.css">
  <link rel="stylesheet" href="../assets/css/login.css">
</head>

<body>

  <div class="main">
    <div class="logo"><i class="uil--heart-rate"></i></div>
    <div class="title">BMI Monitoring System</div>
    <div class="subtitle">Know Your Numbers. Monitor Your Progress.</div>
    <div class="buttons">
      <button class="login" onclick="goToLogin()">Login</button>
      <button class="register">Register</button>
    </div>
  </div>

</body>

<script>
  function goToLogin() {
    window.location.href = "login.php";
  }
</script>

</html>