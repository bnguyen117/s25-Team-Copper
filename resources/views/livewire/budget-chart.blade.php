<div class="mt-8">
    <h3 class="text-xl font-bold text-gray-800 dark:text-gray-200 text-center">Budget Overview</h3>

    <div class="p-6 flex justify-center">
        <canvas id="budgetChart" width="400" height="400"></canvas>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var ctx = document.getElementById('budgetChart').getContext('2d');
            var chart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Income', 'Needs', 'Wants', 'Savings'],
                    datasets: [{
                        label: 'Amount ($)',
                        data: [
                            {{ $income }},
                            {{ $needs }},
                            {{ $wants }},
                            {{ $savings }},
                            // {{ $remaining_balance }}
                        ],
                        backgroundColor: ['#00d8ff', '#00bfff', '#00a5ff', '#0081ff'],
                        borderColor: ['#00c8ef', '#00afef', '#0095ef', '#0071ef'],
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
