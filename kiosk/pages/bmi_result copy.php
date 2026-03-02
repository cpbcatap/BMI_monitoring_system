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

    <script>
        // Change this if the website is opened from another device:
        // If the browser is running on the Raspberry Pi itself, localhost is OK.
        // If the browser is on another PC/phone, use ws://192.168.0.105:8765
        const WS_URL = "ws://192.168.0.105:8765/";

        let P_ws = null;
        let P_wsReady = false;

        function wsConnect() {
            if (P_ws && (P_ws.readyState === WebSocket.OPEN || P_ws.readyState === WebSocket.CONNECTING)) return;

            P_wsReady = false;
            P_ws = new WebSocket(WS_URL);

            P_ws.onopen = () => {
                P_wsReady = true;
                console.log("[WS] connected");
            };

            P_ws.onmessage = (ev) => {
                try {
                    const msg = JSON.parse(ev.data);
                    // optional: show toast / console log
                    if (msg.cmd === "print_ok") console.log("[PRINT] OK", msg);
                    if (msg.cmd === "print_error") console.error("[PRINT] ERROR", msg);
                } catch (e) {
                    console.log("[WS] message", ev.data);
                }
            };

            P_ws.onclose = () => {
                P_wsReady = false;
                console.warn("[WS] disconnected");
                // optional auto-reconnect
                setTimeout(wsConnect, 1500);
            };

            P_ws.onerror = (e) => {
                console.error("[WS] error", e);
            };
        }

        function wsSend(obj) {
            wsConnect();
            if (!P_wsReady || !P_ws || P_ws.readyState !== WebSocket.OPEN) {
                console.warn("[WS] not ready to send", obj);
                return false;
            }
            P_ws.send(JSON.stringify(obj));
            return true;
        }

        // connect early
        $(function() {
            wsConnect();
        });
    </script>

    <?php include '../script/get_bmi_result.php';
    ?>
    <?php include '../script/modal_print_bmi.php';
    ?>

    <script>
        // Prevent double-saving (Yes/No/Countdown can all fire)
        let hasSavedBmi = false;

        function getDashboardBmiData() {
            const saved = JSON.parse(sessionStorage.getItem("bmi_last_result") || "null");
            if (!saved || saved.ok !== true || saved.cmd !== "result") return null;

            const classification = ($('.bmi-category').first().text() || '').trim(); // from JSON match.name

            return {
                height: Number(saved.height_cm).toFixed(1),
                weight: Number(saved.weight_kg).toFixed(1),
                bmi: Number(saved.bmi).toFixed(1),
                classification: classification
            };
        }


        function saveBmiRecord(printedFlag) {
            if (hasSavedBmi) return $.Deferred().resolve({
                ok: true,
                skipped: true
            }).promise();

            const data = getDashboardBmiData();

            if (!data) {
                console.warn('BMI data not ready');
                return $.Deferred().reject({
                    ok: false
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
            saveBmiRecord(true).done(function(saveResp) {
                const data = getDashboardBmiData();
                if (!data) {
                    closePrintModal();
                    return;
                }

                // Send print job to Raspberry Pi printer service
                const ok = wsSend({
                    cmd: "print_bmi",
                    user_id: (JSON.parse(sessionStorage.getItem("bmi_last_result") || "{}").user_id || ""),
                    height_cm: Number(data.height),
                    weight_kg: Number(data.weight),
                    bmi: Number(data.bmi),
                    classification: data.classification,
                    ts: Date.now()
                });

                if (!ok) console.warn("Print command not sent (WS not connected)");

                closePrintModal();
                window.location.href = '../api/logout.php';
            }).fail(function() {
                // if save failed, allow retry
                closePrintModal();
            });
        }

        function skipPrinting() {
            saveBmiRecord(false).always(function() {
                closePrintModal();
                window.location.href = '../api/logout.php';
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
                        window.location.href = '../api/logout.php';
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