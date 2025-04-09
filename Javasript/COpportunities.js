document.addEventListener("DOMContentLoaded", function () {
  const opportunityForm = document.getElementById("opportunityForm");

  if (opportunityForm) {
    opportunityForm.addEventListener("submit", function (e) {
      let isValid = true;

      // Validate company name
      const companyName = document.getElementById("company_name");
      if (!companyName.value.trim()) {
        isValid = false;
        alert("Company name is required");
        companyName.focus();
        return false;
      }

      // Validate title
      const title = document.getElementById("title");
      if (!title.value.trim()) {
        isValid = false;
        alert("Title is required");
        title.focus();
        return false;
      }

      // Validate description
      const description = document.getElementById("description");
      if (!description.value.trim()) {
        isValid = false;
        alert("Description is required");
        description.focus();
        return false;
      }

      // Validate requirements
      const requirements = document.getElementById("requirements");
      if (!requirements.value.trim()) {
        isValid = false;
        alert("Requirements are required");
        requirements.focus();
        return false;
      }

      // Validate location
      const location = document.getElementById("location");
      if (!location.value.trim()) {
        isValid = false;
        alert("Location is required");
        location.focus();
        return false;
      }

      // Validate duration
      const duration = document.getElementById("duration");
      if (!duration.value) {
        isValid = false;
        alert("Duration is required");
        duration.focus();
        return false;
      }

      // Validate available slots
      const availableSlots = document.getElementById("available_slots");
      if (!availableSlots.value || parseInt(availableSlots.value) <= 0) {
        isValid = false;
        alert("Available slots must be a positive number");
        availableSlots.focus();
        return false;
      }

      // Validate deadline
      const deadline = document.getElementById("application_deadline");
      const today = new Date();
      today.setHours(0, 0, 0, 0);
      const deadlineDate = new Date(deadline.value);

      if (!deadline.value || deadlineDate <= today) {
        isValid = false;
        alert("Deadline must be a future date");
        deadline.focus();
        return false;
      }

      if (!isValid) {
        e.preventDefault();
        return false;
      }
    });
  }

  // AJAX form submission handling
  if (opportunityForm) {
    opportunityForm.addEventListener("submit", function (e) {
      e.preventDefault();

      const formData = new FormData(opportunityForm);

      fetch("COpportunities.php", {
        method: "POST",
        body: formData,
        headers: {
          "X-Requested-With": "XMLHttpRequest",
        },
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.success) {
            // Show success message and redirect or refresh
            alert(data.message);
            window.location.href = "COpportunities.php";
          } else {
            // Show error message
            alert(data.message);

            // Highlight problematic fields
            if (data.fieldErrors) {
              if (data.fieldErrors.title) {
                document.getElementById("title").classList.add("is-invalid");
              }
              if (data.fieldErrors.company_name) {
                document
                  .getElementById("company_name")
                  .classList.add("is-invalid");
              }
              if (data.fieldErrors.deadline) {
                document
                  .getElementById("application_deadline")
                  .classList.add("is-invalid");
              }
              if (data.fieldErrors.slots) {
                document
                  .getElementById("available_slots")
                  .classList.add("is-invalid");
              }
              if (data.fieldErrors.duration) {
                document.getElementById("duration").classList.add("is-invalid");
              }
            }
          }
        })
        .catch((error) => {
          console.error("Error:", error);
          alert("An error occurred while submitting the form");
        });
    });
  }
});
