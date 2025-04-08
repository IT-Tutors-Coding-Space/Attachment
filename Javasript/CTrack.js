document.addEventListener("DOMContentLoaded", function () {
  console.log("Document loaded, initializing application tracking...");

  const searchInput = document.getElementById("searchInput");
  const filterStatus = document.getElementById("filterStatus");
  const applicationTable = document.getElementById("applicationTable");
  const applicationModal = new bootstrap.Modal(
    document.getElementById("applicationModal")
  );
  const studentDetails = document.getElementById("studentDetails");
  const acceptBtn = document.getElementById("acceptBtn");
  const rejectBtn = document.getElementById("rejectBtn");

  let applications = [];
  let currentAppId = null;

  // Fetch applications from server
  async function fetchApplications() {
    console.log("Fetching applications...");
    try {
      const response = await fetch("getApplications.php");
      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }
      applications = await response.json();
      console.log("Fetched applications:", applications);
      renderApplications();
    } catch (error) {
      console.error("Error fetching applications:", error);
      alert(
        "Failed to load applications. Please try again or contact support."
      );
    }
  }

  // Render applications to the table
  function renderApplications() {
    console.log("Rendering applications...");
    applicationTable.innerHTML = "";
    const searchTerm = searchInput.value.toLowerCase();
    const statusFilter = filterStatus.value;

    if (applications.length === 0) {
      applicationTable.innerHTML = `
                <tr>
                    <td colspan="5" class="text-center py-4">No applications found</td>
                </tr>
            `;
      return;
    }

    applications.forEach((app) => {
      if (
        (statusFilter === "all" || app.status === statusFilter) &&
        (app.student_name.toLowerCase().includes(searchTerm) ||
          app.opportunity_title.toLowerCase().includes(searchTerm))
      ) {
        const row = document.createElement("tr");
        row.innerHTML = `
                    <td>${app.student_name}</td>
                    <td>${app.opportunity_title}</td>
                    <td><span class="badge ${getStatusBadgeClass(
                      app.status
                    )}">${
          app.status.charAt(0).toUpperCase() + app.status.slice(1)
        }</span></td>
                    <td>${new Date(
                      app.application_date
                    ).toLocaleDateString()}</td>
                    <td>
                        <button class="btn btn-sm btn-info view-btn" data-id="${
                          app.id
                        }">
                            <i class="fas fa-eye"></i> View
                        </button>
                    </td>
                `;
        applicationTable.appendChild(row);
      }
    });
  }

  function getStatusBadgeClass(status) {
    switch (status.toLowerCase()) {
      case "pending":
        return "badge-pending";
      case "accepted":
        return "badge-accepted";
      case "rejected":
        return "badge-rejected";
      default:
        return "badge-secondary";
    }
  }

  async function updateApplicationStatus(status) {
    if (!currentAppId) {
      console.error("No application ID selected");
      return;
    }

    console.log(`Updating application ${currentAppId} to status ${status}`);

    try {
      const response = await fetch("CTrack.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          id: currentAppId,
          status: status,
        }),
      });

      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }

      const result = await response.json();
      if (result.success) {
        console.log("Update successful:", result.message);
        fetchApplications();
        applicationModal.hide();
      } else {
        throw new Error(result.message || "Update failed");
      }
    } catch (error) {
      console.error("Error updating application:", error);
      alert("Failed to update application. Please try again.");
    }
  }

  // Event listeners
  searchInput.addEventListener("input", renderApplications);
  filterStatus.addEventListener("change", renderApplications);

  applicationTable.addEventListener("click", function (e) {
    const viewBtn = e.target.closest(".view-btn");
    if (viewBtn) {
      const appId = viewBtn.dataset.id;
      const application = applications.find((app) => app.id == appId);

      if (application) {
        currentAppId = appId;
        console.log("Viewing application:", application);
        studentDetails.innerHTML = `
                    <h5>${application.student_name}</h5>
                    <p><strong>Email:</strong> ${
                      application.student_email || "N/A"
                    }</p>
                    <p><strong>Opportunity:</strong> ${
                      application.opportunity_title
                    }</p>
                    <p><strong>Status:</strong> <span class="badge ${getStatusBadgeClass(
                      application.status
                    )}">${
          application.status.charAt(0).toUpperCase() +
          application.status.slice(1)
        }</span></p>
                    <p><strong>Applied:</strong> ${new Date(
                      application.application_date
                    ).toLocaleString()}</p>
                    <hr>
                    <h6>Cover Letter</h6>
                    <div class="border p-2 bg-light">${
                      application.cover_letter || "No cover letter provided"
                    }</div>
                `;
        applicationModal.show();
      }
    }
  });

  acceptBtn.addEventListener("click", () => {
    if (confirm("Are you sure you want to accept this application?")) {
      updateApplicationStatus("accepted");
    }
  });

  rejectBtn.addEventListener("click", () => {
    if (confirm("Are you sure you want to reject this application?")) {
      updateApplicationStatus("rejected");
    }
  });

  // Initial load
  fetchApplications();
});
