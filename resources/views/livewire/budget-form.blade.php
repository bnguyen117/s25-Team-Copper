<div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md">
    <h2 class="text-lg font-bold text-gray-800 dark:text-gray-200">Create Your Budget</h2>

    @if (session()->has('success'))
        <div class="text-green-600">{{ session('success') }}</div>
    @endif

    <!-- Income -->
    <div class="mt-4">
        <label class="block text-gray-700 dark:text-gray-300">Income ($)</label>
        <input type="number" wire:model="income" class="w-full p-2 border rounded" wire:change="calculateRemainingBalance">
    </div>

    <!-- Expenses -->
    <!-- <div class="mt-4">
        <label class="block text-gray-700 dark:text-gray-300">Expenses ($)</label>
        <input type="number" wire:model="needs" class="w-full p-2 border rounded" wire:change="calculateRemainingBalance">
    </div> -->

    <!-- Savings -->
    <!-- <div class="mt-4">
        <label class="block text-gray-700 dark:text-gray-300">Savings ($)</label>
        <input type="number" wire:model="savings" class="w-full p-2 border rounded" wire:change="calculateRemainingBalance">
    </div> -->

    <!-- Remaining Balance -->
    <!-- <div class="mt-4">
        <label class="block text-gray-700 dark:text-gray-300">Remaining Balance ($)</label>
        <input type="number" wire:model="remaining_balance" class="w-full p-2 border rounded bg-gray-200" readonly>
    </div> -->

    <!-- Default Budget Button -->
    <button wire:click="defaultBudget" class="mt-4 px-4 py-2 bg-green-500 text-white rounded">
        Default Budget
    </button>

    <!-- Prioritize Debt Payoff Button-->
    <button wire:click="prioritizeDebts" class="mt-4 px-4 py-2 bg-green-500 text-white rounded">
        Priotitize Debt Payoff
    </button>

    <!-- Prioritize Savings Button-->
    <button wire:click="prioritizeSavings" class="mt-4 px-4 py-2 bg-green-500 text-white rounded">
        Priotitize Savings
    </button>

    <!-- Prioritize Wants Button-->
    <button wire:click="prioritizeWants" class="mt-4 px-4 py-2 bg-green-500 text-white rounded">
        Priotitize Wants
    </button>
</div>
