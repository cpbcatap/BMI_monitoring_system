<!doctype html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>BMI Monitoring System</title>
  <link rel="stylesheet" href="../assets/css/icon.css" />
  <link rel="stylesheet" href="../assets/css/root.css" />
  <link rel="stylesheet" href="../assets/css/login.css" />
</head>

<body>
  <!-- LOGO SECTION -->
  <div class="main">
    <div class="logo"><i class="uil--heart-rate"></i></div>
    <div class="title">BMI Monitoring System</div>
    <div class="subtitle">Know Your Numbers. Monitor Your Progress.</div>

    <button class="patientLogin" type="submit" onclick="goToLogin()">
      <i class="uil--login"></i> Patient Login
    </button>

    <button class="newPatient" type="submit" onclick="goToCreatePatient()">
      <i class="bxs--user-plus"></i> RegisterNew Patient
    </button>
  </div>

  <script>
    function goToLogin() {
      console.log("HELLO");
      window.location.href = "login.php";
    }

    function goToCreatePatient() {
      window.location.href = "create_patient.php";
    }
  </script>
</body>

</html>