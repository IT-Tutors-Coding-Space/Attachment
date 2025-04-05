document.addEventListener("DOMContentLoaded", function () {
  const opportunityForm = document.getElementById("opportunityForm");

  if (opportunityForm) {
    opportunityForm.addEventListener("submit", async function (event) {
      event.preventDefault();

      const formData = new FormData(opportunityForm);

      try {
        const response = await fetch("COpportunities.php", {
          method: "POST",
          body: formData,
        });

        if (!response.ok) {
          const errorText = await response.text();
          console.error("HTTP Error:", response.status, errorText);
          alert(
            "An HTTP error occurred. Please check the console for details."
          );
          return;
        }

        const result = await response.json();

        if (result.success) {
          alert(result.message);
          opportunityForm.reset();
        } else if (result.error) {
          alert(result.message);
        } else {
          console.error("Unexpected response:", result);
          alert("An unexpected error occurred.");
        }
      } catch (error) {
        console.error("Fetch Error:", error);
        alert(
          "An error occurred while posting the opportunity. Please check the console for details."
        );
      }
    });
  }
});


