<!-- Extend app.blade.php Page layout -->
<x-app-layout>

    <!-- Header -->
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Finances') }}
        </h2>
    </x-slot>
    
    <!-- Budgeting Section -->
    <div class="mt-16 mb-16 m-auto max-w-7xl lg:px-8">
        <!-- Budget Chart -->
        <div class="mt-8 p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md">
            <h2 class="text-lg font-bold text-gray-800 dark:text-gray-200 text-center">Budget Breakdown</h2>
            @livewire('budget-chart')
        </div>
        
        <!-- Budget Form (Manual or AI) -->
        <div class="mt-8 p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md">
            <h2 class="text-lg font-bold text-gray-800 dark:text-gray-200 text-center p-2">Create Your Budget</h2>
            @livewire('budget-form')
            <div class="mt-8 m-auto max-w-[21rem] sm:max-w-sm md:max-w-2xl lg:max-w-7xl lg:px-8">
                @livewire('transaction-table')
            </div>
        </div>
    </div>

    <!-- Debt Table -->
    <div class="mt-16 mb-16 m-auto max-w-7xl lg:px-8">
        @livewire('user-debt-table')
    </div>

    <!-- AI Budget Chat Section -->
    <div id="chatbot" class="mt-16 mb-16 m-auto max-w-7xl lg:px-8">
        <div class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md">
            <h2 class="text-lg font-bold text-gray-800 dark:text-gray-200 text-center">Chat with Your AI Budgeting Assistant</h2>
            @livewire('budgeting-chat')
        </div>
    </div>

</x-app-layout>
