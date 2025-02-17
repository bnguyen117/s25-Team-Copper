<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Budget;

class Budgeting extends Component
{
    public $income = 0;
    public $expenses = 0;
    public $savings = 0;
    public $remaining_balance = 0;

    public function mount()
    {
        // Get the user's budget if it exists
        $budget = Budget::where('user_id', auth()->id())->first();
        if ($budget) {
            $this->income = $budget->income ?? 0;
            $this->expenses = $budget->expenses ?? 0;
            $this->savings = $budget->savings ?? 0;
            $this->remaining_balance = $budget->remaining_balance ?? 0;
        }
    }

    // Update the remaining balance whenever income, expenses, or savings change
    public function updated($propertyName)
    {
        if (in_array($propertyName, ['income', 'expenses', 'savings'])) {
            $this->calculateRemainingBalance();
        }
    }

    // Calculate the remaining balance
    public function calculateRemainingBalance()
    {
        $this->remaining_balance = max(0, $this->income - $this->expenses - ($this->savings ?? 0));
    }

    // Save the budget to the database
    public function saveBudget()
    {
        Budget::updateOrCreate(
            ['user_id' => auth()->id()],
            [
                'income' => $this->income,
                'expenses' => $this->expenses,
                'savings' => $this->savings,
                'remaining_balance' => $this->remaining_balance,
            ]
        );

        session()->flash('message', 'Budget saved successfully.');
    }

    public function render()
    {
        return view('livewire.budgeting');
    }
}
