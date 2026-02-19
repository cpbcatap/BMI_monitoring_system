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
                <button class="logout" onclick="goToDashboard()">Go Back</button>
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


    <script>
        $(document).ready(function() {
            const urlParams = new URLSearchParams(window.location.search);
            const userId = urlParams.get('user_id');

            console.log("userId:", userId);

            $.ajax({
                url: 'api/get_user_profile.php',
                method: 'GET',
                dataType: 'json',
                data: {
                    user_id: userId
                },
                success: function(response) {
                    console.log("response:", response);

                    // response = { data: [ { ... } ] }
                    if (response.data && response.data.length > 0) {
                        const data = response.data[0];

                        $('.profile-name').text(data.full_name);
                        $('.profile-data').eq(0).text('Username: ' + data.username);
                        $('.profile-data').eq(1).text('Birthday: ' + data.birthday);
                        $('.profile-data').eq(2).text('Gender: ' + data.gender);
                        $('.profile-data').eq(3).text('Barangay: ' + data.barangay);
                    } else {
                        console.warn("No profile data returned");
                    }
                },
                error: function(xhr) {
                    console.error("AJAX error:", xhr.responseText);
                }
            });
        });

        function goToDashboard() {
            window.location.href = "../dashboard";
        }
    </script>


    <?php include 'script/recordTable.php'; ?>

</body>

</html>