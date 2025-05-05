<!-- Line Graph for Debt Payment History & Payment Confirmation -->
<div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 text-center mt-8">
    <!-- Title that will update with the current debt -->
    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 border-b pb-2" id="debtTitle">Debt Payment History</h3>
    
    <!-- Chart.js Canvas for the Line Graph -->
    <canvas id="debtLineChart" class="mx-auto pr-8 md:px-0" style="width:350px; height:200px;"></canvas>
    
    <!-- Button to Switch to the Next Debt -->
    <button id="nextDebt" class="mt-4 px-4 py-2 bg-blue-600 text-white rounded-lg">Next Debt</button>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Line Graph for Debt Payment History 
    let debtData = @json($debtChartData);  // JSON array of debt data.
    let transactions = @json($debtTransactions); // JSON array of debt transaction data.
    let currentDebtIndex = 0;
 
    // Check if there are no debts and assign default data if needed
    if (!debtData || debtData.length === 0) {
    debtData = [{
        id: 0,
        name: 'No Debt',
        amount: 0,
        initial_amount: 0,
        monthly_payment: 0
    }];
    }
 
    // Get the canvas context for the debt line chart
    const ctxDebtLine = document.getElementById('debtLineChart').getContext('2d');
     
    // Function to initialize the chart for the current debt
    function initDebtChart() {
        let currentDebt = debtData[currentDebtIndex];
        let initialDebt = parseFloat(currentDebt.initial_amount);
        let currentAmount = parseFloat(currentDebt.amount);

        // Filter transactions for the current debt
        let debtTransactions = transactions.filter(t => t.debt_id == currentDebt.id);
         
        // Set initial labels and data (only the starting point is shown)
        let labels = ["Start"];
        let data = [initialDebt];

        // Add the transaction points
        let balance = initialDebt;
        debtTransactions.forEach((transaction, index) => {
            balance -= parseFloat(transaction.principal_paid || 0);
            labels.push(`P ${index + 1}`);
            data.push(balance >= 0 ? balance : 0);
        });
         
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
                    label: currentDebt.name + ' Payment History',
                    data: data,
                    borderColor: '#36A2EB',
                    fill: false,
                    tension: 0.1,
                    pointRadius: 5,
                    pointHoverRadius: 8
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
                },
                plugins: {
                    tooltip: {
                        enabled: true,
                        mode: 'index',
                        intersect: false,
                        callbacks: {
                            label: function(context) {
                                let index = context.dataIndex;              // index of a point in the chart
                                let balance = context.raw.toFixed(2);       // balance value of the datapoint rounded
                                if (index === 0) return `Initial: $${balance}`;    // No payment at initial balance.
                                let tx = debtTransactions[index - 1];   // Grab the payment that led to the current point's balance
                                return `Payment ${index}: $${balance} (Paid: $${parseFloat(tx.principal_paid || 0).toFixed(2)})`;
                            }
                        }
                    }
                }
            }
        });
         
        // Update the debt title with the current debt name
        document.getElementById('debtTitle').innerText = currentDebt.name + ' Payment History';
    }
     
    // Initialize the chart for the first debt on page load
    initDebtChart();
     
    // Next Debt Button: Cycle through available debts
    document.getElementById('nextDebt').addEventListener('click', function() {
        currentDebtIndex = (currentDebtIndex + 1) % debtData.length;
        initDebtChart();
    });
</script>

