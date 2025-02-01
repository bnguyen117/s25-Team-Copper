
<div class="bg-white shadow-lg sm:rounded-lg p-6 text-gray-900 dark:text-gray-100">
    <form wire:submit="analyze">
        <!-- Renders the connected Filament form -->
        {{ $this->form }}

        <!-- Submit and Clear form buttons -->
        <div class="flex justify-center pt-6 space-x-8">
            <button type="submit" class="w-64 p-2 bg-blue-500 text-white rounded hover:bg-blue-400">
                Run Analysis
            </button>
            <button type="button" wire:click="clearForm" class="w-64 p-2 bg-gray-500 text-white rounded">
                Clear Form
            </button>
        </div>

    </form>
    
    <!-- Required for modal suport -->
    <x-filament-actions::modals />
</div>