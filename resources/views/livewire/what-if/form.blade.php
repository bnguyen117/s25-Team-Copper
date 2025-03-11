
<!-- 
    Displays the WhatIfForm component with a form and optional report.
    Receives '$report' from the render() method in App/Livewire/WhatIfForm.
-->

<!-- Main container centering the form and report -->
<div class="max-w-4xl mx-auto py-4">

    <!-- Section containing the What-If Analysis form-->
     <div class="bg-gradient-to-br from-white to-gray-50 dark:from-gray-900 dark:to-gray-800 shadow-xl rounded-xl p-6 mb-8">

        <!-- Header for the What-If analysis form -->
        <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-6">What-If Analysis</h2>

        <!-- Form submiting to the analyze() method in WhatIfForm and rendering the form's fields -->
        <form wire:submit="analyze">
            {{ $this->form }}

            <!-- Container for form buttons -->
            <div class="flex justify-center gap-4 mt-6">

                <!-- Submit button to trigger report generation -->
                <button 
                type="submit" 
                class="px-6 py-2 bg-blue-600 text-white rounded-lg
                 hover:bg-blue-700 transition-colors duration-200">
                    Generate
                </button>

                <!-- Clear button resetting the form and report via clearForm() -->
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

    <!-- Section displaying the report if provided by WhatIfForm -->
    @if($report)
        <div class="bg-gradient-to-br from-white to-gray-50 dark:from-gray-900 dark:to-gray-800 shadow-xl rounded-xl p-6">

            <!-- Display error message if $report holds an error -->
            @if(isset($report['error']))
                <div class="bg-red-100 dark:bg-red-900/30 border-l-4 border-red-500 p-4 rounded-lg text-red-700 dark:text-red-300">
                    <p class="font-semibold">Error: {{ $report['error'] }}</p>
                </div>
            
            <!-- Otherwise, display the report by including the report-display partial and passing the 'report' details -->
            @else
                @include('livewire.what-if.partials.report-display', ['report' => $report])
            @endif
        </div>
    @endif

    <x-filament-actions::modals />
</div>