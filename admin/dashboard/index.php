<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
  <link rel="stylesheet" href="../assets/css/icon.css">
  <link rel="stylesheet" href="../assets/css/root.css">
  <link rel="stylesheet" href="../assets/css/dashboard.css">
  <link rel="stylesheet" href="../../plugins/datatables/datatables.min.css">
</head>

<body>

  <div class="main">
    <!-- Structure Header -->
    <div class="header">
      <div class="header-left">
        <div class="header-title">Staff Dashboard</div>
        <div class="header-subtitle">Welcome, Healthcare Worker</div>
      </div>
      <div class="header-right">
        <button class="logout" onclick="goToLogin()">Logout</button>
      </div>
    </div>
    <!-- Structure for Cards -->
    <div class="cards-container">
      <div class="card-content">
        <div class="card-left">
          <div class="card-title">TOTAL PATIENTS</div>
          <div class="card-value" id="totalPatients">120</div>
        </div>
        <div class="card-right">
          <i class="teenyicons--users-solid"></i>
        </div>
      </div>
      <div class="card-content">
        <div class="card-left">
          <div class="card-title">TOTAL RECORDS</div>
          <div class="card-value" id="totalRecords">45</div>
        </div>
        <div class="card-right">
          <i class="vaadin--records"></i>
        </div>
      </div>
      <div class="card-content">
        <div class="card-left">
          <div class="card-title">TODAY'S CHECKS</div>
          <div class="card-value" id="todaysChecks">20</div>
        </div>
        <div class="card-right">
          <i class="tabler--checkup-list"></i>
        </div>
      </div>
    </div>



    <!-- Structure for table -->
    <div class="table-container">
      <div class="table-header">Patient Records</div>
      <div class="table-content">

        <table id="patientTable" class="display nowrap" style="width:100%">
          <thead>
            <tr>
              <th>User ID</th>
              <th>Full Name</th>
              <th>Age</th>
              <th>Gender</th>
              <th>Record Count</th>
              <th>Option</th>
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
    function goToLogin() {
      window.location.href = "../login/login.php";
    }
  </script>
  <script src="../../plugins/js/jquery.min.js"></script>
  <script src="../../plugins/datatables/datatables.min.js"></script>

  <?php include 'script/patientTable.php'; ?>

  <script>
    var totalPatients = document.getElementById('totalPatients');
    var totalRecords = document.getElementById('totalRecords');
    var todaysChecks = document.getElementById('todaysChecks');

    $(function() {
      $.ajax({
        type: "POST",
        url: "api/cards_data.php", // adjust path if needed (see note below)
        dataType: "json",
        success: function(res) {
          if (!res || !res.ok) {
            console.error(res?.error || "Failed to load stats");
            return;
          }

          $("#totalPatients").text(res.data.total_patients);
          $("#totalRecords").text(res.data.total_records);
          $("#todaysChecks").text(res.data.todays_checks);
        },
        error: function(xhr) {
          console.error("Stats API error:", xhr.status, xhr.responseText);
        }
      });
    });
  </script>

</body>

</html>