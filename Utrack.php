<script>
    function loadApplications() {
        fetch('track.php?action=fetch')
            .then(response => response.json())
            .then(data => {
                let table = document.getElementById('applicationsTable');
                table.innerHTML = "";

                if (data.length === 0) {
                    table.innerHTML = "<tr><td colspan='3' class='text-center text-muted'>No applications found</td></tr>";
                    return;
                }

                data.forEach(app => {
                    table.innerHTML += `
                        <tr>
                            <td>${app.student_name}</td>
                            <td><span class="badge bg-${app.status === 'Accepted' ? 'success' : app.status === 'Rejected' ? 'danger' : 'warning'}">${app.status}</span></td>
                            <td>
                                <select onchange="updateStatus(${app.id}, this.value)" class="form-select">
                                    <option value="Pending" ${app.status === "Pending" ? "selected" : ""}>Pending</option>
                                    <option value="Accepted" ${app.status === "Accepted" ? "selected" : ""}>Accepted</option>
                                    <option value="Rejected" ${app.status === "Rejected" ? "selected" : ""}>Rejected</option>
                                </select>
                            </td>
                        </tr>
                    `;
                });
            })
            .catch(error => console.error('Error loading applications:', error));
    }

    function updateStatus(id, status) {
        fetch('track.php?action=update_status', {
            method: 'POST',
            body: new URLSearchParams({ 'id': id, 'status': status })
        }).then(() => loadApplications());
    }

    window.onload = loadApplications;
</script>
