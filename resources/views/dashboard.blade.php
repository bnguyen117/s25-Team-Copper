<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight text-center">
            {{ Auth::user()->name }}'s Dashboard
        </h2>
    </x-slot>

    <div class="max-w-4xl mx-auto mt-8 space-y-6">
        <!-- Income and Budgeting & Debt Breakdown -->
        <div class="flex justify-between space-x-4">
            <!-- Income and Budgeting -->
            <div class="w-1/2 bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 text-center">
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
            
            <!-- New Donut Chart -->
            <div class="w-1/2 bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 text-center">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 border-b pb-2">Debt Breakdown</h3>
                <canvas id="debtBreakdownChart" class="mx-auto mt-4"></canvas>
            </div>
        </div>

        <!-- Debt Stats -->
        <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 text-center">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 border-b pb-2">Your Debt Stats</h3>
            <canvas id="debtChart" class="mx-auto mt-4"></canvas>
        </div>

        <!-- Rewards & Inspiration and Goal Progress Side by Side -->
        <div class="flex justify-between space-x-4">
            <!-- Rewards & Inspiration -->
            <div class="w-1/2 bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 text-center">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 border-b pb-2">Rewards & Inspiration</h3>
                <p class="mt-4 text-lg font-semibold text-gray-400">You got <span class="text-green-400">[insert num]</span> badges</p>
                <a href="{{ url('http://s25-team-copper.test/rewards') }}" class="mt-3 px-4 py-2 bg-blue-600 text-white rounded-lg block text-center">View Rewards</a>
                
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
            <div class="w-1/2 bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 text-center">
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

                <a href="{{ url('http://s25-team-copper.test/finance') }}" 
                   class="mt-3 px-4 py-2 bg-blue-600 text-white rounded-lg block text-center">Edit Goals</a> 
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
                                    return `${label}: $${value}`;
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

            new Chart(ctxDebt, {
                type: 'bar',
                data: {
                    labels: debtNames,
                    datasets: [{
                        label: 'Debt Amount ($)',
                        data: debtAmounts,
                        backgroundColor: '#ff6384',
                    }]
                },
                options: {
                    responsive: true,
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
    
</x-app-layout>
