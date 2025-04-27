<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight text-center">
            {{ Auth::user()->name }}'s Dashboard
        </h2>
    </x-slot>

    <div class="max-w-4xl mx-auto mt-8 space-y-6">
        <!-- Income and Budgeting & Debt Breakdown -->
        <div class="flex flex-col md:flex-row justify-between space-y-4 md:space-y-0 md:space-x-4">
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
            
            <!-- Debt Breakdown Chart -->
            <div class="w-full md:w-1/2 bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 text-center" style="height:280px;">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 border-b pb-2">Debt Breakdown</h3>
                <canvas id="debtBreakdownChart" class="mx-auto" style="width:270px; height:270px;"></canvas>
            </div>
        </div>

        
        <!-- Debt Stats -->
        <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-10 text-center" style="height: 350px;">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 border-b pb-2">Your Debt Stats</h3>
            <div class="max-w-md mx-auto mt-8 space-y-6">
        <canvas id="debtChart" class="w-full h-full"></canvas>
        </div>
    </div>

        <!-- Rewards & Inspiration and Goal Progress Side by Side -->
        <div class="flex flex-col md:flex-row justify-between space-y-4 md:space-y-0 md:space-x-4">
            <!-- Rewards & Inspiration -->
            <div class="w-full md:w-1/2 bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 text-center">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 border-b pb-2">Rewards & Inspiration</h3>
                <p class="mt-4 text-lg font-semibold text-gray-400">
                    You got <span class="text-green-400">{{ auth()->user()->badges()->count() }}</span> badge{{ auth()->user()->badges()->count() !== 1 ? 's' : '' }}
                </p>

                <a href="{{ route('rewards') }}" class="mt-3 px-4 py-2 bg-blue-600 text-white rounded-lg block text-center">View Rewards</a>
                
                <!-- Inspirational Quote -->
                <div x-data="{ quotes: [
                    'Believe in yourself and all that you are.',
                    'Keep going! Your hardest times often lead to the greatest moments of your life.',
                    'Hardships often prepare ordinary people for an extraordinary destiny.',
                    'Success is the sum of small efforts, repeated day in and day out.',
                    'Do what you can, with what you have, where you are.',
                    'Your limitation—it’s only your imagination.'
                ], quote: '',
                updateQuote() {
                    this.quote = this.quotes[Math.floor(Math.random() * this.quotes.length)];
                }}"
                x-init="updateQuote(); setInterval(() => updateQuote(), 50000)"
                class="mt-4 italic text-gray-600 dark:text-gray-300 text-center">
                    <span x-text="quote"></span>
                </div>
            </div>

            <!-- Your Goal Progress -->
            <div class="w-full md:w-1/2 bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 text-center">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 border-b pb-2">Your Goal Progress</h3>
            
                <!-- Display Goal Name Above the Slider -->
                <h4 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mt-4 mb-2">{{ $goalName }} Goal Progress<!-- Display goal name dynamically -->
                </h4>

                <!-- Slider with Dynamic Percentage Display -->
                <div x-data="{ progress: {{ $goalProgress }} }" class="mt-4 flex flex-col items-center w-full">
                    <div class="relative w-1/2">
                    <div class="absolute w-full h-3 rounded-lg bg-gray-300"></div>
                    <div class="absolute h-3 rounded-lg" 
                        :class="progress <= 30 ? 'bg-red-600' : (progress <= 70 ? 'bg-yellow-500' : 'bg-green-600')" 
                        :style="'width: ' + progress + '%'"></div>

                <!-- Read-only Slider -->
                <input type="range" min="0" max="100" x-model="progress"
                    class="relative w-full h-3 bg-transparent appearance-none cursor-not-allowed" disabled>
                </div>
            <span class="mt-2 font-medium" 
                :class="progress <= 30 ? 'text-red-600' : (progress <= 70 ? 'text-yellow-500' : 'text-green-600')">
            <span x-text="progress"></span>%
        </span>
    </div>

                <a href="{{ route('finance') }}" 
                class="mt-3 px-4 py-2 bg-blue-600 text-white rounded-lg block text-center">Edit Goals</a> 
                </div>
            </div>
            <!-- Line Graph for Debt Payment History & Payment Confirmation -->
            <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 text-center mt-8">
            <!-- Title that will update with the current debt -->
            <h3 class="text-lg font-se  mibold text-gray-900 dark:text-gray-100 border-b pb-2" id="debtTitle">Debt Payment History</h3>
            
            <!-- Chart.js Canvas for the Line Graph -->
            <canvas id="debtLineChart" class="mx-auto max-w-full" style="width:350px; height:200px;"></canvas>
            
            <!-- Button to Switch to the Next Debt -->
            <button id="nextDebt" class="mt-4 px-4 py-2 bg-blue-600 text-white rounded-lg">Next Debt</button>
            
            <!-- Payment Confirmation Question and Yes/No Buttons -->
            <div class="mt-6">
                <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">Have you made your payment this month?</p>
                <div class="flex justify-center mt-4 space-x-4">
                    <button id="paymentYes" class="px-4 py-2 bg-green-600 text-white rounded-lg">Yes</button>
                    <button id="paymentNo" class="px-4 py-2 bg-red-600 text-white rounded-lg">No</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Income Chart (Doughnut Chart)
            const ctxIncome = document.getElementById('incomeChart').getContext('2d');

            //Following Const being called from the dashboard controller pulling from the debt table
            const income = {{ $debt2  ?? 0 }}; //debt2 being called from the controller
            const budget = {{ $user->budget ?? 8000 }}; //Pulling from the User Profile budget
            const remaining = budget - income; //Formula for the remaining amount
            const underBudget = remaining >= 0 ? `${remaining.toLocaleString()} under` : `${Math.abs(remaining).toLocaleString()} over`; //Making the text green or red if they are over or under budget
            const chartData = remaining >= 0 ? [income, remaining] : [income, 0]; // If it is negative, the circle will be all red

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
// Line Graph for Debt Payment History 
    let rawDebts = {!! json_encode($debts) !!};
    let currentDebtIndex = 0;

    // Check if there are no debts and assign default data if needed
    if (!rawDebts || rawDebts.length === 0) {
    rawDebts = [{
        debt_name: 'No Debt',
        amount: 0,
        monthly_payment: 0
    }];
    }

    // Get the canvas context for the debt line chart
    const ctxDebtLine = document.getElementById('debtLineChart').getContext('2d');
    
    // Function to initialize the chart for the current debt
    function initDebtChart() {
        let currentDebt = rawDebts[currentDebtIndex];
        // Parse numeric values (ensure amount and monthly_payment are numbers)
        let totalDebt = parseFloat(currentDebt.amount);
        let monthlyPayment = parseFloat(currentDebt.monthly_payment);
        
        // Set initial labels and data (only the starting point is shown)
        let labels = ["Start"];
        let data = [totalDebt];
        
        // Destroy the existing chart if it exists
        if (window.lineChart) {
            window.lineChart.destroy();
        }
        
        // Create a new chart instance
        window.lineChart = new Chart(ctxDebtLine, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: currentDebt.debt_name + ' Payment History',
                    data: data,
                    borderColor: '#36A2EB',
                    fill: false,
                    tension: 0.1
                }]
            },
            options: {
                responsive: false,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: { display: true, text: 'Remaining Debt ($)' }
                    },
                    x: {
                        title: { display: true, text: 'Payment Cycle' }
                    }
                }
            }
        });
        
        // Reset the cycle counter and store the monthly payment value globally for updates
        window.currentCycle = 0;
        window.currentMonthlyPayment = monthlyPayment;
        // Update the debt title with the current debt name
        document.getElementById('debtTitle').innerText = currentDebt.debt_name + ' Payment History';
    }
    
    // Initialize the chart for the first debt on page load
    initDebtChart();
    
    // Payment Confirmation Buttons Event Listeners
    document.getElementById('paymentYes').addEventListener('click', function() {
        let chart = window.lineChart;
        let currentData = chart.data.datasets[0].data;
        let currentLabels = chart.data.labels;
        let lastAmount = currentData[currentData.length - 1];

        if (lastAmount === 0) {
        return;
        }
        // Subtract monthly payment if payment is made
        let newAmount = lastAmount - window.currentMonthlyPayment;
        newAmount = newAmount < 0 ? 0 : newAmount;
        window.currentCycle++;
        currentData.push(newAmount);
        currentLabels.push("Month " + window.currentCycle);
        chart.update();

        // If debt reaches 0, update the title to congratulate the user
        if (newAmount === 0) {
            alert("Congrats, you have finished this debt!");
            }
    });
    
    document.getElementById('paymentNo').addEventListener('click', function() {
        let chart = window.lineChart;
        let currentData = chart.data.datasets[0].data;
        let currentLabels = chart.data.labels;
        // If payment is not made, retain the same debt amount
        let lastAmount = currentData[currentData.length - 1];
        window.currentCycle++;
        currentData.push(lastAmount);
        currentLabels.push("Month " + window.currentCycle);
        chart.update();
        
        if (lastAmount === 0) {
            alert("Congrats, you have finished this debt!");
        }
    });
    
    // Next Debt Button: Cycle through available debts
    document.getElementById('nextDebt').addEventListener('click', function() {
        currentDebtIndex = (currentDebtIndex + 1) % rawDebts.length;
        initDebtChart();
    });

    </script>
    
</x-app-layout>
