<div class="max-w-7xl mx-auto p-4">

    <!-- Percentage Summary -->
    <div class="mb-6 bg-white dark:bg-gray-800 shadow rounded-lg p-4">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Budget Allocation</h2>
        @if($percentageSummary['needs']['percentage'] )
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

                <!-- Needs -->
                <div class="bg-gradient-to-tl from-gray-200 to-gray-50 dark:from-gray-900 dark:to-gray-700 rounded-lg shadow-md p-4">
                    <h3 class="text-md text-gray-600 dark:text-gray-200">Needs</h3>
                    <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                        {{ $percentageSummary['needs']['percentage'] }}%
                    </p>
                </div>

                <!-- Wants -->
                <div class="bg-gradient-to-tl from-gray-200 to-gray-50 dark:from-gray-900 dark:to-gray-700 rounded-lg shadow-md p-4">
                    <h3 class="text-md text-gray-600 dark:text-gray-200">Wants</h3>
                    <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                        {{ $percentageSummary['wants']['percentage'] }}%
                    </p>
                </div>

                <!-- Savings -->
                <div class="bg-gradient-to-tl from-gray-200 to-gray-50 dark:from-gray-900 dark:to-gray-700 rounded-lg shadow-md p-4">
                    <h3 class="text-md text-gray-600 dark:text-gray-200">Savings</h3>
                    <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                        {{ $percentageSummary['savings']['percentage'] }}%
                    </p>
                </div>

            </div>
        @else
            <p class="text-gray-500 dark:text-gray-400">Your budget is not set. Please create one in the form above.</p>
        @endif
    </div>

    <!-- Spending Summary -->
    <div class="mb-6 bg-white dark:bg-gray-800 shadow rounded-lg p-4">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Spending Summary</h2>
        @if($spendingSummary['needs']['budget'] > 0)
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

                <!-- Needs -->
                <div class="bg-gradient-to-tl from-gray-200 to-gray-50 dark:from-gray-900 dark:to-gray-700 rounded-lg shadow-md p-4">
                    <h3 class="text-md text-gray-600 dark:text-gray-200">Needs</h3>
                    <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                        ${{ number_format($spendingSummary['needs']['spent'], 2) }} / ${{ number_format($spendingSummary['needs']['budget'], 2) }}
                    </p>
                </div>

                <!-- Wants -->
                <div class="bg-gradient-to-tl from-gray-200 to-gray-50 dark:from-gray-900 dark:to-gray-700 rounded-lg shadow-md p-4">
                    <h3 class="text-md text-gray-600 dark:text-gray-200">Wants</h3>
                    <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                        ${{ number_format($spendingSummary['wants']['spent'], 2) }} / ${{ number_format($spendingSummary['wants']['budget'], 2) }}
                    </p>
                </div>

                <!-- Savings -->
                <div class="bg-gradient-to-tl from-gray-200 to-gray-50 dark:from-gray-900 dark:to-gray-700 rounded-lg shadow-md p-4">
                    <h3 class="text-md text-gray-600 dark:text-gray-200">Savings</h3>
                    <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                        ${{ number_format($spendingSummary['savings']['spent'], 2) }} / ${{ number_format($spendingSummary['savings']['budget'], 2) }}
                    </p>
                </div>
                
            </div>
        @else
            <p class="text-gray-500 dark:text-gray-400">Your budget is not set. Please create one in the form above.</p>
        @endif
    </div>

    <!-- Transaction Table -->
    <div>
        {{ $this->table }}
    </div>
</div>