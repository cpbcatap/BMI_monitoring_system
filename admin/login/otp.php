<!DOCTYPE html>
<html lang="en">

<?php
session_start();

// DEBUG ONLY (remove in production)
echo "<script>
console.log('PHP SESSION otp_email:', " . json_encode($_SESSION['otp_email'] ?? null) . ");
console.log('PHP SESSION otp_code:', " . json_encode($_SESSION['otp_code'] ?? null) . ");
console.log('PHP SESSION otp_expires_at:', " . json_encode($_SESSION['otp_expires_at'] ?? null) . ");
console.log('PHP SESSION otp_tries:', " . json_encode($_SESSION['otp_tries'] ?? null) . ");
</script>";
?>


<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>BMI Monitoring System</title>
  <link rel="stylesheet" href="../assets/css/icon.css">
  <link rel="stylesheet" href="../assets/css/root.css">
  <link rel="stylesheet" href="../assets/css/login.css">
  <link rel="stylesheet" href="../assets/css/registration.css">
  <link rel="stylesheet" href="../assets/css/otp.css">
</head>

<body>
  <div class="main">
    <div class="logo"><i class="uil--heart-rate"></i></div>
    <div class="title">BMI Monitoring System</div>
    <div class="subtitle">Know Your Numbers. Monitor Your Progress.</div>

    <div class="form-container">

      <div class="form-title">
        Enter OTP
      </div>
      <form id="otpForm" class="otp-form">

        <div class="otp-row">
          <input class="otp" maxlength="1" inputmode="numeric" pattern="[0-9]*" required>
          <input class="otp" maxlength="1" inputmode="numeric" pattern="[0-9]*" required>
          <input class="otp" maxlength="1" inputmode="numeric" pattern="[0-9]*" required>
          <input class="otp" maxlength="1" inputmode="numeric" pattern="[0-9]*" required>
          <input class="otp" maxlength="1" inputmode="numeric" pattern="[0-9]*" required>
          <input class="otp" maxlength="1" inputmode="numeric" pattern="[0-9]*" required>
        </div>

        <button type="submit" id="enter-otp">ENTER</button>
      </form>

      <div class="cancel-link">
        <a href="register.php">Cancel</a>
      </div>

    </div>


  </div>

  <script src="../../plugins/js/jquery.min.js"></script>

  <script>
    const email = new URLSearchParams(window.location.search).get('email') || '';

    console.log('OTP page for email:', email);

    // Optional: auto move cursor
    const inputs = document.querySelectorAll('.otp');
    inputs.forEach((inp, idx) => {
      inp.addEventListener('input', () => {
        inp.value = inp.value.replace(/[^0-9]/g, '');
        if (inp.value && idx < inputs.length - 1) inputs[idx + 1].focus();
      });
      inp.addEventListener('keydown', (e) => {
        if (e.key === 'Backspace' && !inp.value && idx > 0) inputs[idx - 1].focus();
      });
    });

    $('#otpForm').on('submit', function(e) {
      e.preventDefault();

      let otp = '';
      inputs.forEach(i => otp += (i.value || ''));
      if (otp.length !== 6) return alert('Enter complete 6-digit OTP');

      $.ajax({
        type: "POST",
        url: "api/verify_otp.php",
        data: {
          email: email,
          otp: otp
        },
        dataType: "json",
        success: function(res) {
          if (res.ok) {
            // Proceed to your actual account details form
            window.location.href = "register_details.php?email=" + encodeURIComponent(email);
          } else {
            alert(res.error || "Invalid OTP");
          }
        },
        error: function() {
          alert("Server error while verifying OTP");
        }
      });
    });
  </script>
</body>

</html>