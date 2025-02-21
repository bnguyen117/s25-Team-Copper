<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight text-center">
            {{ Auth::user()->name }}'s Dashboard
        </h2>
    </x-slot>

    <div class="max-w-4xl mx-auto mt-8 space-y-6">
        <!-- Income and Budgeting & Rewards -->
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
                    <span class="w-3 h-3 bg-gray-300 inline-block rounded-full mr-1"></span>
                    Remaining Budget
                </div>
                <div class="flex items-center">
                    <span class="w-3 h-3 bg-orange-400 inline-block rounded-full mr-1"></span>
                    Last Month
                </div>
            </div>
        </div>
        
            <!-- Rewards Section -->
            <div class="w-1/2 bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 text-center">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 border-b pb-2">Rewards</h3>
                <p class="mt-2 text-gray-500 dark:text-gray-300">You got <span class="text-green-500">[insert num]</span> badges</p>
                <a href="{{ url('http://s25-team-copper.test/rewards') }}" 
                   class="mt-3 px-4 py-2 bg-blue-600 text-white rounded-lg block text-center">View Rewards</a>

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
        }
    }"
    x-init="updateQuote(); setInterval(() => updateQuote(), 50000)"
                class="mt-4 italic text-gray-600 dark:text-gray-300 text-center">
                <span x-text="quote"></span>
            </div>
        </div>
    </div>

        <!-- Your Goal Progress -->
        <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 text-center">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 border-b pb-2">Your Goal Progress</h3>
           
            <!-- Slider with Number Display -->
            <div x-data="{ progress: 50 }" class="mt-4 flex flex-col items-center w-full">
                <div class="relative w-1/2">

            <!-- Track Background -->
            <div class="absolute w-full h-3 rounded-lg bg-gray-300"></div>

            <!-- Dynamic Progress Bar -->
            <div class="absolute h-3 rounded-lg bg-blue-600" :style="'width: ' + progress + '%'"></div>

            <!-- Input Slider -->
            <input type="range" min="0" max="100" x-model="progress"
                class="relative w-full h-3 bg-transparent appearance-none cursor-pointer">
        </div>
            <span class="mt-2 text-blue-600 font-medium" x-text="progress + '%'"></span>
        </div>


            <!-- Button Creation -->
            <a href="{{ url('http://s25-team-copper.test/finance') }}" 
               class="mt-3 px-4 py-2 bg-blue-600 text-white rounded-lg block text-center">Edit Goals</a>
        </div>

        <!-- Debt Stats -->
        <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 text-center">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 border-b pb-2">Your Debt Stats</h3>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100"> <canvas id="debtChart" class="mx-auto mt-4"></canvas></h3>
        </div>
    </div>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Income Chart (Doughnut Chart)
            const ctxIncome = document.getElementById('incomeChart').getContext('2d');

            const income = {{ $income ?? 5812.37 }};
            const budget = {{ $budget ?? 8000 }};
            const lastMonth = {{ $lastMonth ?? 6000 }};
            const remaining = budget - income;
            const underBudget = remaining > 0 ? `$${remaining.toLocaleString()} under` : `$${Math.abs(remaining).toLocaleString()} over`;

            new Chart(ctxIncome, {
                type: 'doughnut',
                data: {
                    labels: ["This Month", "Remaining Budget", "Last Month"],
                    datasets: [{
                        data: [income, remaining, lastMonth], 
                        backgroundColor: ['#FF6384', '#D3D3D3', '#FFA500'],
                        borderWidth: 3
                    }]
                },
                options: {
                    cutout: '70%',
                    plugins: {
                        legend: { display: false },
                        tooltip: { enabled: false }
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

                        let text1 = `$${income.toLocaleString()}`;
                        ctx.fillStyle = "#ffffff";
                        ctx.fillText(text1, width / 2, height / 2 - 15);

                        ctx.font = `normal ${(height / 16).toFixed(2)}px sans-serif`;
                        let text2 = `of $${budget.toLocaleString()}`;
                        ctx.fillStyle = "#cccccc";
                        ctx.fillText(text2, width / 2, height / 2 + 5);

                        ctx.font = `normal ${(height / 18).toFixed(2)}px sans-serif`;
                        let text3 =  ' ${underBudget}';
                        ctx.fillStyle = "#FF4747";
                        ctx.fillText(text3, width / 2, height / 2 + 25);

                        ctx.restore();
                    }
                }]
            });

            // Debt Chart (Bar Chart)
            const ctxDebt = document.getElementById('debtChart').getContext('2d');
            new Chart(ctxDebt, {
                type: 'bar',
                data: {
                    labels: ["January", "February", "March", "April", "May"],
                    datasets: [{
                        label: 'Debt Amount ($)',
                        data: [1000, 1200, 900, 1100, 950],
                        backgroundColor: '#ff6384',
                    }]
                }
            });
        });
    </script>

</x-app-layout>
