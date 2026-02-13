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
        <div class="header-title">Staff Dashboard</div>
        <div class="header-subtitle">Welcome, Healthcare Worker</div>
      </div>
      <div class="header-right">
        <button class="logout">Logout</button>
      </div>
    </div>

    <!-- Structure for Cards -->
    <div class="cards-container">
      <div class="card-content">
        <div class="card-left">
          <div class="card-title">TOTAL PATIENTS</div>
          <div class="card-value">120</div>
        </div>
        <div class="card-right">
          <div class="card-logo">Card Logo</div>
        </div>
      </div>
      <div class="card-content">
        <div class="card-left">
          <div class="card-title">TOTAL RECORDS</div>
          <div class="card-value">45</div>
        </div>
        <div class="card-right">
          <div class="card-logo">Card Logo</div>
        </div>
      </div>
      <div class="card-content">
        <div class="card-left">
          <div class="card-title">TODAY'S CHECKS</div>
          <div class="card-value">20</div>
        </div>
        <div class="card-right">
          <div class="card-logo">Card Logo</div>
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
            </tr>
          </thead>
          <tbody>
            <!-- Insert Data Dynamically -->
          </tbody>
        </table>

      </div>
    </div>

  </div>

  <script src="../../plugins/js/jquery.min.js"></script>
  <script src="../../plugins/datatables/datatables.min.js"></script>

  <?php include 'script/patientTable.php'; ?>

</body>

</html>