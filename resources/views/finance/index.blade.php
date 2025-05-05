<!-- Extend app.blade.php Page layout -->
<x-app-layout>

    <!-- Header -->
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Finances') }}
        </h2>
    </x-slot>

    @if (session('badge_awarded'))
    <div class="mb-4 max-w-xl mx-auto bg-green-100 text-green-800 border border-green-300 px-4 py-2 rounded shadow">
        {{ session('badge_awarded') }}
    </div>
    @endif
    
    <!-- Budgeting Section -->
    <div class="mt-16 mb-16 m-auto max-w-7xl lg:px-8">
        <!-- Budget Chart -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md">
            <h2 class="mt-8 p-6 text-lg font-bold text-gray-800 dark:text-gray-200 text-center">Budget Breakdown</h2>
            @livewire('budget-chart')
        </div>
        
        <!-- Budget Form (Manual or AI) -->
        <div class="mt-8 p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md">
            <h2 class="text-lg font-bold text-gray-800 dark:text-gray-200 text-center p-2">Enter Your Monthly Income</h2>
                @livewire('budget-form')
            
            <!-- Transaction Table-->
            <div class="mt-8 m-auto max-w-[21rem] sm:max-w-sm md:max-w-2xl lg:max-w-7xl lg:px-8">
                @livewire('transaction-table')
            </div>

            <!-- Debt Table -->
            <div class="mt-8 m-auto max-w-[21rem] sm:max-w-sm md:max-w-2xl lg:max-w-7xl lg:px-8">
                @livewire('user-debt-table')
            </div>
        </div>
    </div>

    <!-- AI Budget Chat Section -->
    <div id="chatbot" class="mt-16 mb-16 m-auto max-w-7xl lg:px-8">
        <div class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md">
            <h2 class="text-lg font-bold text-gray-800 dark:text-gray-200 text-center">Chat with Your AI Budgeting Assistant</h2>
            @livewire('budgeting-chat')
        </div>
    </div>

</x-app-layout>
