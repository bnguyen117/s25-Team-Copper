<div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md">
    @if (session()->has('success'))
        <div class="text-green-600">{{ session('success') }}</div>
    @endif

    <!-- Error Message -->
    @if (session()->has('error'))
        <div class="text-red-600 dark:text-red-400 mb-4">{{ session('error') }}</div>
    @endif

    <!-- Income -->
    <div class="mt-4">
        <label class="block text-gray-700 dark:text-gray-300">Income ($)</label>
        <input type="number" wire:model="income" class="w-full p-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
    </div>

    <h2 class="mt-8 p-6 text-lg font-bold text-gray-800 dark:text-gray-200 text-center p-2">Create Your Budget</h2>
    <!-- Default Budget Button -->
    <button wire:click="defaultBudget" class="mt-4 px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-300">
        Create Default Budget
    </button>

    <!-- Prioritize Debt Payoff Button-->
    <button wire:click="prioritizeDebts" class="mt-4 px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-300">
        Prioritize Debt Payoff
    </button>

    <!-- Prioritize Wants Button-->
    <button wire:click="prioritizeWants" class="mt-4 px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-300">
        Prioritize Personal Expenses
    </button>

    <!-- Prioritize Savings Button-->
    <button wire:click="prioritizeSavings" class="mt-4 px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-300">
        Prioritize Savings
    </button>

    <!-- User Custom Budget -->
    <!-- This doesn't work yet!
    <button wire:click="customizeBudget" class="mt-4 px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-300">
        Set Custom Budget
    </button>
    -->
</div>
