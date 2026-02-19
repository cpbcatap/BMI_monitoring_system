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

      <form id="emailForm">
        <input type="email" name="email" id="email" placeholder="juandelacruz@gmail.com" required>
        <button type="submit" id="send-otp">Send OTP</button>

        <div class="cancel-link">
          <a href="login.php">Cancel</a>
        </div>
      </form>

    </div>
  </div>

  <script src="../../plugins/js/jquery.min.js"></script>

  <script>
    $('#emailForm').on('submit', function(e) {
      e.preventDefault();

      const email = $('#email').val().trim();
      if (!email) return;

      $.ajax({
        type: "POST",
        url: "send_otp.php",
        data: {
          email: email
        },
        dataType: "json",
        success: function(res) {
          if (res.ok) {
            window.location.href = "otp.php?email=" + encodeURIComponent(email);
          } else {
            alert(res.error || "Failed to send OTP");
          }
        },
        error: function() {
          alert("Server error while sending OTP");
        }
      });
    });
  </script>


  <script>
    // function goToOTP() {
    //   window.location.href = "otp.php";
    // }
  </script>
</body>

</html>