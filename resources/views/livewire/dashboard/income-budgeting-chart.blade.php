<!-- Income and Budgeting -->
<div class="w-full md:w-1/2 bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 text-center">
    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 border-b pb-2">Income and Budgeting</h3>
    <canvas id="incomeChart" class="mx-auto mt-4" width="150" height="150"></canvas>

    <!-- Compact Legend -->
    <div id="incomeLegend" class="flex justify-center mt-2 space-x-2 text-xs text-gray-600 dark:text-gray-300">
        <div class="flex items-center">
            <span class="w-3 h-3 bg-red-400 inline-block rounded-full mr-1"></span>
            This Month
        </div>
        <div class="flex items-center">
            <span class="w-3 h-3 bg-green-300 inline-block rounded-full mr-1"></span>
            Available Funds
        </div>
    </div>
</div>

<!-- Chart.js Script for budget donut chart -->
 <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
 <script>
    document.addEventListener('DOMContentLoaded', function () {
        //Income Chart (Donut Chart)
        const ctxIncome = document.getElementById('incomeChart').getContext('2d');

        //Following data is being called from the dashboard controller pulling from the debt table and budget table
        const income = {{ $debt2  ?? 0 }}; //debt2 being called from the controller
        const budget = {{ $budget ?? 5000 }}; //The user's budgeted income
        const remaining = {{ $remaining_balance }}; // Pulls from the remaining balance value of the current user's budget
        //Making the text green or red if they are over or under budget
        const underBudget = remaining >= 0 ? `${remaining.toLocaleString()} under`: `${Math.abs(remaining).toLocaleString()} over`;
        // If it is negative, the circle will be all red
        const chartData = remaining >= 0 ? [income, remaining] : [income, 0]; 

        new Chart(ctxIncome, {
            type: 'doughnut',
            data: {
                labels: ["This Month", "Available Funds"],
                datasets: [{
                    data: chartData, 
                    backgroundColor: ['#FF4747', '#4CAF50'],
                    borderWidth: 3
                }]
            },
            options: {
                cutout: '70%',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        enabled: true,
                        callbacks: {
                            label: function(tooltipItem) {
                                let value = tooltipItem.raw.toLocaleString();
                                let label = tooltipItem.label;
                                return `${label}: ${value}`;
                            }
                        }
                    }
                }
            },
            plugins: [{
                id: 'centerText',
                beforeDraw: function(chart) {
                    let width = chart.width,
                        height = chart.height,
                        ctx = chart.ctx;

                    ctx.save();
                    let fontSize = (height / 10).toFixed(2);
                    ctx.font = `bold ${fontSize}px sans-serif`;
                    ctx.textBaseline = "middle";
                    ctx.textAlign = "center";

                    let text1 = `$${remaining.toLocaleString()}`;
                    ctx.fillStyle = "#ffffff";
                    ctx.fillText(text1, width / 2, height / 2 - 15);

                    ctx.font = `normal ${(height / 16).toFixed(2)}px sans-serif`;
                    let text2 = `Remaining of $${budget.toLocaleString()}`;
                    ctx.fillStyle = "#cccccc";
                    ctx.fillText(text2, width / 2, height / 2 + 5);

                    ctx.font = `normal ${(height / 18).toFixed(2)}px sans-serif`;
                    let text3 =  'UnderBudget';
                    ctx.fillStyle = remaining > 0 ? "#4CAF50" : "#FF4747";
                    ctx.fillText(text3, width / 2, height / 2 + 25);

                    ctx.restore();
                }
            }]
        });
    });
 </script>