document.addEventListener("DOMContentLoaded", () => {
  // --- Element References ---
  const searchInput = document.getElementById("searchInput"); // Input for searching names
  const filterStatus = document.getElementById("filterStatus"); // Dropdown to filter by status
  const applicationTbody = document.getElementById("applicationTableBody"); // Table body to inject rows
  const detailsModal = new bootstrap.Modal(document.getElementById("detailsModal")); // Bootstrap modal instance
  const modalBody = document.getElementById("modalStudentDetails"); // Element inside modal to show details
  const updateStatusBtnGroup = document.getElementById("modalStatusButtons"); // Button group in modal for status update
  let currentEditingIndex = -1; // Track which application index is being viewed/edited in the modal

  // --- Initial Data ---
  let applications = [
      { id: 1, name: "Alice Wonderland", program: "Web Dev", status: "pending", date: "2024-05-01" },
      { id: 2, name: "Bob The Builder", program: "Data Science", status: "accepted", date: "2024-05-02" },
      { id: 3, name: "Charlie Chaplin", program: "UX Design", status: "rejected", date: "2024-04-28" },
      { id: 4, name: "Diana Prince", program: "Web Dev", status: "pending", date: "2024-05-03" },
  ];

  // --- Core Function: Render Table ---
  const renderTable = () => {
      applicationTbody.innerHTML = ""; // Clear existing rows
      const searchTerm = searchInput.value.toLowerCase();
      const selectedStatus = filterStatus.value;

      const filteredApps = applications.filter(app =>
          (selectedStatus === "all" || app.status === selectedStatus) &&
          (app.name.toLowerCase().includes(searchTerm))
      );

      if (filteredApps.length === 0) {
          applicationTbody.innerHTML = `<tr><td colspan="4" class="text-center text-muted">No matching applications found.</td></tr>`;
          return;
      }

      filteredApps.forEach((app, index) => {
          // Find the original index in the main `applications` array, needed for actions
          const originalIndex = applications.findIndex(originalApp => originalApp.id === app.id);
          const statusBadgeClass = { pending: 'warning', accepted: 'success', rejected: 'danger' }[app.status] || 'secondary';
          const row = document.createElement('tr');
          row.innerHTML = `
              <td>${app.name}</td>
              <td><span class="badge bg-${statusBadgeClass}">${app.status.charAt(0).toUpperCase() + app.status.slice(1)}</span></td>
              <td>${app.date}</td>
              <td>
                  <button class="btn btn-sm btn-info view-btn" data-index="${originalIndex}" title="View Details"><i class="fas fa-eye"></i></button>
                  <button class="btn btn-sm btn-danger delete-btn" data-index="${originalIndex}" title="Delete"><i class="fas fa-trash-alt"></i></button>
              </td>`;
          applicationTbody.appendChild(row);
      });
  };

  // --- Event Listeners ---
  searchInput.addEventListener("input", renderTable);
  filterStatus.addEventListener("change", renderTable);

  // Event delegation for table buttons (View/Delete)
  applicationTbody.addEventListener("click", (e) => {
      const targetButton = e.target.closest('button'); // Find the button clicked
      if (!targetButton) return; // Exit if click wasn't on or inside a button

      const index = parseInt(targetButton.dataset.index, 10); // Get original index from data attribute

      if (targetButton.classList.contains("view-btn")) {
          currentEditingIndex = index; // Store index for modal actions
          const app = applications[index];
          modalBody.innerHTML = `
              <p><strong>Name:</strong> ${app.name}</p>
              <p><strong>Program:</strong> ${app.program}</p>
              <p><strong>Submitted:</strong> ${app.date}</p>
              <p><strong>Current Status:</strong> <span class="badge bg-${{ pending: 'warning', accepted: 'success', rejected: 'danger' }[app.status] || 'secondary'}">${app.status}</span></p>`;
          // Highlight the current status button in the modal
          updateStatusBtnGroup.querySelectorAll('.btn').forEach(btn => {
              btn.classList.toggle('active', btn.dataset.status === app.status);
          });
          detailsModal.show();
      } else if (targetButton.classList.contains("delete-btn")) {
          if (confirm(`Are you sure you want to delete the application for ${applications[index].name}?`)) {
              applications.splice(index, 1); // Remove from data array
              renderTable(); // Re-render the table
          }
      }
  });

  // Event delegation for modal status update buttons
  updateStatusBtnGroup.addEventListener('click', (e) => {
      const targetButton = e.target.closest('button');
      if (!targetButton || currentEditingIndex === -1) return;

      const newStatus = targetButton.dataset.status;
      if (newStatus && applications[currentEditingIndex]) {
          applications[currentEditingIndex].status = newStatus; // Update status in data
          renderTable(); // Re-render table to show change
          detailsModal.hide(); // Close modal
          currentEditingIndex = -1; // Reset index
      }
  });

  // --- Initial Render ---
  renderTable();
});