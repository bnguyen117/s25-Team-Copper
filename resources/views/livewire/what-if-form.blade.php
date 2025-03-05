
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

            <!--If result holds an error, display it -->
            @if(isset($result['error']))
                <div class="bg-red-100 dark:bg-red-900/30 border-l-4 border-red-500 p-4 rounded-lg text-red-700 dark:text-red-300">
                    <p class="font-semibold">Error: {{ $result['error'] }}</p>
                </div>
            
            <!-- Else if result holds a report, display it -->
            @else
                <!-- Include the algorithm-specific report header and summary grid -->
                @include('livewire.what-if-reports.'.$result['what_if_algorithm'])

                <!-- Include the timeline table -->
                @include('livewire.what-if-tables.timeline-table')

                <!-- Chart import with `chartData` set as `$result -->
                @livewire(\App\Filament\Widgets\DebtRepaymentChart::class, ['chartData' => $result])
            @endif
        </div>
    @endif

    <x-filament-actions::modals />
</div>