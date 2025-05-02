<div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md">
    @if (session()->has('success'))
        <div class="text-green-600">{{ session('success') }}</div>
    @endif

    <!-- Income -->
    <div class="mt-4">
        <label class="block text-gray-700 dark:text-gray-300">Income ($)</label>
        <input type="number" wire:model="income" class="w-full p-2 border rounded" wire:change="calculateRemainingBalance">
    </div>

    <!-- Default Budget Button -->
    <button wire:click="defaultBudget" class="mt-4 px-4 py-2 bg-green-500 text-white rounded">
        Default Budget
    </button>

    <!-- Prioritize Debt Payoff Button-->
    <button wire:click="prioritizeDebts" class="mt-4 px-4 py-2 bg-green-500 text-white rounded">
        Priotitize Debt Payoff
    </button>

    <!-- Prioritize Wants Button-->
    <button wire:click="prioritizeWants" class="mt-4 px-4 py-2 bg-green-500 text-white rounded">
        Priotitize Personal Expenses
    </button>

    <!-- Prioritize Savings Button-->
    <button wire:click="prioritizeSavings" class="mt-4 px-4 py-2 bg-green-500 text-white rounded">
        Priotitize Savings
    </button>
</div>
