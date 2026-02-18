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

      <div class="form-title">
        Register Email Address
      </div>

      <form>
        <input type="email" name="email" placeholder="juandelacruz@gmail.com" required>
        <!-- need clarification if what type of button submit or button -->
        <button type="button" id="send-otp" onclick="goToOTP()">Send OTP</button>
        <div class="cancel-link">
          <a href="login.php">Cancel</a>
        </div>
      </form>

    </div>


  </div>
</body>
<script>
  function goToOTP() {
    window.location.href = "otp.php";
  }
</script>

</html>