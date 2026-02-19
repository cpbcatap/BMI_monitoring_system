<script>
  $(function() {
    const urlParams = new URLSearchParams(window.location.search);
    const userId = urlParams.get('user_id');

    const table = $('#recordTable').DataTable({
      ajax: {
        url: 'api/record_table_data.php',
        data: function(d) {
          d.user_id = userId; // ✅ pass user_id to PHP
        },
        dataSrc: 'data'
      },
      columns: [{
          data: 'ID' //0
        },
        {
          data: 'Timestamp' //1
        },
        {
          data: 'Weight' //2
        },
        {
          data: 'Height'
        },
        {
          data: 'BMI'
        },
        {
          data: 'Class'
        }
      ],
      columnDefs: [{
          targets: 0,
          visible: false
        },
        {
          targets: '_all',
          className: 'dt-left'
        }
      ],
      scrollX: true,
      scrollCollapse: true,
      responsive: false,
      autoWidth: true,
      ordering: true,
      order: [
        [0, 'desc']
      ],
      paging: true,
      searching: true
    });

    function adjustTable() {
      table.columns.adjust().draw(false);
    }
    window.addEventListener('resize', adjustTable);
    setTimeout(adjustTable, 200);

  });
</script>