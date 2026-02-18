<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CREATE ONLY</title>
    <link rel="stylesheet" href="../assets/css/root.css">
    <link rel="stylesheet" href="../assets/css/create_patient.css">
    <link rel="stylesheet" href="../assets/css/icon.css">
    <link rel="stylesheet" href="../assets/css/modal.css">
</head>

<body>
    <div class="container">
        <div class="card">

            <div class="card-header">
                <h2 class="card-title">
                    <span class="icon-badge">
                        <i class="bxs--user-plus"></i>
                    </span>
                    Create Your Health Profile
                </h2>

                <button class="btn-logout" onclick="goToLogin()">Go To Login</button>
            </div>

            <form id="addPatientForm">
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" id="full_name" name="full_name" placeholder="Juan Dela Cruz" required>
                </div>
                <div class="form-group">
                    <label>Birthday</label>
                    <input type="date" id="birthday" name="birthday" required>
                </div>
                <div class="form-group">
                    <label>Gender</label>
                    <select id="gender" name="gender" required>
                        <option value="">Select Gender</option>
                        <option>Female</option>
                        <option>Male</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Barangay</label>
                    <select id="barangay" name="barangay" required>
                        <option value="">Loading barangays...</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" id="username" name="username" placeholder="juandelacruz2026" required>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" id="password" name="password" placeholder="********" required>
                </div>

                <button type="submit" class="btn-primary">Create Profile</button>
            </form>
        </div>
        <p id="result"></p>
    </div>

    <!-- =========================
     NOTIFICATION MODAL
    ========================== -->
    <div id="notificationModal" class="modal-overlay">
        <div class="modal-box">
            <div class="modal-icon" id="modalIcon"></div>
            <h3 id="modalTitle"></h3>
            <p id="modalMessage"></p>
            <button id="modalClose" class="btn-primary">OK</button>
        </div>
    </div>

    <script src="../../plugins/js/jquery.min.js"></script>

    <script>
        $(document).ready(function() {

            $.ajax({
                type: "POST", // keeping your preferred format
                url: "../assets/json/brgy_list.json",
                dataType: "json",

                success: function(response) {

                    const select = $("#barangay");
                    select.empty(); // clear loading text

                    select.append('<option value="">Select Barangay</option>');

                    if (response.barangays && response.barangays.length > 0) {

                        $.each(response.barangays, function(index, brgy) {
                            select.append(
                                $('<option>', {
                                    value: brgy,
                                    text: brgy
                                })
                            );
                        });

                    } else {
                        select.append('<option value="">No barangays found</option>');
                    }
                },

                error: function() {
                    $("#barangay")
                        .empty()
                        .append('<option value="">Failed to load barangays</option>');
                }
            });

        });
    </script>

    <script>
        function goToLogin() {
            window.location.href = "login.php";

        }
    </script>

    <script>
        let redirectAfterClose = false;

        function showModal(type, title, message) {

            $("#notificationModal")
                .css("display", "flex") // activate flex centering
                .hide()
                .fadeIn(200);

            $(".modal-box").removeClass("modal-success modal-error");

            if (type === "success") {
                $(".modal-box").addClass("modal-success");
                $("#modalIcon").html("✅");
                redirectAfterClose = true;
            } else {
                $(".modal-box").addClass("modal-error");
                $("#modalIcon").html("❌");
                redirectAfterClose = false;
            }

            $("#modalTitle").text(title);
            $("#modalMessage").text(message);
        }

        $("#modalClose").click(function() {

            $("#notificationModal").fadeOut(200);

            if (redirectAfterClose) {
                window.location.href = "index.php";
            }

        });
    </script>

    <script>
        $(document).ready(function() {

            $("#addPatientForm").on("submit", function(e) {
                e.preventDefault();

                console.log("Form submitted");

                $.ajax({
                    type: "POST",
                    url: "../api/api_create_patient.php",
                    data: $(this).serialize(), // cleaner & safer
                    dataType: "json",

                    success: function(response) {

                        if (response.status === "success") {

                            showModal(
                                "success",
                                "Profile Created!",
                                "Your account has been successfully registered."
                            );

                            $("#addPatientForm")[0].reset();

                        } else {

                            showModal(
                                "error",
                                "Registration Failed",
                                response.message
                            );
                        }
                    },

                    error: function() {
                        showModal(
                            "error",
                            "Server Error",
                            "Something went wrong. Please try again."
                        );
                    }
                });
            });

        });
    </script>

</body>

</html>