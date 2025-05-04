

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
    
    debts = {!! json_encode($debts) !!}; // Array of debts
    debtTransactions = {!! json_encode($debtTransactions) !!}; // Array of debt transactions
    currentDebtIndex = 0; // Index to track the current debt
    const ctxDebtLine = document.getElementById('debtLineChart').getContext('2d');

    // Function to update the chart with the current debt's payment history
    function updateDebtChart() {
        const currentDebt = debts[currentDebtIndex];
        const transactions = debtTransactions.filter(transaction => transaction.debt_id === currentDebt.id);

        const dates = transactions.map(transaction => new Date(transaction.created_at).toLocaleDateString());
        const principal_paid = transactions.map(transaction => transaction.principal_paid);

        const newAmounts = currentDebt.amounts - principal_paid[currentDebtIndex];

        const ctxDebtLine = document.getElementById('debtLineChart').getContext('2d');

        // Destroy the previous chart instance if it exists
        if (window.debtLineChart) {
            window.debtLineChart.destroy();
        }

        // Create a new chart instance
        window.debtLineChart = new Chart(ctxDebtLine, {
            type: 'line',
            data: {
                labels: dates,
                datasets: [{
                    label: 'Principal Paid ($)',
                    data: principal_paid,
                    backgroundColor: '#4CAF50',
                    borderColor: '#4CAF50',
                    fill: false,
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
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
                            text: 'Date'
                        }
                    }
                }
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        updateDebtChart(); // Initial chart update

        // Event listener for the "Next Debt" button
        document.getElementById('nextDebt').addEventListener('click', function () {
            currentDebtIndex = (currentDebtIndex + 1) % debts.length; // Cycle through debts
            const currentDebt = debts[currentDebtIndex];
            document.getElementById('debtTitle').innerText = `Payment History for ${currentDebt.name}`;
            updateDebtChart(); // Update the chart with the new debt
        });
    });
        
</script>

