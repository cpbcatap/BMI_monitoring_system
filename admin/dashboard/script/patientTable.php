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
      window.location.href = `../profile/index.php?user_id=${userId}`;
    });



    let pending_delete_user_id = null;
    let lastFocusedElement = null;

    function openDeleteModal(userId, triggerBtn) {
      pending_delete_user_id = userId;
      lastFocusedElement = triggerBtn; // store button that opened modal

      const overlay = document.getElementById('deleteModal');
      overlay.style.display = 'flex';

      // Move focus inside modal safely
      document.getElementById('deleteConfirmBtn').focus();
    }

    function closeDeleteModal() {
      pending_delete_user_id = null;

      const overlay = document.getElementById('deleteModal');
      overlay.style.display = 'none';

      // Return focus to original button
      if (lastFocusedElement) {
        lastFocusedElement.focus();
        lastFocusedElement = null;
      }
    }

    // Open modal
    $('#patientTable').on('click', '.btn-delete', function() {
      const userId = $(this).data('id');
      openDeleteModal(userId, this);
    });

    // Cancel
    $('#deleteCancelBtn').on('click', function() {
      closeDeleteModal();
    });

    // Click outside to close
    $('#deleteModal').on('click', function(e) {
      if (e.target === this) closeDeleteModal();
    });

    // ESC key
    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape') {
        const overlay = document.getElementById('deleteModal');
        if (overlay.style.display === 'flex') {
          closeDeleteModal();
        }
      }
    });

    // Confirm delete
    $('#deleteConfirmBtn').on('click', function() {
      const userId = pending_delete_user_id;
      if (!userId) return;

      const btn = $(this);
      btn.prop('disabled', true).text('Deleting...');

      $.ajax({
        type: 'POST',
        url: 'api/delete_patient.php',
        data: {
          user_id: userId
        },
        dataType: 'json',
        success: function(res) {
          if (res.success) {
            closeDeleteModal();
            reloadCards();
            table.ajax.reload(null, false);
          } else {
            alert('Error deleting patient: ' + (res.message || 'Unknown error'));
          }
        },
        error: function() {
          alert('An error occurred while trying to delete the patient.');
        },
        complete: function() {
          btn.prop('disabled', false).text('Yes, delete');
        }
      });
    });


  });
</script>