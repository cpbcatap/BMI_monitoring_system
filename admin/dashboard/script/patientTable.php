<script>
  $(function() {
    const table = $('#patientTable').DataTable({
      ajax: {
        url: 'api/patient_table_data.php',
        dataSrc: 'data'
      },

      columns: [{
          data: 'UserID'
        },
        {
          data: 'FullName'
        },
        {
          data: 'Gender'
        },
        {
          data: 'Birthday'
        }
      ],

      columnDefs: [{
          targets: 0,
          visible: false
        }, // hide ID
        {
          targets: '_all',
          className: 'dt-left'
        }
      ],

      scrollX: true,
      scrollCollapse: true,
      responsive: false,
      autoWidth: true, // IMPORTANT: allow recalculation

      ordering: true,
      order: [
        [0, 'desc']
      ],
      paging: true,
      searching: true,
    });

    /* 🔑 THIS FIXES THE “STUCK WIDTH” ISSUE */
    function adjustTable() {
      table.columns.adjust().draw(false);
    }

    // On resize
    window.addEventListener('resize', adjustTable);

    // On sidebar/layout changes (just in case)
    setTimeout(adjustTable, 200);
  });
</script>