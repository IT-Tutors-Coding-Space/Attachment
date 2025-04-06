document.addEventListener("DOMContentLoaded", function () {
const profileSettingsForm = document.getElementById("profileSettingsForm");

// Fetch company data from PHP (encoded in JSON format)
const companyData = <?php echo json_encode($companyDetails); ?>;

// Populate the readonly fields with the company data
if (companyData) {
document.getElementById("companyName").value = companyData.company_name;
document.getElementById("location").value = companyData.location;
document.getElementById("email").value = companyData.email;
}

// Handle form submission
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
const errorText = await response.text();
console.error("HTTP Error:", response.status, errorText);
alert(`HTTP Error ${response.status}: ${errorText}`);
return;
}

const result = await response.json();

if (result.success) {
alert(result.success);
profileSettingsForm.reset();
} else if (result.error) {
alert(result.error);
} else {
console.error("Unexpected response:", result);
alert("An unexpected error occurred.");
}
} catch (error) {
console.error("Fetch Error:", error.message);
alert(`An error occurred: ${error.message}`);
}
});
}
});
