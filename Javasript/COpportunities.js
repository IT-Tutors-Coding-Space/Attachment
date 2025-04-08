document.addEventListener("DOMContentLoaded", function () {
  // Prevent form resubmission on page refresh
  if (window.history.replaceState) {
    window.history.replaceState(null, null, window.location.href);
  }

  // Auto-dismiss alerts after 10 seconds
  const autoDismissAlerts = () => {
    const alerts = document.querySelectorAll(".alert");
    alerts.forEach((alert) => {
      setTimeout(() => {
        const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
        bsAlert.close();
      }, 10000);
    });
  };

  // Initialize auto-dismiss for existing alerts
  autoDismissAlerts();

  // Form submission handling
  const opportunityForm = document.getElementById("opportunityForm");

  if (opportunityForm) {
    opportunityForm.addEventListener("submit", function (e) {
      const submitBtn = this.querySelector('button[type="submit"]');
      const originalBtnText = submitBtn.innerHTML;

      // Show loading state
      submitBtn.disabled = true;
      submitBtn.innerHTML =
        '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Posting...';

      // For AJAX form submission
      e.preventDefault();

      const formData = new FormData(this);

      fetch(this.action, {
        method: "POST",
        body: formData,
        headers: {
          "X-Requested-With": "XMLHttpRequest",
        },
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.success) {
            showAlert(data.message, "success");
            // Reset form after 2 seconds
            setTimeout(() => {
              this.reset();
              // Hide form if success
              if (window.location.search.includes("create=true")) {
                window.location.href = "COpportunities.php";
              }
            }, 2000);
          } else {
            showAlert(data.message, "danger");
            // Highlight the title field if it's a duplicate error
            if (data.message.includes("title already exists")) {
              const titleField = document.getElementById("title");
              titleField.classList.add("is-invalid");
              titleField.focus();
            }
          }
        })
        .catch((error) => {
          showAlert(
            "An unexpected error occurred. Please try again.",
            "danger"
          );
        })
        .finally(() => {
          submitBtn.disabled = false;
          submitBtn.innerHTML = originalBtnText;
        });
    });
  }

  // Enhanced alert function
  function showAlert(message, type) {
    // Remove existing alerts
    const existingAlerts = document.querySelectorAll(".alert");
    existingAlerts.forEach((alert) => alert.remove());

    // Create new alert
    const alertDiv = document.createElement("div");
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.role = "alert";
    alertDiv.innerHTML = `
            <div class="d-flex align-items-center">
                <i class="fas ${
                  type === "success"
                    ? "fa-check-circle"
                    : "fa-exclamation-triangle"
                } me-2"></i>
                <div>${message}</div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;

    // Add to DOM
    const container = document.querySelector(".container");
    if (container) {
      container.insertBefore(alertDiv, container.firstChild);

      // Auto-dismiss after 10 seconds
      setTimeout(() => {
        const bsAlert = bootstrap.Alert.getOrCreateInstance(alertDiv);
        bsAlert.close();
      }, 10000);

      // Also close when clicked
      alertDiv.querySelector(".btn-close").addEventListener("click", () => {
        const bsAlert = bootstrap.Alert.getOrCreateInstance(alertDiv);
        bsAlert.close();
      });
    }
  }

  // Remove error class when user starts typing in title field
  const titleField = document.getElementById("title");
  if (titleField) {
    titleField.addEventListener("input", function () {
      this.classList.remove("is-invalid");
    });
  }
});
