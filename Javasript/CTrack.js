<<<<<<< HEAD
document.addEventListener("DOMContentLoaded", () => {
    const searchInput = document.getElementById("searchInput");
    const filterStatus = document.getElementById("filterStatus");
    const applicationTable = document.getElementById("applicationTable");
    const modal = document.getElementById("applicationModal");
    const studentDetails = document.getElementById("studentDetails");
    const closeModalButton = document.getElementById("closeModalButton");
    const statusButtons = document.querySelectorAll(".status-btn");
    let applications = [
      { name: "Vincent Omondi", status: "Pending" },
      { name: "Hedmon Achacha", status: "Accepted" },
      { name: "Vera Brenda", status: "Rejected" },
    ];
  
    function renderTable() {
      applicationTable.innerHTML = "";
      applications.forEach((app, index) => {
        if (
          filterStatus.value !== "all" &&
          app.status.toLowerCase() !== filterStatus.value
        )
          return;
        if (!app.name.toLowerCase().includes(searchInput.value.toLowerCase()))
          return;
  
        let row = `<tr>
                  <td>${app.name}</td>
                  <td class="status">${app.status}</td>
                  <td><button class="view-btn" onclick="openModal(${index})">View</button></td>
              </tr>`;
        applicationTable.innerHTML += row;
      });
    }
  
    searchInput.addEventListener("input", renderTable);
    filterStatus.addEventListener("change", renderTable);
  
    window.sortTable = (n) => {
      applications.sort((a, b) =>
        a[Object.keys(a)[n]].localeCompare(b[Object.keys(b)[n]])
      );
      renderTable();
    };
  
    window.openModal = (index) => {
      studentDetails.textContent = `Name: ${applications[index].name}\nStatus: ${applications[index].status}`;
      modal.style.display = "flex";
  
      statusButtons.forEach((button) => {
        button.onclick = () => {
          applications[index].status =
            button.dataset.status.charAt(0).toUpperCase() +
            button.dataset.status.slice(1);
          renderTable();
          closeModal();
        };
      });
    };
  
    window.closeModal = () => {
      modal.style.display = "none";
    };
  
    closeModalButton.addEventListener("click", closeModal);
    window.addEventListener("click", (event) => {
      if (event.target === modal) closeModal();
    });
  
    renderTable();
  });
  
=======
document.addEventListener("DOMContentLoaded", function () {
  const searchInput = document.getElementById("searchInput");
  const filterStatus = document.getElementById("filterStatus");
  const applicationTable = document.getElementById("applicationTable");
  const applicationModal = new bootstrap.Modal(document.getElementById("applicationModal"));
  const studentDetails = document.getElementById("studentDetails");
  
  let applications = [
      { name: "Phenius Mutiga", status: "pending" },
      { name: "Richard Ochieng'", status: "accepted" },
      { name: "Hedmon Achacha", status: "rejected" },
      { name: "Vincent Owuor", status: "pending" }
  ];
  
  function renderApplications() {
      applicationTable.innerHTML = "";
      applications.forEach((app, index) => {
          if (filterStatus.value === "all" || filterStatus.value === app.status) {
              const row = document.createElement("tr");
              row.innerHTML = `
                  <td>${app.name}</td>
                  <td><span class="badge bg-${app.status === 'accepted' ? 'success' : app.status === 'rejected' ? 'danger' : 'warning'}">${app.status}</span></td>
                  <td>
                      <button class="btn btn-info btn-sm view-btn" data-index="${index}"><i class="fa fa-eye"></i></button>
                      <button class="btn btn-warning btn-sm edit-btn" data-index="${index}"><i class="fa fa-edit"></i></button>
                      <button class="btn btn-danger btn-sm delete-btn" data-index="${index}"><i class="fa fa-trash"></i></button>
                  </td>
              `;
              applicationTable.appendChild(row);
          }
      });
  }
  
  searchInput.addEventListener("input", function () {
      const searchText = searchInput.value.toLowerCase();
      applications.forEach((app, index) => {
          const row = applicationTable.rows[index];
          if (app.name.toLowerCase().includes(searchText)) {
              row.style.display = "";
          } else {
              row.style.display = "none";
          }
      });
  });
  
  filterStatus.addEventListener("change", renderApplications);
  
  applicationTable.addEventListener("click", function (event) {
      if (event.target.closest(".view-btn")) {
          const index = event.target.closest(".view-btn").getAttribute("data-index");
          const app = applications[index];
          studentDetails.textContent = `Name: ${app.name} | Status: ${app.status}`;
          applicationModal.show();
      }

      if (event.target.closest(".edit-btn")) {
          const index = event.target.closest(".edit-btn").getAttribute("data-index");
          const newStatus = prompt("Update application status (pending, accepted, rejected):", applications[index].status);
          if (newStatus && ["pending", "accepted", "rejected"].includes(newStatus.toLowerCase())) {
              applications[index].status = newStatus.toLowerCase();
              renderApplications();
          } else {
              alert("Invalid status entered.");
          }
      }

      if (event.target.closest(".delete-btn")) {
          const index = event.target.closest(".delete-btn").getAttribute("data-index");
          if (confirm("Are you sure you want to delete this application?")) {
              applications.splice(index, 1);
              renderApplications();
          }
      }
  });
  
  renderApplications();
});
>>>>>>> origin/main
