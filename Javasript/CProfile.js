document.addEventListener("DOMContentLoaded", function () {
  const profileSettingsForm = document.getElementById("profileSettingsForm");

  if (profileSettingsForm) {
    profileSettingsForm.addEventListener("submit", async function (event) {
      event.preventDefault();

      // Collect form data
      const formData = new FormData(profileSettingsForm);

      try {
        const response = await fetch("CProfile.php", {
          method: "POST",
          body: formData,
        });

        if (!response.ok) {
          // Handle HTTP errors (e.g., 400, 500)
          const errorText = await response.text(); // Get the error message from the response.
          console.error("HTTP Error:", response.status, errorText);
          alert(
            "An HTTP error occurred. Please check the console for details."
          );
          return; // Stop further execution.
        }

        const result = await response.json();

        if (result.success) {
          alert(result.success);
          profileSettingsForm.reset(); // Clear the form fields after successful update
        } else if (result.error) {
          alert(result.error);
        } else {
          // Handle unexpected response format
          console.error("Unexpected response:", result);
          alert("An unexpected error occurred.");
        }
      } catch (error) {
        console.error("Fetch Error:", error);
        alert(
          "An error occurred while updating the profile. Please check the console for details."
        );
      }
    });
  }
});