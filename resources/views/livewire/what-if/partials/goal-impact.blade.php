
<!-- UI for the goals section of a WhatIfReport -->
<div class="mt-6">

    <!-- Title -->
    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">Goal Impact</h3>

    <!-- Section container to hold goal cards -->
    <div class="bg-gradient-to-br from-gray-200 to-gray-100 dark:from-gray-900 dark:to-gray-700 shadow-xl rounded-xl p-4">

        <!-- Check if the user has no monthly savings leftover -->
        @if($report->goal_impact['monthly_savings'] == 0)

            <!-- Warning container to provide context to the user when they have no monthly savings -->
            <div class="border-l-4 border-red-500 bg-red-100 dark:bg-red-900/30 p-4 rounded-r-lg">

                <!-- Title message -->
                <p class="text-lg font-semibold text-red-700 dark:text-red-300">
                    No Progress Possible for {{ $report->goal_impact['goal_name'] }}
                </p>

                <!-- Explanation message -->
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">
                    This plan leaves no savings because your total expenses 
                    (${{ number_format($report->goal_impact['total_expenses'], 2) }}) exceed your income 
                    (${{ number_format($report->goal_impact['monthly_income'], 2) }}) by 
                    ${{ number_format($report->goal_impact['shortfall'], 2) }}.
                </p>

                <!-- Tip message to provide a helpful nudge -->
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    Tip: Reduce expenses by ${{ number_format($report->goal_impact['shortfall'], 2) }} or increase income to start saving.
                </p>
            </div>
        
        <!-- The user has monthly savings leftover after all expenses -->
        @else
            <!-- Grid layout to hold goal info cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                
                <!-- The goal's name -->
                <div class="bg-gradient-to-tl from-gray-200 to-gray-50 dark:from-gray-900 dark:to-gray-700 rounded-lg shadow-md p-4">
                    <p class="text-sm text-gray-600 dark:text-gray-400">Goal</p>
                    <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $report->goal_impact['goal_name'] }}</p>
                </div>

                <!-- Target Amount to achieve goal -->
                <div class="bg-gradient-to-tl from-gray-200 to-gray-50 dark:from-gray-900 dark:to-gray-700 rounded-lg shadow-md p-4">
                    <p class="text-sm text-gray-600 dark:text-gray-400">Target Amount</p>
                    <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">${{ number_format($report->goal_impact['target_amount'], 2) }}</p>
                </div>

                <!-- Current Amount accumulated -->
                <div class="bg-gradient-to-tl from-gray-200 to-gray-50 dark:from-gray-900 dark:to-gray-700 rounded-lg shadow-md p-4">
                    <p class="text-sm text-gray-600 dark:text-gray-400">Current Amount</p>
                    <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">${{ number_format($report->goal_impact['current_amount'], 2) }}</p>
                </div>

                <!-- Remaining amount left to gather -->
                <div class="bg-gradient-to-tl from-gray-200 to-gray-50 dark:from-gray-900 dark:to-gray-700 rounded-lg shadow-md p-4">
                    <p class="text-sm text-gray-600 dark:text-gray-400">Amount Still Needed</p>
                    <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">${{ number_format($report->goal_impact['amount_still_needed'], 2) }}</p>
                </div>

                <!-- Monthly savings after all other expenses -->
                <div class="bg-gradient-to-tl from-gray-200 to-gray-50 dark:from-gray-900 dark:to-gray-700 rounded-lg shadow-md p-4">
                    <p class="text-sm text-gray-600 dark:text-gray-400">Monthly Savings After All Expenses</p>
                    <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">${{ number_format($report->goal_impact['monthly_savings'], 2) }}</p>
                </div>

                <!-- Months until goal is achieved -->
                <div class="bg-gradient-to-tl from-gray-200 to-gray-50 dark:from-gray-900 dark:to-gray-700 rounded-lg shadow-md p-4">
                    <p class="text-sm text-gray-600 dark:text-gray-400">Months Until Achieved</p>
                    <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $report->goal_impact['projected_months'] ?? 'N/A' }}</p>
                </div>
            </div>

            <!-- The user has already achieved their goal -->
            @if($report->goal_impact['amount_still_needed'] == 0)

                <!-- Notification container to notify the user their goal is already achieved -->
                <div class="mt-2 border-l-4 border-green-500 bg-green-100 dark:bg-green-900/30 p-4 rounded-r-lg">

                    <!-- Title message -->
                    <p class="text-lg font-semibold text-green-700 dark:text-green-300">
                        Goal Achieved!
                    </p>

                    <!-- Explanation message -->
                    <p class="mt-1 text-sm text-green-600 dark:text-green-400">
                        You've already met your target of ${{ number_format($report->goal_impact['target_amount'], 2) }} for {{ $report->goal_impact['goal_name'] }}. Congratulations!
                    </p>
                </div>

            <!-- The user is past due on achieving their goal -->
            @elseif($report->goal_impact['is_overdue'] && $report->goal_impact['amount_still_needed'] > 0)

                <!-- Warning container to alert the user that they are past their goal's achieve-by date -->
                <div class="mt-2 border-l-4 border-red-500 bg-red-100 dark:bg-red-900/30 p-4 rounded-r-lg">

                    <!-- Title message -->
                    <p class="text-lg font-semibold text-red-700 dark:text-red-300">
                        Goal Overdue!
                    </p>

                    <!-- Explantion message -->
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">
                        Your goal {{ $report->goal_impact['goal_name'] }} is past due, 
                        and you still need ${{ number_format($report->goal_impact['amount_still_needed'], 2) }}.
                    </p>

                    <!-- Tip message  -->
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                        Tip: You can still accomplish your goal in {{ $report->goal_impact['projected_months'] }} months 
                        with your current savings of ${{ number_format($report->goal_impact['monthly_savings'], 2) }}, 
                        or catch up this month by saving an extra ${{ number_format($report->goal_impact['extra_savings_needed'], 2) }} now.
                    </p>
                </div>

            <!-- The user's plan will delay their goal -->
            @elseif($report->goal_impact['projected_months'] > $report->goal_impact['target_months'])

                <!-- Warning container to alert the user that this plan will delay their goal -->
                <div class="mt-2 border-l-4 border-yellow-500 bg-yellow-100 dark:bg-yellow-900/30 p-4 rounded-r-lg">

                    <!-- Title message -->
                    <p class="text-lg font-semibold text-yellow-700 dark:text-yellow-300">
                        Goal Delay Warning
                    </p>

                    <!-- Explanation message -->
                    <p class="mt-1 text-sm text-yellow-600 dark:text-yellow-400">
                        This plan delays your goal beyond its {{ $report->goal_impact['target_months'] }}-month target.
                    </p>

                    <!-- Tip message to provide a helpful nudge -->
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                        Tip: Save an extra ${{ number_format($report->goal_impact['extra_savings_needed'], 2) }} per month to meet your target on time.
                    </p>
                </div>

            <!-- The user's plan keeps their goal on track -->
            @elseif($report->goal_impact['projected_months'] <= $report->goal_impact['target_months'])
                <!-- Notification container to notify the user that their plan keeps their goal on track -->
                <div class="mt-2 border-l-4 border-green-500 bg-green-100 dark:bg-green-900/30 p-4 rounded-r-lg">

                    <!-- Title message -->
                    <p class="text-lg font-semibold text-green-700 dark:text-green-300">
                        Goal On Track!
                    </p>

                    <!-- Explanation message -->
                    <p class="mt-1 text-sm text-green-600 dark:text-green-400">
                        This plan keeps your goal within its {{ $report->goal_impact['target_months'] }}-month target.
                    </p>
                </div>
            @endif
        @endif
    </div>
</div>