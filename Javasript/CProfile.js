// CProfile.js
document.addEventListener("DOMContentLoaded", function () {
  const saveProfileButton = document.getElementById("saveProfile");
  const updatePasswordButton = document.getElementById("updatePassword");
  const profileUpdateMessage = document.getElementById("profileUpdateMessage");

  saveProfileButton.addEventListener("click", function () {
    const companyName = document.getElementById("companyName").value;
    const location = document.getElementById("location").value;
    const contact = document.getElementById("contact").value;
    const industry = document.getElementById("industry").value;

    fetch("updateProfile.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        companyName: companyName,
        location: location,
        contact: contact,
        industry: industry,
        action: "updateCompanyInfo",
      }),
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          profileUpdateMessage.className = "alert alert-success";
          profileUpdateMessage.textContent = data.message;
        } else {
          profileUpdateMessage.className = "alert alert-danger";
          profileUpdateMessage.textContent = data.message;
        }
      })
      .catch((error) => {
        console.error("Error:", error);
        profileUpdateMessage.className = "alert alert-danger";
        profileUpdateMessage.textContent = "An unexpected error occurred.";
      });
  });

  updatePasswordButton.addEventListener("click", function () {
    const currentPassword = document.getElementById("currentPassword").value;
    const newPassword = document.getElementById("newPassword").value;
    const confirmPassword = document.getElementById("confirmPassword").value;

    fetch("updateProfile.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        currentPassword: currentPassword,
        newPassword: newPassword,
        confirmPassword: confirmPassword,
        action: "updatePassword",
      }),
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          profileUpdateMessage.className = "alert alert-success";
          profileUpdateMessage.textContent = data.message;
        } else {
          profileUpdateMessage.className = "alert alert-danger";
          profileUpdateMessage.textContent = data.message;
        }
      })
      .catch((error) => {
        console.error("Error:", error);
        profileUpdateMessage.className = "alert alert-danger";
        profileUpdateMessage.textContent = "An unexpected error occurred.";
      });
  });
});
