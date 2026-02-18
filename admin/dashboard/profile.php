<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
  <link rel="stylesheet" href="../assets/css/root.css">
  <link rel="stylesheet" href="../assets/css/dashboard.css">
  <link rel="stylesheet" href="../../plugins/datatables/datatables.min.css">
</head>

<body>

  <div class="main">
    <!-- Structure Header -->
    <div class="header">
      <div class="header-left">
        <div class="profile-name">Full Name</div>
        <div class="profile-data">Username:</div>
        <div class="profile-data">Birthday:</div>
        <div class="profile-data">Gender:</div>
        <div class="profile-data">Barangay:</div>
      </div>
      <div class="header-right">
        <button class="logout" onclick="goToLogout()">Logout</button>
      </div>
    </div>

    <!-- Structure for table -->
    <div class="table-container">
      <div class="table-content">

        <table id="recordTable" class="display nowrap" style="width:100%">
          <thead>
            <tr>
              <th>ID</th>
              <th>Date & Time</th>
              <th>Weight (Kg)</th>
              <th>Height (cm)</th>
              <th>BMI</th>
              <th>classification</th>

            </tr>
          </thead>
          <tbody>
            <!-- Insert Data Dynamically -->
          </tbody>
        </table>

      </div>
    </div>

  </div>

  <script>
    function goToLogout() {
      window.location.href = "index.php";
    }
  </script>
  <script src="../../plugins/js/jquery.min.js"></script>
  <script src="../../plugins/datatables/datatables.min.js"></script>

  <?php include 'script/recordTable.php'; ?>

</body>

</html>