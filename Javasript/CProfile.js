document.addEventListener("DOMContentLoaded", () => {
  console.log("Company Profile Page Loaded");

  // Password form handling
  const passwordForm = document.getElementById("passwordForm");
  if (passwordForm) {
    passwordForm.addEventListener("submit", async (e) => {
      e.preventDefault();

      const form = e.target;
      const submitBtn = form.querySelector('button[type="submit"]');
      const originalBtnText = submitBtn.innerHTML;
      // Ensure logout redirects properly
    document.getElementById('logoutBtn').addEventListener('click', function() {
        window.location.href = '../../auth/logout.php';

      // Client-side validation
      if (!form.checkValidity()) {
        form.classList.add("was-validated");
        return;
      }

      const newPassword = form.querySelector(
        "input[name='new_password']"
      ).value;
      const confirmPassword = form.querySelector(
        "input[name='confirm_password']"
      ).value;

      // Check password match
      if (newPassword !== confirmPassword) {
        alert("New passwords don't match");
        return;
      }

      // Check password strength
      if (newPassword.length < 8) {
        alert("Password must be at least 8 characters");
        return;
      }

      // Show loading state
      submitBtn.disabled = true;
      submitBtn.innerHTML =
        '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Updating...';

      try {
        const formData = new FormData(form);
        const response = await fetch(window.location.href, {
          method: "POST",
          body: formData,
        });

        // Reload to show success/error message
        window.location.reload();
      } catch (error) {
        console.error("Error:", error);
        alert("Error updating password");
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalBtnText;
      }
    });
  }

  // Account deletion with confirmation
  const deleteAccountBtn = document.getElementById("deleteAccountBtn");
  if (deleteAccountBtn) {
    deleteAccountBtn.addEventListener("click", async () => {
      if (
        !confirm(
          "WARNING: This will permanently delete your company account and all related data.\n\nAre you absolutely sure?"
        )
      ) {
        return;
      }

      const confirmation = prompt('Type "DELETE" to confirm account deletion:');
      if (confirmation !== "DELETE") {
        alert("Account deletion cancelled");
        return;
      }

      const btn = deleteAccountBtn;
      const originalText = btn.innerHTML;
      btn.disabled = true;
      btn.innerHTML =
        '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Deleting...';

      try {
        const formData = new FormData();
        formData.append("delete_account", "true");

        const response = await fetch(window.location.href, {
          method: "POST",
          body: formData,
        });

        // Redirect to login page after deletion
        window.location.href = "../../SignUps/Clogin.php";
      } catch (error) {
        console.error("Error:", error);
        alert("Error deleting account");
        btn.disabled = false;
        btn.innerHTML = originalText;
      }
    });
  }
});
