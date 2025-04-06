document.addEventListener("DOMContentLoaded", () => {
    console.log("Student Profile Page Loaded");

    // Password validation
    const passwordForm = document.querySelector("form[name='change_password']");
    if (passwordForm) {
        passwordForm.addEventListener("submit", (e) => {
            const newPassword = passwordForm.querySelector("input[name='new_password']").value;
            const confirmPassword = passwordForm.querySelector("input[name='confirm_password']").value;

            // Check password match
            if (newPassword !== confirmPassword) {
                e.preventDefault();
                showToast("New passwords don't match", "error");
                return;
            }

            // Check password strength
            if (newPassword.length < 8) {
                e.preventDefault();
                showToast("Password must be at least 8 characters", "error");
                return;
            }
        });
    }

            profileEmail.innerText = profileEmail.querySelector("input").value;
            profileCourse.innerText = profileCourse.querySelector("input").value;
            profileYear.innerText = profileYear.querySelector("input").value;
            editProfileBtn.innerText = "Edit Profile";
        }
        isEditing = !isEditing;
    });

    // Function to change profile image
    changeProfileImage.addEventListener("click", () => {
        uploadProfileImage.click();
    });

    uploadProfileImage.addEventListener("change", (event) => {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = (e) => {
                profileImage.src = e.target.result;
                alert("✅ Profile picture updated successfully!");
            };
            reader.readAsDataURL(file);
        }
    });
});