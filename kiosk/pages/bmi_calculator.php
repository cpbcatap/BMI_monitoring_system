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
  <title>BMI Monitoring System</title>
  <link rel="stylesheet" href="../assets/css/icon.css">
  <link rel="stylesheet" href="../assets/css/root.css">
  <link rel="stylesheet" href="../assets/css/login.css">
  <link rel="stylesheet" href="../assets/css/bmi_calculator.css">
  <link rel="stylesheet" href="../assets/css/modal.css">
</head>

<body>
  <!-- LOGO SECTION -->
  <div class="main">
    <div class="logo"><i class="uil--heart-rate"></i></div>
    <div class="title">BMI Monitoring System</div>
    <div class="subtitle">Know Your Numbers. Monitor Your Progress.</div>
    <div class="subtitle start"> START CALCULATING BMI?</div>

    <button class="bmi-start-btn" type="button" onclick="startBMI()"> Start </button>
    <div class="cancel-link">
      <!-- change the link  -->
      <a href="../api/logout.php">Logout</a>
    </div>
  </div>


  <!-- SCANNING MODAL (reuses your modal.css without modifying it) -->
  <div id="scanModal" class="modal-overlay" aria-hidden="true">
    <div class="modal-box modal-success" role="dialog" aria-modal="true" aria-labelledby="scanTitle">
      <div class="modal-icon">
        <!-- simple spinner glyph; you can replace with an icon font if you want -->
        ⏳
      </div>

      <h3 id="scanTitle">Scanning in progress</h3>
      <p id="scanMsg">Please stand still while we measure your height and weight…</p>

      <div class="scan-actions">
        <button id="btnCancelScan" type="button" class="scan-btn scan-btn-secondary" onclick="cancelScan()">
          Cancel
        </button>
      </div>
    </div>
  </div>

  <script>
    const user_id = "<?php echo $_SESSION['user_id']; ?>";
    const ws = new WebSocket("ws://192.168.0.105:8765");

    const scanModal = document.getElementById('scanModal');
    const scanMsg = document.getElementById('scanMsg');
    const btnCancelScan = document.getElementById('btnCancelScan');

    let scanActive = false;

    function openScanModal(message) {
      if (message) scanMsg.textContent = message;
      scanModal.style.display = 'flex'; // your CSS uses flex alignment
      scanModal.setAttribute('aria-hidden', 'false');
    }

    function closeScanModal() {
      scanModal.style.display = 'none';
      scanModal.setAttribute('aria-hidden', 'true');
      btnCancelScan.disabled = false;
    }

    function setScanMessage(message) {
      scanMsg.textContent = message;
    }

    ws.onopen = () => console.log("WS connected");

    ws.onmessage = (e) => {
      const msg = JSON.parse(e.data);
      console.log("WS message:", msg);

      // If Python broadcasts that scanning started
      if (msg.cmd === "started" && msg.ok && String(msg.user_id) === String(user_id)) {
        scanActive = true;
        openScanModal("Scanning… please stand still.");
        return;
      }

      // Final result
      if (msg.cmd === "result" && msg.ok && String(msg.user_id) === String(user_id)) {
        // Save latest scan result for the result page
        sessionStorage.setItem("bmi_last_result", JSON.stringify(msg));

        // Go to result page
        window.location.href = "bmi_result.php";
      }

      // Cancel confirmation
      if (msg.cmd === "canceled" && msg.ok && String(msg.user_id) === String(user_id)) {
        scanActive = false;
        setScanMessage("Scan canceled.");
        setTimeout(closeScanModal, 600);
        return;
      }

      // Errors
      if (msg.cmd === "error") {
        // keep modal open but show message
        openScanModal("Error: " + (msg.error || "Unknown error"));
        btnCancelScan.disabled = false;
        scanActive = false;
      }
    };

    ws.onerror = (e) => {
      console.error("WS error", e);
      openScanModal("Connection error. Please try again.");
      btnCancelScan.disabled = false;
      scanActive = false;
    };

    ws.onclose = () => {
      console.log("WS closed");
      if (scanActive) {
        openScanModal("Connection closed. Please try again.");
        btnCancelScan.disabled = false;
        scanActive = false;
      }
    };

    function startBMI() {
      if (ws.readyState !== WebSocket.OPEN) {
        openScanModal("Not connected to scanner service. Please refresh.");
        return;
      }

      scanActive = true;
      openScanModal("Starting scan…");

      ws.send(JSON.stringify({
        cmd: "start",
        user_id
      }));
    }

    function cancelScan() {
      if (ws.readyState !== WebSocket.OPEN) {
        setScanMessage("Not connected. Cancel failed.");
        return;
      }

      btnCancelScan.disabled = true;
      setScanMessage("Canceling…");

      ws.send(JSON.stringify({
        cmd: "cancel",
        user_id
      }));
    }

    // Optional: click outside modal to do nothing (prevents accidental close)
    scanModal.addEventListener('click', (ev) => {
      if (ev.target === scanModal) {
        // ignore outside click to avoid accidental dismissal
      }
    });
  </script>


</body>

</html>