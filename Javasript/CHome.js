// script.js

document.addEventListener("DOMContentLoaded", () => {
    console.log("Company Dashboard Loaded!");

    const dateTimeElement = document.getElementById("dateTime");
    const opportunityList = document.getElementById("opportunityList");
    const opportunityModal = document.getElementById("opportunityModal");
    const opportunityForm = document.getElementById("opportunityForm");

    let editingOpportunity = null;

    // Show current date and time
    function updateDateTime() {
        const now = new Date();
        dateTimeElement.innerText = `Today is ${now.toDateString()}, ${now.toLocaleTimeString()}`;
    }
    setInterval(updateDateTime, 1000);
    updateDateTime();

    // Open and close modal with animation
    window.openModal = function () {
        opportunityModal.style.display = "flex";
        opportunityModal.classList.add("fade-in");
    }

    window.closeModal = function () {
        opportunityModal.classList.add("fade-out");
        setTimeout(() => {
            opportunityModal.style.display = "none";
            opportunityModal.classList.remove("fade-in", "fade-out");
        }, 300);
        resetForm();
    }

    // Reset form and editing state
    function resetForm() {
        opportunityForm.reset();
        editingOpportunity = null;
        document.querySelector(".save-btn").innerText = "Save Opportunity";
    }

    // Form submission with inline validation
    opportunityForm.addEventListener("submit", (event) => {
        event.preventDefault();
        
        const title = document.getElementById("title").value.trim();
        const description = document.getElementById("description").value.trim();
        const slots = document.getElementById("slots").value;
        const deadline = document.getElementById("deadline").value;

        if (!title || !description || slots <= 0 || !deadline) {
            showToast("Please fill out all fields correctly!", "error");
            return;
        }

        const newOpportunity = { title, description, slots, deadline };

        if (editingOpportunity) {
            updateOpportunity(editingOpportunity, newOpportunity);
            showToast("Opportunity updated successfully!", "success");
        } else {
            addOpportunity(newOpportunity);
            showToast("Opportunity created successfully!", "success");
        }

        closeModal();
    });

    // Add new opportunity to the list
    function addOpportunity(opportunity) {
        const opportunityCard = createOpportunityCard(opportunity);
        opportunityList.appendChild(opportunityCard);
    }

    // Update opportunity
    function updateOpportunity(card, updatedData) {
        card.innerHTML = generateOpportunityHTML(updatedData, card);
    }

    // Create opportunity card element
    function createOpportunityCard(opportunity) {
        const card = document.createElement("div");
        card.className = "opportunity-card";
        card.innerHTML = generateOpportunityHTML(opportunity, card);
        return card;
    }

    // Generate HTML for opportunity card
    function generateOpportunityHTML(opportunity, card) {
        return `
            <h4>${opportunity.title}</h4>
            <p>${opportunity.description}</p>
            <p>Slots: ${opportunity.slots}</p>
            <p>Deadline: ${opportunity.deadline}</p>
            <div class="card-actions">
                <button class="edit-btn" onclick="editOpportunity(this)">Edit</button>
                <button class="delete-btn" onclick="deleteOpportunity(this)">Delete</button>
            </div>
        `;
    }

    // Edit opportunity
    window.editOpportunity = function (btn) {
        const card = btn.closest(".opportunity-card");
        
        document.getElementById("title").value = card.querySelector("h4").innerText;
        document.getElementById("description").value = card.querySelector("p").innerText;
        document.getElementById("slots").value = card.querySelector("p:nth-of-type(2)").innerText.replace("Slots: ", "");
        document.getElementById("deadline").value = card.querySelector("p:nth-of-type(3)").innerText.replace("Deadline: ", "");

        editingOpportunity = card;
        document.querySelector(".save-btn").innerText = "Update Opportunity";
        openModal();
    }

    // Delete opportunity
    window.deleteOpportunity = function (btn) {
        const card = btn.closest(".opportunity-card");
        if (confirm("Are you sure you want to delete this opportunity?")) {
            card.remove();
            showToast("Opportunity deleted successfully!", "success");
        }
    }

    // Show feedback toast
    function showToast(message, type) {
        const toast = document.createElement("div");
        toast.className = `toast ${type}`;
        toast.innerText = message;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 3000);
    }
});
