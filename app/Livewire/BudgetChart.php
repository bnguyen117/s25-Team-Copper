<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Budget;
use Illuminate\Support\Facades\Auth;

class BudgetChart extends Component
{
    public $income, $expenses, $savings, $remaining_balance;

    public function mount()
    {
        $budget = Budget::where('user_id', Auth::id())->latest()->first();

        if ($budget) {
            $this->income = $budget->income;
            $this->expenses = $budget->budgeted_needs + $budget->budgeted_wants; // Needs + Wants = Expenses
            $this->savings = $budget->budgeted_savings;
            $this->remaining_balance = $budget->remaining_balance;
        } else {
            $this->income = 0;
            $this->expenses = 0;
            $this->savings = 0;
            $this->remaining_balance = 0;
        }
    }

    public function render()
    {
        return view('livewire.budget-chart');
    }
}
