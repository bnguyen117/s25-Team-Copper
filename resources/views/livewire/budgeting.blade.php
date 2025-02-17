<div>
    @if (session()->has('message'))
        <div class="alert alert-success">
            {{ session('message') }}
        </div>
    @endif

    <form wire:submit.prevent="saveBudget">
        <div class="mb-4">
            <label for="income" class="block text-sm font-medium text-gray-700">Income</label>
            <input type="number" wire:model="income" id="income" class="mt-1 block w-full" placeholder="Enter your income">
        </div>

        <div class="mb-4">
            <label for="expenses" class="block text-sm font-medium text-gray-700">Expenses</label>
            <input type="number" wire:model="expenses" id="expenses" class="mt-1 block w-full" placeholder="Enter your expenses">
        </div>

        <div class="mb-4">
            <label for="savings" class="block text-sm font-medium text-gray-700">Savings</label>
            <input type="number" wire:model="savings" id="savings" class="mt-1 block w-full" placeholder="Enter your savings">
        </div>

        <div class="mb-4">
            <label for="remaining_balance" class="block text-sm font-medium text-gray-700">Remaining Balance</label>
            <input type="number" wire:model="remaining_balance" id="remaining_balance" class="mt-1 block w-full" readonly>
        </div>

        <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-lg">Save Budget</button>
    </form>
</div>