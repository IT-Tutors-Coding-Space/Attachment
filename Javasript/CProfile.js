document.addEventListener("DOMContentLoaded", function () {
  const saveProfileBtn = document.getElementById("saveProfile");
  const updatePasswordBtn = document.getElementById("updatePassword");

  saveProfileBtn.addEventListener("click", function () {
      const companyName = document.getElementById("companyName").value.trim();
      const location = document.getElementById("location").value.trim();
      const contact = document.getElementById("contact").value.trim();

      if (!companyName || !location || !contact) {
          alert("Please fill in all fields before saving.");
          return;
      }

      alert("Profile updated successfully!");
  });

  updatePasswordBtn.addEventListener("click", function () {
      const currentPassword = document.getElementById("currentPassword").value;
      const newPassword = document.getElementById("newPassword").value;
      const confirmPassword = document.getElementById("confirmPassword").value;

      if (!currentPassword || !newPassword || !confirmPassword) {
          alert("Please fill in all password fields.");
          return;
      }

      if (newPassword.length < 6) {
          alert("Password must be at least 6 characters long.");
          return;
      }

      if (newPassword !== confirmPassword) {
          alert("New password and confirm password do not match.");
          return;
      }

      alert("Password updated successfully!");
  });
});
