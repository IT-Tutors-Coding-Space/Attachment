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
        button.addEventListener("click", (event) => {
            const card = event.target.closest(".card");
            const opportunityTitle = card.querySelector("h5").innerText;
            const companyName = card.querySelector("p.text-muted").innerText;
            
            if (confirm(`📩 Apply for ${opportunityTitle} at ${companyName}?`)) {
                alert("✅ Application submitted successfully!");
                event.target.innerText = "Applied ✔";
                event.target.classList.add("btn-success");
                event.target.disabled = true;
            }
        });
    });
});
