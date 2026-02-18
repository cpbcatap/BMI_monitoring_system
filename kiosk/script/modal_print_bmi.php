<script>
  let printTimer = null;
  let printCountdownTimer = null;
  let countdownValue = 10;

  function saveData() {
    console.log("Save button clicked1");

    // TODO: if you already have an AJAX save, call it first,
    // then on success => openPrintModal();
    // For now, open modal immediately:
    openPrintModal();
  }

  function openPrintModal() {
    console.log("Opening print modal");
    const modal = document.getElementById('printModal');
    modal.classList.add('show');

    // reset countdown
    countdownValue = 10;
    document.getElementById('printCountdown').textContent = countdownValue;

    // clear any existing timers
    clearTimers();

    // countdown display
    printCountdownTimer = setInterval(() => {
      countdownValue--;
      if (countdownValue <= 0) {
        document.getElementById('printCountdown').textContent = 0;
        clearTimers();
        // auto-print when time runs out (like your design implies)
        confirmPrinting();
        return;
      }
      document.getElementById('printCountdown').textContent = countdownValue;
    }, 1000);

    // allow ESC to close
    document.addEventListener('keydown', escToClose);
  }

  function closePrintModal() {
    const modal = document.getElementById('printModal');
    modal.classList.remove('show');
    clearTimers();
    document.removeEventListener('keydown', escToClose);
  }

  function escToClose(e) {
    if (e.key === 'Escape') closePrintModal();
  }

  function clearTimers() {
    if (printCountdownTimer) {
      clearInterval(printCountdownTimer);
      printCountdownTimer = null;
    }
    if (printTimer) {
      clearTimeout(printTimer);
      printTimer = null;
    }
  }

  function skipPrinting() {
    console.log("User skipped printing");
    closePrintModal();

    // optional redirect after skipping
    // window.location.href = "record.php";
  }

  function confirmPrinting() {
    console.log("User confirmed printing");
    closePrintModal();

    // TODO: call your printing endpoint or open print page
    // Example:
    // window.open("print_receipt.php?record_id=123", "_blank");
    // OR:
    // window.location.href = "print.php";
  }

  // click outside modal to close (optional)
  document.addEventListener('click', function(e) {
    const modal = document.getElementById('printModal');
    if (!modal.classList.contains('show')) return;

    if (e.target === modal) closePrintModal();
  });

  function viewRecords() {
    console.log("View Records button clicked");
    window.location.href = "record.php";
  }
</script>