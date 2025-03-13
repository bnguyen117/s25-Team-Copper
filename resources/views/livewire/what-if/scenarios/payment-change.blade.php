
<!--
    Displays the header and summary cards for a What-If Report
    analyzing the 'payment-change' scenario.
-->

<!-- Header displaying the debt name and scenrio type -->
<h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-4">
    {{ $report->debt->debt_name }} - Payment Adjustment
</h2>

<!-- Summary Section with current debt details and new payment impact -->
<div class="space-y-4 mb-6">

    <!-- Section showing current debt details -->
    <div class="bg-gradient-to-br from-gray-200 to-gray-100 dark:from-gray-900 dark:to-gray-700 shadow-xl rounded-xl p-4">
        <!-- Header for current debt detail cards -->
        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">Original Debt Details</h3>

        <!-- Grid container for current debt detail cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">

            <!-- Original monthly debt payment at the time of report -->
            <div class="bg-gradient-to-tl from-gray-200 to-gray-50 dark:from-gray-900 dark:to-gray-700 rounded-lg shadow-md  p-4">
                <p class="text-sm text-gray-600 dark:text-gray-400">Monthly Payment</p>
                <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">${{ number_format($report->original_monthly_debt_payment, 2) }}</p>
            </div>

            <!-- Original total debt amount at the time of report -->
            <div class="bg-gradient-to-tl from-gray-200 to-gray-50 dark:from-gray-900 dark:to-gray-700 rounded-lg shadow-md  p-4">
                <p class="text-sm text-gray-600 dark:text-gray-400">Debt Amount</p>
                <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">${{ number_format($report->original_debt_amount, 2) }}</p>
            </div>

            <!-- Original annual interest rate at the time of report -->
            <div class="bg-gradient-to-tl from-gray-200 to-gray-50 dark:from-gray-900 dark:to-gray-700 rounded-lg shadow-md  p-4">
                <p class="text-sm text-gray-600 dark:text-gray-400">Interest Rate</p>
                <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ number_format($report->original_interest_rate ?? 0, 2) }}%</p>
            </div>

        </div>
    </div>

    <!-- Section showing new payment impact -->
    <div class="bg-gradient-to-br from-gray-200 to-gray-100 dark:from-gray-900 dark:to-gray-700 shadow-xl rounded-xl p-4">

        <!-- Header for payment impact cards -->
        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">New Payment Impact</h3>

        <!-- Grid container for new payment impact cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">

            <!-- Proposed new monthly debt payment -->
            <div class="bg-gradient-to-tl from-gray-200 to-gray-50 dark:from-gray-900 dark:to-gray-700 rounded-lg shadow-md p-4">
                <p class="text-sm text-gray-600 dark:text-gray-400">Proposed Monthly Payment</p>
                <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">${{ number_format($report->new_monthly_debt_payment, 2) }}</p>
            </div>

            <!-- Total months until full repayment -->
            <div class="bg-gradient-to-tl from-gray-200 to-gray-50 dark:from-gray-900 dark:to-gray-700 rounded-lg shadow-md p-4">
                <p class="text-sm text-gray-600 dark:text-gray-400">Months Until Full Repayment</p>
                <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $report->total_months }}</p>
            </div>

            <!-- Total interest paid -->
            <div class="bg-gradient-to-tl from-gray-200 to-gray-50 dark:from-gray-900 dark:to-gray-700 rounded-lg shadow-md p-4">
                <p class="text-sm text-gray-600 dark:text-gray-400">Total Interest Paid</p>
                <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">${{ number_format($report->total_interest_paid, 2) }}</p>
            </div>

        </div>
    </div>
</div>