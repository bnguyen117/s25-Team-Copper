<div class="mt-8">
    <h3 class="text-xl font-bold text-gray-800 dark:text-gray-200 text-center">Budget Overview</h3>

    <div class="flex justify-center">
        <canvas id="budgetChart" width="400" height="400"></canvas>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var ctx = document.getElementById('budgetChart').getContext('2d');
            var chart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Income', 'Expenses', 'Savings', 'Remaining Balance'],
                    datasets: [{
                        label: 'Amount ($)',
                        data: [
                            {{ $income }},
                            {{ $expenses }},
                            {{ $savings }},
                            {{ $remaining_balance }}
                        ],
                        backgroundColor: ['#36a2eb', '#ff6384', '#ffcd56', '#4bc0c0'],
                        borderColor: ['#2b7bb9', '#d62828', '#e3b505', '#1d7874'],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: { beginAtZero: true }
                    },
                    plugins: {
                        legend: { display: true, position: 'top' }
                    }
                }
            });
        });
    </script>
</div>
