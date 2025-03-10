document.addEventListener("DOMContentLoaded", function() {
    // Simulated history data (replace this with real database data when available)
    const historyData = [
        {
            id: 1,
            status: "Pending Approval",
            appliedDate: "2025-03-01"
        },
        {
            id: 2,
            status: "Approved",
            appliedDate: "2025-03-05"
        },
        {
            id: 3,
            status: "Picked",
            appliedDate: "2025-03-10"
        },
        {
            id: 4,
            status: "Declined",
            appliedDate: "2025-03-15"
        }
    ];

    const historyList = document.getElementById("history-list");

    if (historyData.length === 0) {
        historyList.innerHTML = "<p>No applications found in history.</p>";
    } else {
        historyData.forEach(application => {
            const historyItem = document.createElement("div");
            historyItem.classList.add("history-item");

            // Set the status class based on application status
            let statusClass = "";
            if (application.status === "Pending Approval") {
                statusClass = "status-pending";
            } else if (application.status === "Approved") {
                statusClass = "status-approved";
            } else if (application.status === "Picked") {
                statusClass = "status-approved";
            } else if (application.status === "Declined") {
                statusClass = "status-declined";
            }

            // Create HTML structure for each history item
            historyItem.innerHTML = `
                <strong>Application #${application.id}</strong>
                <p class="status ${statusClass}">${application.status}</p>
                <small>Applied on: ${application.appliedDate}</small>
            `;

            // Append the history item to the list
            historyList.appendChild(historyItem);
        });
    }
});
