document.addEventListener("DOMContentLoaded", function () {
  // Handle status update forms with AJAX
  document.querySelectorAll(".update-status-form").forEach((form) => {
    form.addEventListener("submit", async function (e) {
      e.preventDefault();

      const form = this;
      const button = form.querySelector('button[type="submit"]');
      const originalText = button.innerHTML;
      const applicationId = form.application_id.value;
      const newStatus = form.status.value;
      const row = form.closest("tr");
      const statusBadge = row.querySelector(".status-badge");

      // Show loading state
      button.disabled = true;
      button.innerHTML =
        '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...';

      try {
        const response = await fetch(form.action, {
          method: "POST",
          body: new FormData(form),
          headers: {
            "X-Requested-With": "XMLHttpRequest",
            Accept: "application/json",
          },
        });

        if (!response.ok) {
          throw new Error("Network response was not ok");
        }

        const data = await response.json();

        if (data.success) {
          // Update UI immediately
          updateStatusUI(row, newStatus);

          // Show success message
          showAlert(data.message || "Status updated successfully!", "success");

          // If filtered view, optionally remove the row
          if (
            window.location.search.includes("filter=") &&
            !window.location.search.includes("filter=all")
          ) {
            const currentFilter = new URLSearchParams(
              window.location.search
            ).get("filter");
            if (currentFilter !== newStatus) {
              row.style.display = "none";
            }
          }
        } else {
          throw new Error(data.message || "Failed to update status");
        }
      } catch (error) {
        console.error("Error:", error);
        showAlert(
          error.message || "An error occurred while updating status",
          "danger"
        );
        // Revert button state if error
        button.disabled = false;
        button.innerHTML = originalText;
      }
    });
  });

  // Function to update UI after status change
  function updateStatusUI(row, newStatus) {
    const statusBadge = row.querySelector(".status-badge");
    const acceptBtn = row.querySelector('button[value="Accepted"]');
    const rejectBtn = row.querySelector('button[value="Rejected"]');

    // Update status badge
    statusBadge.className = `badge status-badge bg-${
      newStatus === "Accepted"
        ? "success"
        : newStatus === "Rejected"
        ? "danger"
        : "warning"
    }`;
    statusBadge.textContent = newStatus;

    // Update button states
    if (newStatus === "Accepted") {
      acceptBtn.classList.add("disabled");
      rejectBtn.classList.remove("disabled");
    } else if (newStatus === "Rejected") {
      rejectBtn.classList.add("disabled");
      acceptBtn.classList.remove("disabled");
    }
  }

  // Function to show alerts
  function showAlert(message, type) {
    // Remove existing alerts first
    const existingAlerts = document.querySelectorAll(".alert");
    existingAlerts.forEach((alert) => alert.remove());

    const alertDiv = document.createElement("div");
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.innerHTML = `
            <div class="d-flex align-items-center">
                <i class="fas ${
                  type === "success"
                    ? "fa-check-circle"
                    : "fa-exclamation-triangle"
                } me-2"></i>
                <div>${message}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;

    const container = document.querySelector(".container");
    if (container) {
      container.insertBefore(alertDiv, container.firstChild);

      // Auto-dismiss after 5 seconds
      setTimeout(() => {
        const bsAlert = bootstrap.Alert.getOrCreateInstance(alertDiv);
        bsAlert.close();
      }, 5000);
    }
  }

  // Highlight current filter
  function highlightActiveFilter() {
    const currentFilter =
      new URLSearchParams(window.location.search).get("filter") || "all";
    document.querySelectorAll(".status-filter a").forEach((link) => {
      const filterValue = link.getAttribute("href").split("=")[1];
      if (filterValue === currentFilter) {
        link.classList.add("active");
      } else {
        link.classList.remove("active");
      }
    });
  }

  // Initialize
  highlightActiveFilter();
});

function confirmAction(action) {
    return confirm(`Are you sure you want to ${action} this application?`);
}

function showStatusMessage(message, isError = false) {
    const messageDiv = document.createElement('div');
    messageDiv.className = `alert alert-${isError ? 'danger' : 'success'} alert-dismissible fade show`;
    messageDiv.role = 'alert';
    messageDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;
    
    const container = document.querySelector('.main-content');
    if (container) {
        container.insertBefore(messageDiv, container.firstChild);
        
        // Auto-dismiss after 5 seconds
        setTimeout(() => {
            const alert = bootstrap.Alert.getOrCreateInstance(messageDiv);
            alert.close();
        }, 5000);
    }
}
