<script>
  let P_countdownTimer = null;

  function saveData() {
    // just open modal; actual save happens on Yes/No/Timeout
    openPrintModal(10);
  }

  function openPrintModal(seconds = 10) {
    const modal = document.getElementById('printModal');
    modal.classList.add('show');

    startPrintCountdown(seconds);

    document.addEventListener('keydown', escToClose);
  }

  function closePrintModal() {
    const modal = document.getElementById('printModal');
    modal.classList.remove('show');

    clearInterval(P_countdownTimer);
    P_countdownTimer = null;

    document.removeEventListener('keydown', escToClose);
  }

  function escToClose(e) {
    if (e.key === 'Escape') closePrintModal();
  }

  function startPrintCountdown(seconds) {
    let s = seconds;
    document.getElementById('printCountdown').textContent = s;

    clearInterval(P_countdownTimer);
    P_countdownTimer = setInterval(() => {
      s--;
      document.getElementById('printCountdown').textContent = Math.max(0, s);

      if (s <= 0) {
        clearInterval(P_countdownTimer);
        P_countdownTimer = null;

        // Timeout behavior=treat as "skip printing"
        if (typeof window.skipPrinting === "function") {
          window.skipPrinting();
        } else {
          closePrintModal();
        }
      }
    }, 1000);
  }

  // click outside to close (optional)
  document.addEventListener('click', function(e) {
    const modal = document.getElementById('printModal');
    if (!modal.classList.contains('show')) return;
    if (e.target === modal) closePrintModal();
  });

  // navigation (UI)
  function viewRecords() {
    window.location.href = "record.php";
  }
</script>