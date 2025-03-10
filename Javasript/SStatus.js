// Initial Status (0% by default)
let status = 0; // 0% by default
let statusText = "Status: 0%";
let statusBar = document.getElementById("status-bar");
let statusTextElement = document.getElementById("status-text");

// Function to update the progress bar and status text
function updateStatus(state) {
    // Apply action (Moving to Pending)
    if (state === "apply") {
        if (status === 0 || status === 30) {
            status = 30; // Pending Approval
            statusText = "Status: Pending Approval (30%)";
        }
    } 
    // Approve action (Moving to Approved)
    else if (state === "approved") {
        if (status === 30) {
            status = 80; // Approved
            statusText = "Status: Approved (80%)";
        }
    }
    // Picked action (Final step to 100%)
    else if (state === "picked") {
        if (status === 80) {
            status = 100; // Picked
            statusText = "Status: Picked (100%)";
        }
    }
    // Declined action (Reset to 0%)
    else if (state === "declined") {
        status = 0; // Reset to 0%
        statusText = "Status: Declined (0%)";
    }

    // Update the progress bar and status text based on the current status
    statusBar.style.width = status + "%";
    statusTextElement.innerText = statusText;
}
