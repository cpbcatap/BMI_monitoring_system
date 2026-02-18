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
    <title>BMI Result</title>
    <link rel="stylesheet" href="../assets/css/bmi_result.css">
    <link rel="stylesheet" href="../assets/css/root.css">
    <link rel="stylesheet" href="../assets/css/print_modal.css">
    <link rel="stylesheet" href="../assets/css/icon.css">

</head>

<body>

    <div class="container">

        <!-- LEFT SECTION -->
        <div class="left">

            <div class="card header-card">
                <h2 class="title">Your BMI Result is Ready!</h2>
                <p class="subtitle">Here is your complete health summary</p>
            </div>

            <div class="card result-card">
                <h1 id="height" hidden>166</h1>
                <h1 id="weight" hidden>59</h1>
                <h1 class="bmi-value">--</h1>
                <p class="bmi-category">--</p>
            </div>

            <div class="card advice-card">
                <h3>Health Advice</h3>
                <div class="advice-box">
                    <ul class="advice-list"></ul>
                </div>
            </div>

        </div>

        <!-- RIGHT SECTION -->
        <div class="right">
            <div class="card">
                <h3>Food Examples</h3>
                <div class="content-box">
                    <ul class="food-list"></ul>
                </div>
            </div>

            <div class="card">
                <h3>Nutrition Goals</h3>
                <div class="content-box">
                    <div class="nutrition-goal-text">--</div>
                    <div class="nutrition-range-text" style="margin-top:8px; opacity:0.85;">--</div>
                </div>
            </div>


            <div class="button-group">
                <button class="btn secondary" onclick="viewRecords()">View Records</button>
                <button class="btn primary" onclick="saveData()">Save</button>
                <button class="btn secondary" onclick="goToBmiCal()">Go Back</button>
            </div>

            <footer footer>
                Note: You may take a photo of your dietitian's advice and local food examples for easy reference.
            </footer>
        </div>

    </div>

    <!-- PRINT CONFIRM MODAL -->
    <div class="modal-overlay" id="printModal">
        <div class="print-modal-card" role="dialog" aria-modal="true" aria-labelledby="printModalTitle">

            <!-- optional close X -->
            <button class="modal-close-x" type="button" onclick="closePrintModal()">✕</button>

            <h1 class="print-modal-title" id="printModalTitle">
                <i class="uil--print"></i> Would you like to print your BMI?
            </h1>
            <p class="print-modal-subtitle">
                Your record will be saved. You have limited time to decide.
            </p>

            <div class="print-modal-countdown">
                <div class="label">Printing in</div>
                <div class="value" id="printCountdown">10</div>
                <div class="unit">seconds</div>
            </div>

            <div class="print-modal-buttons">
                <button class="btn secondary" type="button" onclick="skipPrinting()">
                    No, Skip Printing
                </button>
                <button class="btn primary" type="button" onclick="confirmPrinting()">
                    Yes, Print Data
                </button>
            </div>

        </div>
    </div>


    <script src="../../plugins/js/jquery.min.js"></script>
    <?php include '../script/get_bmi_result.php'; ?>
    <?php include '../script/modal_print_bmi.php'; ?>

    <script>
        // Prevent double-saving (Yes/No/Countdown can all fire)
        let hasSavedBmi = false;

        function getDashboardBmiData() {
            // Height / Weight (you currently store them in hidden <h1 id="height">166</h1>)
            const height = ($('#height').text() || '').trim();
            const weight = ($('#weight').text() || '').trim();

            // BMI display
            const bmi = ($('.bmi-value').first().text() || '').trim();

            // Classification display
            const classification = ($('.bmi-category').first().text() || '').trim();

            return {
                height,
                weight,
                bmi,
                classification
            };
        }

        function saveBmiRecord(printedFlag) {
            if (hasSavedBmi) return $.Deferred().resolve({
                ok: true,
                skipped: true
            }).promise();

            const data = getDashboardBmiData();

            // Optional: guard if still "--"
            if (!data.height || !data.weight || !data.bmi || !data.classification ||
                data.bmi === '--' || data.classification === '--') {
                console.warn('BMI data not ready yet:', data);
                return $.Deferred().reject({
                    ok: false,
                    error: 'BMI data not ready'
                }).promise();
            }

            hasSavedBmi = true;

            return $.ajax({
                url: '../api/save_bmi_record.php',
                method: 'POST',
                dataType: 'json',
                data: {
                    height: data.height,
                    weight: data.weight,
                    bmi: data.bmi,
                    classification: data.classification,
                    printed: printedFlag ? 1 : 0
                }
            }).fail(function(xhr) {
                // If server failed, allow retry
                hasSavedBmi = false;
                console.error('Save failed:', xhr.responseText);
            });
        }

        // Hook into your existing modal buttons:
        function confirmPrinting() {
            // Save first, then print
            saveBmiRecord(true).always(function() {
                // your existing print logic here
                // window.print(); OR whatever you already do
                closePrintModal();
            });
        }

        function skipPrinting() {
            saveBmiRecord(false).always(function() {
                closePrintModal();
                // optional redirect or UI update
            });
        }

        // Countdown auto-save when time is up
        // If your save_print_bmi.php already runs countdown, you can just call this when it hits 0.
        // If not, here is a simple countdown starter:
        let countdownTimer = null;

        function startPrintCountdown(seconds) {
            let s = seconds;
            $('#printCountdown').text(s);

            clearInterval(countdownTimer);
            countdownTimer = setInterval(function() {
                s--;
                $('#printCountdown').text(s);

                if (s <= 0) {
                    clearInterval(countdownTimer);
                    // Auto-save as "skipped/auto"
                    saveBmiRecord(false).always(function() {
                        closePrintModal();
                    });
                }
            }, 1000);
        }

        // Example: when you open the modal, call startPrintCountdown(10)
        // openPrintModal() { $('#printModal').addClass('show'); startPrintCountdown(10); }
    </script>


    <script>
        function goToBmiCal() {
            window.location.href = 'bmi_calculator.php';
        }
    </script>
</body>

</html>