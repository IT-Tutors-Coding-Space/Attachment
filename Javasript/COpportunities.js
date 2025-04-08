document.addEventListener("DOMContentLoaded", function () {
  // Prevent form resubmission on page refresh
  if (window.history.replaceState) {
    window.history.replaceState(null, null, window.location.href);
  }

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

      // For AJAX form submission (uncomment if you want AJAX)
      /*
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert(data.message, 'success');
                    // Reset form or redirect
                    if (!data.redirect) {
                        this.reset();
                    } else {
                        window.location.href = data.redirect;
                    }
                } else {
                    showAlert(data.message, 'danger');
                }
            })
            .catch(error => {
                showAlert('An error occurred. Please try again.', 'danger');
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnText;
            });
            */
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
});
