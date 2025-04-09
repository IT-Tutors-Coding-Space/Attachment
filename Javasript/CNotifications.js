document.addEventListener("DOMContentLoaded", function () {
  const messageList = document.getElementById("messageList");
  const messageInput = document.getElementById("messageInput");
  const messageForm = document.getElementById("messageForm");
  const studentSelect = document.getElementById("studentSelect");

  // Initialize the messaging functionality
  function initMessaging() {
    // Auto-scroll to bottom of message list
    if (messageList) {
      messageList.scrollTop = messageList.scrollHeight;
    }

    // Handle student selection change
    if (studentSelect) {
      studentSelect.addEventListener("change", function () {
        if (this.value) {
          window.location.href = `CNotifications.php?student_id=${this.value}`;
        }
      });
    }

    // Handle form submission
    if (messageForm) {
      messageForm.addEventListener("submit", function (e) {
        e.preventDefault();
        sendMessage();
      });
    }

    // Handle Enter key press
    if (messageInput) {
      messageInput.addEventListener("keypress", function (e) {
        if (e.key === "Enter") {
          e.preventDefault();
          if (messageForm) messageForm.dispatchEvent(new Event("submit"));
        }
      });
    }

    // Set up auto-refresh
    setupAutoRefresh();
  }

  // Send message to server
  function sendMessage() {
    const messageText = messageInput.value.trim();
    const studentId = studentSelect.value;

    if (!messageText || !studentId) {
      if (!studentId) alert("Please select a student first");
      return;
    }

    const formData = new FormData(messageForm);
    const submitBtn = messageForm.querySelector('button[type="submit"]');
    const originalBtnText = submitBtn.innerHTML;

    // Show loading state
    submitBtn.disabled = true;
    submitBtn.innerHTML = `
            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Sending...
        `;

    fetch("../../api/send-message.php", {
      method: "POST",
      body: formData,
    })
      .then((response) => {
        if (!response.ok) throw new Error("Network response was not ok");
        return response.json();
      })
      .then((data) => {
        if (data.success) {
          // Create and append new message immediately
          const messageDiv = document.createElement("div");
          messageDiv.classList.add("message", "sent");
          messageDiv.innerHTML = `
                    <div class="fw-bold">You:</div>
                    <div>${escapeHtml(messageText)}</div>
                    <div class="small text-muted">Just now</div>
                `;
          messageList.appendChild(messageDiv);
          messageInput.value = "";
          messageList.scrollTop = messageList.scrollHeight;
        } else {
          throw new Error(data.error || "Failed to send message");
        }
      })
      .catch((error) => {
        console.error("Error:", error);
        alert(error.message);
      })
      .finally(() => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalBtnText;
      });
  }

  // Set up auto-refresh of messages
  function setupAutoRefresh() {
    if (!studentSelect || !studentSelect.value) return;

    const refreshMessages = () => {
      fetch(`CNotifications.php?student_id=${studentSelect.value}&ajax=1`)
        .then((response) => response.text())
        .then((html) => {
          const parser = new DOMParser();
          const doc = parser.parseFromString(html, "text/html");
          const newList = doc.getElementById("messageList");
          if (newList) {
            messageList.innerHTML = newList.innerHTML;
            messageList.scrollTop = messageList.scrollHeight;
          }
        })
        .catch((error) => console.error("Refresh error:", error));
    };

    // Refresh every 5 seconds
    setInterval(refreshMessages, 5000);
  }

  // Simple HTML escaping for user input
  function escapeHtml(unsafe) {
    return unsafe
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;")
      .replace(/'/g, "&#039;");
  }

  // Initialize the messaging system
  initMessaging();
});
