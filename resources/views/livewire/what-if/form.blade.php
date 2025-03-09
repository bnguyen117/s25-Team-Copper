
<!-- 
    Renders the view for the WhatIfForm component.
    Has access to '$report' from the render method inside of: 
    app/Livewire/WhatIfForm.php
-->
<div class="max-w-4xl mx-auto py-8">
    <!-- Container for the What-If Analysis form -->
     <div class="bg-gradient-to-br from-white to-gray-50 dark:from-gray-900 dark:to-gray-800 shadow-xl rounded-xl p-6 mb-8">

        <!-- Form Header -->
        <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-6">What-If Analysis</h2>

        <!-- Form submiting its state to the analyze() method in WhatIfForm -->
        <form wire:submit="analyze">
            <!-- Renders the form -->
            {{ $this->form }}

            <!-- Container for the form's buttons -->
            <div class="flex justify-center gap-4 mt-6">

                <!-- Submit button to generate the analysis report -->
                <button 
                type="submit" 
                class="px-6 py-2 bg-blue-600 text-white rounded-lg
                 hover:bg-blue-700 transition-colors duration-200">
                    Generate Report
                </button>

                <!-- Clear button to reset the form via the clearForm() WhatIfForm method -->
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

    <!-- Report card displayed below the form if $report was passed by the WhatIfForm component -->
    @if($report)
        <div class="bg-gradient-to-br from-white to-gray-50 dark:from-gray-900 dark:to-gray-800 shadow-xl rounded-xl p-6">

            <!-- Error message if $report holds an error -->
            @if(isset($report['error']))
                <div class="bg-red-100 dark:bg-red-900/30 border-l-4 border-red-500 p-4 rounded-lg text-red-700 dark:text-red-300">
                    <p class="font-semibold">Error: {{ $report['error'] }}</p>
                </div>
            
            <!-- Otherwise, display the analysis report -->
            @else
                @include('livewire.what-if.partials.report-display', ['report' => $report])
            @endif
        </div>
    @endif

    <x-filament-actions::modals />
</div>