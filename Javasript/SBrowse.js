// browse-opportunities.js

document.addEventListener("DOMContentLoaded", () => {
    console.log("Browse Opportunities Page Loaded");

    const searchInput = document.getElementById("searchOpportunities");
    const opportunitiesList = document.getElementById("opportunitiesList");
    const applyButtons = document.querySelectorAll(".apply-btn");

    // Function to search/filter opportunities
    searchInput.addEventListener("keyup", () => {
        const searchText = searchInput.value.toLowerCase();
        const opportunityCards = opportunitiesList.querySelectorAll(".card");

        opportunityCards.forEach(card => {
            const title = card.querySelector("h5").innerText.toLowerCase();
            const company = card.querySelector("p.text-muted").innerText.toLowerCase();
            if (title.includes(searchText) || company.includes(searchText)) {
                card.parentElement.style.display = "block";
            } else {
                card.parentElement.style.display = "none";
            }
        });
    });

    // Function to handle application submission
    applyButtons.forEach(button => {
        button.addEventListener("click", async (event) => {
            const card = event.target.closest(".card");
            const opportunityTitle = card.querySelector("h5").innerText;
            const companyName = card.querySelector("p.text-muted").innerText;
            const opportunityId = card.dataset.opportunityId;
            
            if (!confirm(`📩 Apply for ${opportunityTitle} at ${companyName}?`)) {
                return;
            }

            // Show loading state
            const originalText = event.target.innerText;
            event.target.innerText = "Applying...";
            event.target.disabled = true;

            try {
                // First save application to our database
                const dbResponse = await fetch("/Attachment/api/application-submit.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({
                        opportunity_id: opportunityId
                    })
                });

                const dbData = await dbResponse.json();

                if (!dbResponse.ok) {
                    throw new Error(dbData.error || "Application failed");
                }

                // Get the opportunity details including external link
                const oppResponse = await fetch(`/Attachment/api/get-opportunity.php?id=${opportunityId}`);
                const opportunity = await oppResponse.json();

                if (opportunity.application_link) {
                    // Redirect to company's application page
                    window.open(opportunity.application_link, '_blank');
                }

                // Update UI on success
                event.target.innerText = "Applied ✔";
                event.target.classList.remove("btn-primary");
                event.target.classList.add("btn-success");
                
                // Show success toast
                showToast(`✅ Application recorded. Redirecting to ${opportunity.company_name}'s site...`, "success");
            } catch (error) {
                // Reset button state
                event.target.innerText = originalText;
                event.target.disabled = false;
                
                // Show error toast
                showToast(`❌ Error: ${error.message}`, "danger");
            }
        });
    });

    // Toast notification function
    function showToast(message, type = "info") {
        const toast = document.createElement("div");
        toast.className = `toast show position-fixed bottom-0 end-0 m-3 bg-${type} text-white`;
        toast.style.zIndex = "9999";
        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">${message}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;
        document.body.appendChild(toast);
        
        // Auto-remove after 5 seconds
        setTimeout(() => {
            toast.classList.remove("show");
            setTimeout(() => toast.remove(), 300);
        }, 5000);
    }
});
