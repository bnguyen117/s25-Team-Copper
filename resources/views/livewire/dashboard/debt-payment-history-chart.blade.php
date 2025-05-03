<!-- Line Graph for Debt Payment History & Payment Confirmation -->
<div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 text-center mt-8">
    <!-- Title that will update with the current debt -->
    <h3 class="text-lg font-se  mibold text-gray-900 dark:text-gray-100 border-b pb-2" id="debtTitle">Debt Payment History</h3>
    
    <!-- Chart.js Canvas for the Line Graph -->
    <canvas id="debtLineChart" class="mx-auto" style="width:350px; height:200px;"></canvas>
    
    <!-- Button to Switch to the Next Debt -->
    <button id="nextDebt" class="mt-4 px-4 py-2 bg-blue-600 text-white rounded-lg">Next Debt</button>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
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
     
    // Next Debt Button: Cycle through available debts
    document.getElementById('nextDebt').addEventListener('click', function() {
        currentDebtIndex = (currentDebtIndex + 1) % rawDebts.length;
        initDebtChart();
    });
</script>

