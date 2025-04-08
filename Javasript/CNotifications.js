document.addEventListener("DOMContentLoaded", function () {
  const notificationForm = document.getElementById("notificationForm");
  const notificationsTableBody = document.getElementById(
    "notificationsTableBody"
  );

  // AJAX form submission
  if (notificationForm) {
    notificationForm.addEventListener("submit", function (e) {
      e.preventDefault();

      const formData = new FormData(this);
      const submitBtn = this.querySelector('button[type="submit"]');
      const originalBtnText = submitBtn.textContent;

      // Show loading state
      submitBtn.disabled = true;
      submitBtn.innerHTML =
        '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Sending...';

      fetch(this.action, {
        method: "POST",
        body: formData,
        headers: {
          "X-Requested-With": "XMLHttpRequest",
          Accept: "application/json",
        },
      })
        .then((response) => {
          if (!response.ok) {
            return response.json().then((err) => Promise.reject(err));
          }
          return response.json();
        })
        .then((data) => {
          if (data.success) {
            showAlert(data.message, "success");

            // Add new notification to the table without reloading
            if (data.notification) {
              const newRow = document.createElement("tr");
              newRow.innerHTML = `
                            <td>${data.notification.notification_id}</td>
                            <td>${escapeHtml(data.notification.message)}</td>
                            <td><span class="badge bg-warning">${
                              data.notification.status
                            }</span></td>
                            <td>${data.notification.created_at}</td>
                        `;

              // Insert at the top of the table
              if (notificationsTableBody.firstChild) {
                notificationsTableBody.insertBefore(
                  newRow,
                  notificationsTableBody.firstChild
                );
              } else {
                notificationsTableBody.appendChild(newRow);
              }
            }

            // Clear the form
            notificationForm.reset();
          } else {
            showAlert(data.message || "Failed to send notification", "danger");
          }
        })
        .catch((error) => {
          console.error("Error:", error);
          showAlert(
            error.message ||
              "An error occurred while sending the notification.",
            "danger"
          );
        })
        .finally(() => {
          // Restore button state
          submitBtn.disabled = false;
          submitBtn.textContent = originalBtnText;
        });
    });
  }

  // Function to show alert messages
  function showAlert(message, type) {
    const existingAlerts = document.querySelectorAll(".alert");
    existingAlerts.forEach((alert) => alert.remove());

    const alertDiv = document.createElement("div");
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.role = "alert";
    alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;

    const container = document.querySelector(".container");
    if (container) {
      container.insertBefore(alertDiv, container.firstChild);

      // Auto-dismiss after 5 seconds
      setTimeout(() => {
        const bsAlert = new bootstrap.Alert(alertDiv);
        bsAlert.close();
      }, 5000);
    }
  }

  // Simple HTML escaping function
  function escapeHtml(unsafe) {
    return unsafe
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;")
      .replace(/'/g, "&#039;");
  }
});
