<!-- Debt Breakdown Chart -->
<div class="w-1/2 md:w-1/2 bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 text-center" style="height:280px;">
    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 border-b pb-2">Debt Breakdown</h3>
    <canvas id="debtBreakdownChart" class="mx-auto" style="width:270px; height:270px;"></canvas>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Debt Breakdown Chart (Doughnut Chart with Interactive Legend)
        const ctxDebtBreakdown = document.getElementById('debtBreakdownChart').getContext('2d');
        // Aggregated data for the debt breakdown chart
        const categories = {!! json_encode((isset($categories) && count($categories) > 0) ? $categories : ['No Data']) !!};
        const debtAmounts = {!! json_encode((isset($debtAmounts) && count($debtAmounts) > 0) ? $debtAmounts : [1]) !!};

        // Calculate total debt from the aggregated amounts
        const totalDebt = debtAmounts.reduce((acc, debt) => acc + debt, 0);
        const debtPercentages = debtAmounts.map(amount => ((amount / totalDebt) * 100).toFixed(2));


        const debtBreakdownChart = new Chart(ctxDebtBreakdown, {
            type: 'doughnut',
            data: {
                labels: categories,
                datasets: [{
                    data: debtAmounts,
                    backgroundColor:  ['#FF4747', '#36A2EB', '#FF7043', '#9C27B0', '#FFEB3B', '#8BC34A', '#FFC107'],
                    borderWidth: 3
                }]
            },
            options: {
                cutout: '70%',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'right',
                        labels: {
                            usePointStyle: true,
                            pointStyle: 'circle',
                                font:{
                                    size: 14,}
                        },
                        onClick: (e, legendItem, legend) => {
                            const index = legendItem.index;
                            const chart = legend.chart;
                            chart.toggleDataVisibility(index);
                            chart.update();
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function (tooltipItem) {
                                let value = tooltipItem.raw.toLocaleString();
                                let label = tooltipItem.label;
                                let percentage = debtPercentages[tooltipItem.dataIndex];
                                return `${label}: $${value} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
    });
</script>