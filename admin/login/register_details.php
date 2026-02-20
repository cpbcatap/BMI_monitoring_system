<?php
session_start();

// Must come from successful verify_otp.php
$email = $_SESSION['otp_verified_email'] ?? '';
if (!($_SESSION['otp_verified'] ?? false) || $email === '') {
  header("Location: register.php");
  exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>BMI Monitoring System</title>

  <link rel="stylesheet" href="../assets/css/icon.css">
  <link rel="stylesheet" href="../assets/css/root.css">
  <link rel="stylesheet" href="../assets/css/login.css">
  <link rel="stylesheet" href="../assets/css/registration.css">
</head>

<body>
  <div class="main">
    <div class="logo"><i class="uil--heart-rate"></i></div>
    <div class="title">BMI Monitoring System</div>
    <div class="subtitle">Know Your Numbers. Monitor Your Progress.</div>

    <div class="form-container">
      <div class="form-title">Create your password</div>

      <form id="detailsForm">
        <!-- show email (disabled) -->
        <input type="email" value="<?= htmlspecialchars($email) ?>" disabled>

        <input type="password" id="password" name="password" placeholder="Password (min 8 chars)" minlength="8" required>
        <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm Password" minlength="8" required>

        <button type="submit" id="password_value">ENTER</button>

        <div class="cancel-link">
          <a href="login.php">Cancel</a>
        </div>
      </form>
    </div>
  </div>

  <script src="../../plugins/js/jquery.min.js"></script>
  <script>
    $('#detailsForm').on('submit', function(e) {
      e.preventDefault();

      const password = $('#password').val();
      const confirm = $('#confirm_password').val();

      if (password.length < 8) return alert('Password must be at least 8 characters.');
      if (password !== confirm) return alert('Passwords do not match.');

      $.ajax({
        type: "POST",
        url: "api/register_admin.php",
        data: {
          password: password
        },
        dataType: "json",
        success: function(res) {
          if (res.ok) {
            alert('Account created successfully!');
            window.location.href = "login.php";
          } else {
            alert(res.error || 'Failed to create account');
          }
        },
        error: function() {
          alert('Server error while creating account');
        }
      });
    });
  </script>
</body>

</html>