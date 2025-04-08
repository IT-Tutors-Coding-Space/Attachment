document.addEventListener("DOMContentLoaded", function () {
  const opportunityForm = document.getElementById("opportunityForm");

  if (opportunityForm) {
    opportunityForm.addEventListener("submit", async function (e) {
      e.preventDefault();

      // Show loading state
      const submitButton = opportunityForm.querySelector(
        'button[type="submit"]'
      );
      const originalButtonText = submitButton.innerHTML;
      submitButton.disabled = true;
      submitButton.innerHTML =
        '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Posting...';

      try {
        const formData = new FormData(opportunityForm);

        const response = await fetch("COpportunities.php", {
          method: "POST",
          body: formData,
          headers: {
            "X-Requested-With": "XMLHttpRequest",
          },
        });

        if (!response.ok) throw new Error("Network response was not ok");

        const result = await response.json();

        if (result.success) {
          // Show success message and reload the page to update the table
          window.location.href =
            "COpportunities.php?success=" + encodeURIComponent(result.message);
        } else {
          // Show error message
          alert(result.message);
        }
      } catch (error) {
        console.error("Error:", error);
        alert("An error occurred. Please try again.");
      } finally {
        // Restore button state
        if (submitButton) {
          submitButton.disabled = false;
          submitButton.innerHTML = originalButtonText;
        }
      }
    });
  }

  // Check for success message in URL
  const urlParams = new URLSearchParams(window.location.search);
  const successMessage = urlParams.get("success");
  if (successMessage) {
    alert(successMessage);
    // Remove the success parameter from URL
    window.history.replaceState({}, document.title, window.location.pathname);
  }
});
