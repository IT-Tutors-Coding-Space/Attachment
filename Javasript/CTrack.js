 /**
 * Application Tracking System
 * Handles application status updates and document viewing
 */
document.addEventListener("DOMContentLoaded", function () {
  // Initialize auto-dismiss for existing alerts
  autoDismissAlerts();

  // Set up event listeners for status updates
  setupStatusUpdateHandlers();

  // Set up view details modal
  setupViewModal();

  // Form submission handling (if opportunityForm exists)
  const opportunityForm = document.getElementById("opportunityForm");
  if (opportunityForm) {
    setupOpportunityForm(opportunityForm);
  }

  // Remove error class when user interacts with fields
  setupFieldValidation();
});

function setupOpportunityForm(form) {
  form.addEventListener("submit", function (e) {
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
          // Highlight fields with errors
          if (data.message.includes("title already exists")) {
            const titleField = document.getElementById("title");
            titleField.classList.add("is-invalid");
            titleField.focus();
          }
          if (data.fieldErrors && data.fieldErrors.duration) {
            const durationField = document.getElementById("duration");
            durationField.classList.add("is-invalid");
          }
        }
      })
      .catch((error) => {
        showAlert("An unexpected error occurred. Please try again.", "danger");
      })
      .finally(() => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalBtnText;
      });
  });
}

function setupStatusUpdateHandlers() {
  document.querySelectorAll('form[name="update_status"]').forEach((form) => {
    form.addEventListener("submit", async (e) => {
      e.preventDefault();

      const form = e.target;
      const button = form.querySelector('button[type="submit"]');
      const originalText = button.innerHTML;
      const applicationId = form.application_id.value;
      const newStatus = form.status.value;
      const statusBadge = document.querySelector(
        `tr[data-application-id="${applicationId}"] .status-badge`
      );
      const originalStatus = statusBadge.textContent;

      try {
        // Show loading state
        button.disabled = true;
        button.innerHTML = `
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    Updating...
                `;

        // Update status badge immediately for better UX
        if (statusBadge) {
          statusBadge.className = `badge status-badge status-${newStatus.toLowerCase()}`;
          statusBadge.textContent = newStatus;
        }

        const response = await fetch(form.action, {
          method: "POST",
          body: new FormData(form),
          headers: {
            "X-Requested-With": "XMLHttpRequest",
          },
        });

        const data = await response.json();

        if (data.success) {
          showAlert(data.message, "success");

          // Update the status badge again in case server made additional changes
          if (statusBadge) {
            statusBadge.className = `badge status-badge status-${data.newStatus.toLowerCase()}`;
            statusBadge.textContent = data.newStatus;
          }
        } else {
          showAlert(data.message, "danger");

          // Revert status badge if update failed
          if (statusBadge) {
            statusBadge.className = `badge status-badge status-${originalStatus.toLowerCase()}`;
            statusBadge.textContent = originalStatus;
          }
        }
      } catch (error) {
        showAlert("An error occurred while updating the status", "danger");
        console.error("Status update error:", error);

        // Revert status badge on error
        if (statusBadge) {
          statusBadge.className = `badge status-badge status-${originalStatus.toLowerCase()}`;
          statusBadge.textContent = originalStatus;
        }
      } finally {
        button.disabled = false;
        button.innerHTML = originalText;
      }
    });
  });
}

function setupViewModal() {
  const modal = document.getElementById("applicationModal");
  if (!modal) return;

  // Store original status when opening modal
  modal.addEventListener("show.bs.modal", (e) => {
    const button = e.relatedTarget;
    const applicationId = button.getAttribute("data-id");
    loadApplicationDetails(applicationId);
  });
}

async function loadApplicationDetails(applicationId) {
  const modalBody = document.getElementById("applicationDetails");
  if (!modalBody) return;

  // Show loading state
  modalBody.innerHTML = `
        <div class="text-center py-4">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2">Loading application details...</p>
        </div>
    `;

  try {
    const response = await fetch(
      `CTrack.php?action=get_application_details&id=${applicationId}`
    );
    const data = await response.json();

    if (data.success) {
      modalBody.innerHTML = createDetailsContent(data.application);
    } else {
      modalBody.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    ${data.message || "Failed to load application details"}
                </div>
            `;
    }
  } catch (error) {
    console.error("Error loading application details:", error);
    modalBody.innerHTML = `
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle me-2"></i>
                An error occurred while loading application details
            </div>
        `;
  }
}

function createDetailsContent(application) {
  return `
        <div class="row">
            <div class="col-md-6">
                <h5 class="mb-3">Student Information</h5>
                <div class="mb-3">
                    <strong>Name:</strong> ${application.full_name}
                </div>
                <div class="mb-3">
                    <strong>Email:</strong> ${application.email}
                </div>
                <div class="mb-3">
                    <strong>Course:</strong> ${application.course}, Year ${
    application.year_of_study
  }
                </div>
            </div>
            
            <div class="col-md-6">
                <h5 class="mb-3">Application Details</h5>
                <div class="mb-3">
                    <strong>Opportunity:</strong> ${
                      application.opportunity_title
                    }
                </div>
                <div class="mb-3">
                    <strong>Applied On:</strong> ${new Date(
                      application.submitted_at
                    ).toLocaleDateString()}
                </div>
                <div class="mb-3">
                    <strong>Status:</strong>
                    <span class="badge status-badge status-${application.status.toLowerCase()}">
                        ${application.status}
                    </span>
                </div>
            </div>
        </div>
        
        <div class="mt-4">
            <h5 class="mb-3">Cover Letter</h5>
            <div class="card card-body bg-light">
                ${
                  application.cover_letter ||
                  "<em>No cover letter provided</em>"
                }
            </div>
        </div>
        
        <div class="mt-4">
            <h5 class="mb-3">Student Documents</h5>
            <div class="document-list">
                ${createDocumentsList(application.documents)}
            </div>
        </div>
        
        <div class="mt-4 d-flex justify-content-end gap-2">
            <form method="POST" action="CTrack.php" name="update_status" class="d-inline">
                <input type="hidden" name="application_id" value="${
                  application.applications_id
                }">
                <input type="hidden" name="status" value="Accepted">
                <button type="submit" name="update_status" class="btn btn-success">
                    <i class="fas fa-check me-1"></i> Accept
                </button>
            </form>
            <form method="POST" action="CTrack.php" name="update_status" class="d-inline">
                <input type="hidden" name="application_id" value="${
                  application.applications_id
                }">
                <input type="hidden" name="status" value="Rejected">
                <button type="submit" name="update_status" class="btn btn-danger">
                    <i class="fas fa-times me-1"></i> Reject
                </button>
            </form>
        </div>
    `;
}

function createDocumentsList(documents) {
  if (!documents || documents.length === 0) {
    return '<div class="alert alert-info">No documents submitted</div>';
  }

  return `
        <div class="list-group">
            ${documents
              .map(
                (doc) => `
                <a href="../../uploads/${doc.file_path}" 
                   target="_blank" 
                   class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fas fa-file-${getFileIcon(
                          doc.file_type
                        )} me-2"></i>
                        ${doc.document_type}
                    </div>
                    <span class="badge bg-light text-dark">${formatFileSize(
                      doc.file_size
                    )}</span>
                </a>
            `
              )
              .join("")}
        </div>
    `;
}

function getFileIcon(fileType) {
  if (!fileType) return "alt";

  if (fileType.includes("pdf")) return "pdf";
  if (fileType.includes("word")) return "word";
  if (fileType.includes("excel")) return "excel";
  if (fileType.includes("image")) return "image";
  if (fileType.includes("zip")) return "archive";

  return "alt";
}

function formatFileSize(bytes) {
  if (!bytes) return "0 Bytes";

  const k = 1024;
  const sizes = ["Bytes", "KB", "MB", "GB"];
  const i = Math.floor(Math.log(bytes) / Math.log(k));

  return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + " " + sizes[i];
}

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

function autoDismissAlerts() {
  document.querySelectorAll(".alert").forEach((alert) => {
    const closeButton = alert.querySelector(".btn-close");
    const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);

    // Auto-dismiss after 10 seconds
    const dismissTimer = setTimeout(() => bsAlert.close(), 10000);

    // Also close when clicked
    closeButton.addEventListener("click", () => {
      clearTimeout(dismissTimer);
      bsAlert.close();
    });
  });
}

function setupFieldValidation() {
  const titleField = document.getElementById("title");
  if (titleField) {
    titleField.addEventListener("input", function () {
      this.classList.remove("is-invalid");
    });
  }

  const durationField = document.getElementById("duration");
  if (durationField) {
    durationField.addEventListener("change", function () {
      this.classList.remove("is-invalid");
    });
  }
}
