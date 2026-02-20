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
    <form id="loginForm">
      <input type="email" name="email" placeholder="email" required>
      <input type="password" name="password" placeholder="password" required>
      <button type="submit">Login</button>

      <div class="register-link">
        Don’t have an account? <a href="register.php">Click here to register</a>
      </div>
    </form>
  </div>

  <script src="../../plugins/js/jquery.min.js"></script>
  <script>
    $('#loginForm').on('submit', function(e) {
      e.preventDefault();

      $.ajax({
        type: "POST",
        url: "api/doLogin.php",
        data: $(this).serialize(),
        dataType: "json",
        success: function(res) {
          if (res.ok) {
            window.location.href = "../dashboard/index.php"; // change to your admin landing page
          } else {
            alert(res.error || "Login failed");
          }
        },
        error: function() {
          alert("Server error");
        }
      });
    });
  </script>
</body>

</html>