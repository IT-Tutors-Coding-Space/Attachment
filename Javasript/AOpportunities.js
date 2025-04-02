// COpportunities.js
document.addEventListener('DOMContentLoaded', function() {
    const opportunityForm = document.getElementById('opportunityForm');
    const opportunityTable = document.getElementById('opportunityTable');
    let currentOpportunityId = null;

    function fetchOpportunities() {
        fetch('COpportunitiess.php?action=getOpportunities')
            .then(response => response.json())
            .then(data => {
                if (Array.isArray(data)) {
                    opportunityTable.innerHTML = '';
                    data.forEach(opportunity => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td>${opportunity.title}</td>
                            <td>${opportunity.application_deadline}</td>
                            <td>${opportunity.available_slots}</td>
                            <td>${opportunity.status}</td>
                            <td>
                                <button class="btn btn-sm btn-primary view-btn" data-id="${opportunity.opportunity_id}">View</button>
                                <button class="btn btn-sm btn-secondary copy-btn" data-id="${opportunity.opportunity_id}">Copy</button>
                                <button class="btn btn-sm btn-danger delete-btn" data-id="${opportunity.opportunity_id}">Delete</button>
                            </td>
                            <td>${opportunity.created_at}</td>
                            <td>${opportunity.updated_at}</td>
                        `;
                        opportunityTable.appendChild(row);
                    });
                    addDeleteEventListeners();
                    addViewEventListeners();
                    addCopyEventListeners();
                } else {
                    console.error('Data is not an array:', data);
                    alert('Error fetching opportunities. Please check the console.');
                }
            })
            .catch(error => {
                console.error('Error fetching opportunities:', error);
                alert('An unexpected error occurred. Please check the console.');
            });
    }

    function addDeleteEventListeners() {
        const deleteButtons = document.querySelectorAll('.delete-btn');
        deleteButtons.forEach(button => {
            button.addEventListener('click', function() {
                const opportunityId = this.getAttribute('data-id');
                if (confirm('Are you sure you want to delete this opportunity?')) {
                    const formData = new FormData();
                    formData.append('action', 'deleteOpportunity');
                    formData.append('opportunity_id', opportunityId);

                    fetch('COpportunitiess.php', {
                        method: 'POST',
                        body: formData,
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Opportunity deleted successfully!');
                            fetchOpportunities();
                        } else {
                            alert('Error: ' + data.message);
                            console.error('Server-side error:', data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error deleting opportunity:', error);
                        alert('An unexpected error occurred. Please check the console.');
                    });
                }
            });
        });
    }

    function addViewEventListeners() {
        const viewButtons = document.querySelectorAll('.view-btn');
        viewButtons.forEach(button => {
            button.addEventListener('click', function() {
                const opportunityId = this.getAttribute('data-id');
                currentOpportunityId = opportunityId;
                fetch('COpportunitiess.php?action=getOpportunities')
                    .then(response => response.json())
                    .then(data => {
                        const opportunity = data.find(o => o.opportunity_id === parseInt(opportunityId));
                        if (opportunity) {
                            document.getElementById('title').value = opportunity.title;
                            document.getElementById('deadline').value = opportunity.application_deadline;
                            document.getElementById('description').value = opportunity.description;
                            document.getElementById('positions').value = opportunity.available_slots;
                            $('#createOpportunityModal').modal('show');
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching opportunity details:', error);
                        alert('An unexpected error occurred. Please check the console.');
                    });
            });
        });
    }

    function addCopyEventListeners() {
        const copyButtons = document.querySelectorAll('.copy-btn');
        copyButtons.forEach(button => {
            button.addEventListener('click', function() {
                const opportunityId = this.getAttribute('data-id');
                fetch('COpportunitiess.php?action=getOpportunities')
                    .then(response => response.json())
                    .then(data => {
                        const opportunity = data.find(o => o.opportunity_id === parseInt(opportunityId));
                        if (opportunity) {
                            navigator.clipboard.writeText(JSON.stringify(opportunity)).then(() => {
                                alert('Opportunity data copied to clipboard!');
                            }).catch(err => {
                                console.error('Could not copy text: ', err);
                                alert('Could not copy opportunity data.');
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching opportunity details:', error);
                        alert('An unexpected error occurred. Please check the console.');
                    });
            });
        });
    }

    opportunityForm.addEventListener('submit', function(event) {
        event.preventDefault();

        const formData = new FormData(opportunityForm);
        console.log('Form Data:', formData);

        if (currentOpportunityId) {
            formData.append('action', 'updateOpportunity');
            formData.append('opportunity_id', currentOpportunityId);
        } else {
            formData.append('action', 'createOpportunity');
        }

        fetch('COpportunitiess.php', {
            method: 'POST',
            body: formData,
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                let message = currentOpportunityId ? 'Opportunity "' + document.getElementById('title').value + '" updated successfully!' : 'Opportunity created successfully!';
                alert(message);
                $('#createOpportunityModal').modal('hide');
                opportunityForm.reset();
                fetchOpportunities();
                currentOpportunityId = null;
            } else {
                alert('Error: ' + data.message);
                console.error('Server-side error:', data.message);
            }
        })
        .catch(error => {
            console.error('Fetch error:', error);
            alert('An unexpected error occurred. Please check the console.');
        });
    });

    fetchOpportunities();
});