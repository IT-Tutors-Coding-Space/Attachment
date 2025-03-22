// student-profile.js

document.addEventListener("DOMContentLoaded", () => {
    console.log("Student Profile Page Loaded");

    const editProfileBtn = document.getElementById("editProfileBtn");
    const profileName = document.getElementById("profileName");
    const profileEmail = document.getElementById("profileEmail");
    const profileCourse = document.getElementById("profileCourse");
    const profileYear = document.getElementById("profileYear");
    const profileImage = document.getElementById("profileImage");
    const uploadProfileImage = document.getElementById("uploadProfileImage");
    const changeProfileImage = document.getElementById("changeProfileImage");

    let isEditing = false;

    // Function to toggle edit mode
    editProfileBtn.addEventListener("click", () => {
        if (!isEditing) {
            profileName.innerHTML = `<input type='text' class='form-control' value='${profileName.innerText}'>`;
            profileEmail.innerHTML = `<input type='email' class='form-control' value='${profileEmail.innerText}'>`;
            profileCourse.innerHTML = `<input type='text' class='form-control' value='${profileCourse.innerText}'>`;
            profileYear.innerHTML = `<input type='text' class='form-control' value='${profileYear.innerText}'>`;
            editProfileBtn.innerText = "Save Profile";
        } else {
            profileName.innerText = profileName.querySelector("input").value;
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