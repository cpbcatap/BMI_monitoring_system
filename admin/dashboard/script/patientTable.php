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
          data: 'Birthday'
        },
        {
          data: 'Gender'
        },
        {
          data: 'RecordCount'
        },
        {
          data: null,
          render: function(data, type, row) {
            return `
              <button class="table-btn btn-view" data-id="${row.UserID}">
                View
              </button>
              <button class="table-btn btn-delete" data-id="${row.UserID}">
                Delete
              </button>
            `;
          }
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

    // ✅ Button View Click Event to View Details
    $('#patientTable').on('click', '.btn-view', function() {
      const userId = $(this).data('id');
      alert("ViewUser ID: " + userId);
    });

    // ✅ Button Delete Click Event to Delete Patient
    $('#patientTable').on('click', '.btn-delete', function() {
      const userId = $(this).data('id');
      alert("Delete User ID: " + userId);
    });

  });
</script>