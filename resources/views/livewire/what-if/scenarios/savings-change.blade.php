<!-- 
    Displays the header and summary cards for a Savings What-If Report analyzing the 
    'savings-change' algorithm
-->

<!-- Header displaying the savings name and scenario type -->
 <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-4">
    {{ $report->savings_name }} - Savings Rate Impact
</h2>

<!-- Summary Section with current savings details and new rate impact -->
 <div class="space-y-4 mb-6">
    <!-- Section showing current savings details -->
     <div class="bg-gradient-to-br from-gray-200 to-gray-100 dark:from-gray-900 dark:to-gray-700 shadow-xl rounded-xl p-4">
        <!-- Header for current savings detail cards -->
        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">Original Savings Details</h3>

        <!-- Grid container for current savings detail cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">

            <!-- Original annual interest rate at time of report -->
            <div class="bg-gradient-to-tl from-gray-200 to-gray-50 dark:from-gray-900 dark:to-gray-700 rounded-lg shadow-md p-4">
                <p class="text-sm text-gray-600 dark:text-gray-400">Interest Rate</p>
                <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ number_format($report->original_interest_rate ?? 0, 2) }}%</p>
            </div>

            <!-- Original savings amount at time of report  -->
            <div class="bg-gradient-to-tl from-gray-200 to-gray-50 dark:from-gray-900 dark:to-gray-700 rounded-lg shadow-md p-4">
                <p class="text-sm text-gray-600 dark:text-gray-400">Savings Amount</p>
                <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">${{ number_format($report->original_savings, 2) }}</p>
            </div>

            <!-- Original monthly savings payment at the time of report -->
            <div class="bg-gradient-to-tl from-gray-200 to-gray-50 dark:from-gray-900 dark:to-gray-700 rounded-lg shadow-md p-4">
                <p class="text-sm text-gray-600 dark:text-gray-400">Monthly Savings Rate</p>
                <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">${{ number_format($report->current_monthly_savings, 2) }}</p>
            </div>

        </div>

    <!-- Section showing new rate impact -->
     <div class="bg-gradient-to-br from-gray-200 to-gray-100 dark:from-gray-900 dark:to-gray-700 shadow-xl rounded-xl p-4">
        <!-- Header for the new rate impact cards -->
        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">New Savings Rate Impact</h3>

        <!-- Grid container for new rate impact cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4"></div>

        <!-- Proposed new monthly savings rate -->
        <div class="bg-gradient-to-tl from-gray-200 to-gray-50 dark:from-gray-900 dark:to-gray-700 rounded-lg shadow-md p-4">
                <p class="text-sm text-gray-600 dark:text-gray-400">Proposed Savings Rate</p>
                <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">${{ number_format($report->new_monthly_savings_rate, 2) }}</p>
        </div>

        <!-- Total months saved -->
        <div class="bg-gradient-to-tl from-gray-200 to-gray-50 dark:from-gray-900 dark:to-gray-700 rounded-lg shadow-md p-4">
                <p class="text-sm text-gray-600 dark:text-gray-400">Total months saved</p>
                <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $report->total_months }}</p>
        </div>

        <!-- Total interest paid -->
        <div class="bg-gradient-to-tl from-gray-200 to-gray-50 dark:from-gray-900 dark:to-gray-700 rounded-lg shadow-md p-4">
                <p class="text-sm text-gray-600 dark:text-gray-400">Total Interest Earned</p>
                <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">${{ number_format($report->total_interest_earned, 2) }}</p>
        </div>

     </div>"
 </div>