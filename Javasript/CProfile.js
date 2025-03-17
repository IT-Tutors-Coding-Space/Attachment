document.getElementById("saveProfile").addEventListener("click", function () {
    alert("Profile updated successfully!");
  });
  
  document
    .getElementById("updatePassword")
    .addEventListener("click", function () {
      const currentPassword = document.getElementById("currentPassword").value;
      const newPassword = document.getElementById("newPassword").value;
      const confirmPassword = document.getElementById("confirmPassword").value;
  
      if (newPassword !== confirmPassword) {
        alert("New passwords do not match!");
        return;
      }
      alert("Password updated successfully!");
    });
  