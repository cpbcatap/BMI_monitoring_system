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
      <input type="text" name="username" id="username" placeholder="username" required>
      <input type="password" name="password" id="password" placeholder="password" required>
      <button type="submit">Login</button>

      <div class="register-link">
        Don’t have an account? <a href="create_patient.php">Click here to register</a>
      </div>
    </form>

  </div>

  <script src="../../plugins/js/jquery.min.js"></script>

  <script>
    $("#loginForm").submit(function(e) {
      e.preventDefault();

      $.ajax({
        url: "../api/api_login.php",
        type: "POST",
        data: {
          username: $("#username").val(),
          password: $("#password").val()
        },
        success: function(response) {

          if (response.status === "success") {
            // alert("Login successful!");
            window.location.href = "bmi_calculator.php";
          } else {
            alert(response.message);
          }

        },
        error: function() {
          alert("Something went wrong.");
        }
      });

    });
  </script>
</body>

</html>