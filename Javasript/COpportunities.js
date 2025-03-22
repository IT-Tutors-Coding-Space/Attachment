document.addEventListener("DOMContentLoaded", function () {
    const opportunityForm = document.getElementById("opportunityForm");
    const opportunityTable = document.getElementById("opportunityTable");
    
    let opportunities = [];
    
    // Function to render opportunities
    function renderOpportunities() {
        opportunityTable.innerHTML = "";
        opportunities.forEach((opportunity, index) => {
            const row = document.createElement("tr");
            row.innerHTML = `
                <td>${opportunity.title}</td>
                <td>${opportunity.deadline}</td>
                <td>${opportunity.positions}</td>
                <td><span class="badge bg-success">Active</span></td>
                <td>
                    <button class="btn btn-warning btn-sm edit-btn" data-index="${index}"><i class="fa fa-edit"></i></button>
                    <button class="btn btn-danger btn-sm delete-btn" data-index="${index}"><i class="fa fa-trash"></i></button>
                </td>
            `;
            opportunityTable.appendChild(row);
        });
    }
    
    // Handle form submission
    opportunityForm.addEventListener("submit", function (event) {
        event.preventDefault();
        
        const title = document.getElementById("title").value;
        const deadline = document.getElementById("deadline").value;
        const description = document.getElementById("description").value;
        const positions = document.getElementById("positions").value;
        
        if (!title || !deadline || !description || !positions) {
            alert("Please fill all fields");
            return;
        }
        
        const newOpportunity = { title, deadline, description, positions };
        opportunities.push(newOpportunity);
        
        renderOpportunities();
        
        // Clear form fields
        opportunityForm.reset();
        
        // Close modal
        let modal = bootstrap.Modal.getInstance(document.getElementById("createOpportunityModal"));
        modal.hide();
    });
    
    // Handle delete action
    opportunityTable.addEventListener("click", function (event) {
        if (event.target.closest(".delete-btn")) {
            const index = event.target.closest(".delete-btn").getAttribute("data-index");
            opportunities.splice(index, 1);
            renderOpportunities();
        }
    });
    
    // Handle edit action
    opportunityTable.addEventListener("click", function (event) {
        if (event.target.closest(".edit-btn")) {
            const index = event.target.closest(".edit-btn").getAttribute("data-index");
            const opportunity = opportunities[index];
            
            document.getElementById("title").value = opportunity.title;
            document.getElementById("deadline").value = opportunity.deadline;
            document.getElementById("description").value = opportunity.description;
            document.getElementById("positions").value = opportunity.positions;
            
            // Remove old opportunity and re-add it after editing
            opportunities.splice(index, 1);
            renderOpportunities();
        }
    });
});
