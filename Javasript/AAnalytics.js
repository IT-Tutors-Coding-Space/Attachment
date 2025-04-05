document.addEventListener('DOMContentLoaded', function() {
    // Update summary cards
    document.getElementById('totalApplications').textContent = analyticsData.totalApplications;
    document.getElementById('acceptedApplications').textContent = analyticsData.acceptedApplications;
    document.getElementById('rejectedApplications').textContent = analyticsData.rejectedApplications;
    document.getElementById('activeCompanies').textContent = analyticsData.activeCompanies;

    // Applications per Company Chart
    const companyNames = analyticsData.applicationsPerCompany.map(item => item.company_name);
    const applicationCounts = analyticsData.applicationsPerCompany.map(item => item.application_count);
    
    const companyChartCtx = document.getElementById('applicationsChart').getContext('2d');
    new Chart(companyChartCtx, {
        type: 'bar',
        data: {
            labels: companyNames,
            datasets: [{
                label: 'Applications',
                data: applicationCounts,
                backgroundColor: 'rgba(54, 162, 235, 0.7)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Number of Applications'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Companies'
                    }
                }
            }
        }
    });

    // Application Status Chart
    const statusLabels = analyticsData.statusDistribution.map(item => item.status);
    const statusCounts = analyticsData.statusDistribution.map(item => item.count);
    
    const statusChartCtx = document.getElementById('statusChart').getContext('2d');
    new Chart(statusChartCtx, {
        type: 'pie',
        data: {
            labels: statusLabels,
            datasets: [{
                data: statusCounts,
                backgroundColor: [
                    'rgba(75, 192, 192, 0.7)', // Accepted
                    'rgba(255, 99, 132, 0.7)',  // Rejected
                    'rgba(255, 205, 86, 0.7)'   // Pending
                ],
                borderColor: [
                    'rgba(75, 192, 192, 1)',
                    'rgba(255, 99, 132, 1)',
                    'rgba(255, 205, 86, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom',
                },
                title: {
                    display: true,
                    text: 'Application Status Distribution'
                }
            }
        }
    });
});
