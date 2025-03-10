<!-- Report header -->
<h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-4">
    {{ $report->debt->debt_name }} - Interest Rate Impact
</h2>

<!-- Summary Section -->
<div class="space-y-4 mb-6">

    <!-- Current Debt Details -->
    <div>
        <!-- Header for cards representing current debt data -->
        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">Current Debt Details</h3>

        <!-- Holds the current debt data in a grid based on screen size -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">

            <!-- The user's current interest rate -->
            <div class="bg-gray-100 dark:bg-gray-800 rounded-lg p-4">
                <p class="text-sm text-gray-600 dark:text-gray-400">Interest Rate</p>
                <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ number_format($report->debt->interest_rate ?? 0, 2) }}%</p>
            </div>
            
            <!-- The user's current debt amount -->
            <div class="bg-gray-100 dark:bg-gray-800 rounded-lg p-4">
                <p class="text-sm text-gray-600 dark:text-gray-400">Debt Amount</p>
                <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">${{ number_format($report->original_amount, 2) }}</p>
            </div>

            <!-- The user's current monthly payment amount -->
            <div class="bg-gray-100 dark:bg-gray-800 rounded-lg p-4">
                <p class="text-sm text-gray-600 dark:text-gray-400">Monthly Payment</p>
                <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">${{ number_format($report->current_payment, 2) }}</p>
            </div>

        </div>
    </div>

    <!-- New Rate Impact -->
    <div class="bg-gradient-to-br from-gray-200 to-gray-100 dark:from-gray-900 dark:to-gray-700 shadow-xl rounded-xl p-4">

        <!-- Header for cards representing the impact of changing the debt's interest rate -->
        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">New Rate Impact</h3>

        <!-- Holds the proposed interest rate data in a grid based on screen size -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">

            <!-- The proposed new interest rate -->
            <div class="bg-gradient-to-tl from-gray-200 to-gray-50 dark:from-gray-900 dark:to-gray-700 rounded-lg shadow-md  p-4">
                <p class="text-sm text-gray-600 dark:text-gray-400">Proposed Interest Rate</p>
                <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ number_format($report->new_interest_rate ?? 0, 2) }}%</p>
            </div>

            <!-- Total months until full repayment with proposed rate -->
            <div class="bg-gradient-to-tl from-gray-200 to-gray-50 dark:from-gray-900 dark:to-gray-700 rounded-lg shadow-md p-4">
                <p class="text-sm text-gray-600 dark:text-gray-400">Months Until Full Repayment</p>
                <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $report->total_months }}</p>
            </div>

            <!-- Total interest paid with proposed rate -->
            <div class="bg-gradient-to-tl from-gray-200 to-gray-50 dark:from-gray-900 dark:to-gray-700 rounded-lg shadow-md  p-4">
                <p class="text-sm text-gray-600 dark:text-gray-400">Total Interest Paid</p>
                <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">${{ number_format($report->total_interest_paid, 2) }}</p>
            </div>

        </div>
    </div>
</div>