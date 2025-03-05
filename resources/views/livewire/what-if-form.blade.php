
<div class="max-w-4xl mx-auto py-8">
    <!-- Container for the What-If Analysis form -->
     <div class="bg-gradient-to-br from-white to-gray-50 dark:from-gray-900 dark:to-gray-800 shadow-xl rounded-xl p-6 mb-8">

        <!-- Form Header -->
        <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-6">What-If Analysis</h2>

        <!-- Form that submits to the analyze() method in the WhatIfForm component-->
        <form wire:submit="analyze">
            <!-- Renders the form -->
            {{ $this->form }}

            <!-- Button Container  -->
            <div class="flex justify-center gap-4 mt-6">

                <!-- Button to submit the form and generate the analysis report -->
                <button 
                type="submit" 
                class="px-6 py-2 bg-blue-600 text-white rounded-lg
                 hover:bg-blue-700 transition-colors duration-200">
                    Generate Your Report
                </button>

                <!-- Button to clear the form using the clearForm() method -->
                <button 
                type="button" 
                wire:click="clearForm" 
                class="px-6 py-2 bg-gray-500 text-white rounded-lg
                 hover:bg-gray-600 transition-colors duration-200">
                    Clear Form
                </button>
                
            </div>
        </form>
     </div>

    <!-- Displays results card if there is a result -->
    @if($result)
        <div class="bg-gradient-to-br from-white to-gray-50 dark:from-gray-900 dark:to-gray-800 shadow-xl rounded-xl p-6">

            <!--If result holds an error from the WhatIfAnalysis service, display it -->
            @if(isset($result['error']))
                <div class="bg-red-100 dark:bg-red-900/30 border-l-4 border-red-500 p-4 rounded-lg text-red-700 dark:text-red-300">
                    <p class="font-semibold">Error: {{ $result['error'] }}</p>
                </div>
            
            <!-- Else if result holds a report, display it -->
            @else
                <!-- Report Header -->
                <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-4">{{ $result['debt_name'] }} Debt Analysis</h2>

                <!-- Summary Grid -->
                 <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
                    
                    <!-- The user's current debt amount -->
                    <div class="bg-gray-100 dark:bg-gray-800 rounded-lg p-4">
                        <p class="text-sm text-gray-600 dark:text-gray-400">Original Debt</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">${{ number_format($result['original_amount'], 2) }}</p>
                    </div>

                    <!-- The user's minimum required payment on the debt -->
                    <div class="bg-gray-100 dark:bg-gray-800 rounded-lg p-4">
                        <p class="text-sm text-gray-600 dark:text-gray-400">Minimum Required Payment</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">${{ number_format($result['minimum_payment'] ?? 0, 2) }}</p>
                    </div>

                    <!-- The user's current monthly payment amount -->
                    <div class="bg-gray-100 dark:bg-gray-800 rounded-lg p-4">
                        <p class="text-sm text-gray-600 dark:text-gray-400">Current Monthly Payment</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">${{ number_format($result['current_payment'], 2) }}</p>
                    </div>

                    <!-- The user's newly proposed monthly payment amount -->
                    <div class="bg-gray-100 dark:bg-gray-800 rounded-lg p-4">
                        <p class="text-sm text-gray-600 dark:text-gray-400">Proposed Monthly Payment</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">${{ number_format($result['new_payment'], 2) }}</p>
                    </div>
                    
                    <!-- Total months until the user fully repays the debt at the newly proposed monthly amount -->
                    <div class="bg-gray-100 dark:bg-gray-800 rounded-lg p-4">
                        <p class="text-sm text-gray-600 dark:text-gray-400">Total Months Until Full Repayment</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $result['total_months'] }}</p>
                    </div>

                    <!-- The amount of interest in dollars that the user will pay over the course of their debt -->
                    <div class="bg-gray-100 dark:bg-gray-800 rounded-lg p-4">
                        <p class="text-sm text-gray-600 dark:text-gray-400">Total Interest Paid</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">${{ number_format($result['total_interest_paid'], 2) }}</p>
                    </div>
                 </div>

                 <!-- Timeline Table -->
                <div class="mb-6">

                  <!-- Table Header -->
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">Repayment Timeline</h3>
                    
                    <!-- Table wrapper -->
                    <div class="max-h-64 overflow-y-auto rounded-lg border border-gray-200 dark:border-gray-700">

                        <!-- The table -->
                        <table class="w-full text-sm text-gray-700 dark:text-gray-300">

                            <!-- The Header Row of the Table -->
                            <thread class="sticky top-0 bg-gray-200 dark:bg-gray-800 text-gray-900 dark:text-gray-100">
                                <tr>
                                    <th class="px-4 py-2 text-left">Month</th>
                                    <th class="px-4 py-2 text-right">Balance</th>
                                    <th class="px-4 py-2 text-right">Interest Paid</th>
                                </tr>
                            </thread>
                            
                            <!-- Iterates over each entry in the timline array -->
                            <tbody>
                                @foreach($result['timeline'] as $entry)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="px-4 py-2">{{ $entry['month'] }}</td>
                                    <td class="px-4 py-2 text-right">${{ number_format($entry['balance'], 2) }}</td>
                                    <td class="px-4 py-2 text-right">${{ number_format($entry['interest_paid'], 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Chart -->
                <div>
                    @livewire(\App\Filament\Widgets\DebtRepaymentChart::class, ['chartData' => $result])
                </div>
            @endif
        </div>
    @endif

    <x-filament-actions::modals />
</div>