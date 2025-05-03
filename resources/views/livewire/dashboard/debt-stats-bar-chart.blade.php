<!-- Debt Stats Bar Chart-->
<div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-10 text-center" style="height: 350px;">
    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 border-b pb-2">Your Debt Stats</h3>
    <div class="max-w-md mx-auto mt-8 space-y-6">
    <canvas id="debtChart" class="w-full h-full"></canvas>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Debt Chart (Bar Chart)
        const ctxDebt = document.getElementById('debtChart').getContext('2d');
        const debtNames = {!! json_encode($debtChartData->pluck('name')) !!};
        const debtAmounts = {!! json_encode($debtChartData->pluck('amount')) !!};
        const palette = ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40'];

        new Chart(ctxDebt, {
            type: 'bar',
            data: {
                labels: debtNames,
                datasets: [{
                    label: 'Debt Amount ($)',
                    data: debtAmounts,
                    backgroundColor: debtNames.map((_, i) => palette[i % palette.length]),
                    borderColor: debtNames.map((_, i) => palette[i % palette.length]),
                    borderWidth: 1,
                    borderRadius: 8, // Rounded corners for a smoother look
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        onClick: function(e) {
                            // Disable clicking on legend items
                            e.stopPropagation();
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Amount ($)'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Debt Name'
                        }
                    }
                }
            }
        });
    });
</script>