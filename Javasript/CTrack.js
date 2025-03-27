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
  