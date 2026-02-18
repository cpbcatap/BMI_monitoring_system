<!DOCTYPE html>
<html lang="en">
<?php
session_start();
// to protect the page 
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../assets/css/root.css">
    <link rel="stylesheet" href="../assets/css/record.css">
    <link rel="stylesheet" href="../../plugins/datatables/datatables.min.css">
</head>

<body>

    <div class="main">
        <!-- Structure Header -->
        <div class="header">
            <div class="header-left">
                <div class="profile-name">Full Name: <span><?php echo $_SESSION['full_name']; ?></span></div>
                <div class="profile-data">Username: <span><?php echo $_SESSION['username']; ?></span></div>
                <div class="profile-data">Birthday: <span><?php echo $_SESSION['birthday']; ?></span></div>
                <div class="profile-data">Gender: <span><?php echo $_SESSION['gender']; ?></span></div>
                <div class="profile-data">Barangay: <span><?php echo $_SESSION['barangay']; ?></span></div>
            </div>
            <div class="header-right">
                <button class="logout" onclick="goToBmiResult()">Go Back</button>
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


    <script src="../../plugins/js/jquery.min.js"></script>
    <script src="../../plugins/datatables/datatables.min.js"></script>

    <?php include '../script/recordTable.php'; ?>

    <script>
        function goToBmiResult() {
            window.location.href = "bmi_result.php";
        }
    </script>

</body>

</html>