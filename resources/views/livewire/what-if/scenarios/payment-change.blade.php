
<!-- Report header -->
<h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-4">
    {{ $report->debt->debt_name }} - Payment Adjustment
</h2>

<!-- Summary Grid -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
    
    <!-- The user's current debt amount -->
    <div class="bg-gray-100 dark:bg-gray-800 rounded-lg p-4">
        <p class="text-sm text-gray-600 dark:text-gray-400">Original Debt</p>
        <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">${{ number_format($report->original_amount, 2) }}</p>
    </div>

    <!-- The user's current monthly payment amount -->
    <div class="bg-gray-100 dark:bg-gray-800 rounded-lg p-4">
        <p class="text-sm text-gray-600 dark:text-gray-400">Current Monthly Payment</p>
        <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">${{ number_format($report->current_payment, 2) }}</p>
    </div>


    <!-- The user's newly proposed monthly payment amount -->
    <div class="bg-gray-100 dark:bg-gray-800 rounded-lg p-4">
        <p class="text-sm text-gray-600 dark:text-gray-400">Proposed Monthly Payment</p>
        <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">${{ number_format($report->new_payment, 2) }}</p>
    </div>

    <!-- The user's interest rate on the debt -->
    <div class="bg-gray-100 dark:bg-gray-800 rounded-lg p-4">
        <p class="text-sm text-gray-600 dark:text-gray-400">Interest Rate</p>
        <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">${{ number_format($report->debt->interest_rate ?? 0, 2) }}%</p>
    </div>

    <!-- The amount of interest in dollars that the user will pay over the course of their debt -->
    <div class="bg-gray-100 dark:bg-gray-800 rounded-lg p-4">
        <p class="text-sm text-gray-600 dark:text-gray-400">Total Interest Paid</p>
        <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">${{ number_format($report->total_interest_paid, 2) }}</p>
    </div>

    <!-- Total months until the user fully repays the debt at the newly proposed monthly amount -->
    <div class="bg-gray-100 dark:bg-gray-800 rounded-lg p-4">
        <p class="text-sm text-gray-600 dark:text-gray-400">Total Months Until Full Repayment</p>
        <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $report->total_months }}</p>
    </div>
</div>