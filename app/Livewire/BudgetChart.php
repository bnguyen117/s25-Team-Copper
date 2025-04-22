<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Budget;
use Illuminate\Support\Facades\Auth;

class BudgetChart extends Component
{
    public $income, $needs, $wants, $savings, $remaining_balance;

    public function mount()
    {
        $budget = Budget::where('user_id', Auth::id())->latest()->first();

        if ($budget) {
            $this->income = $budget->income;
            $this->needs = $this->income * ($budget->needs_percentage / 100);
            $this->wants = $this->income * ($budget->wants_percentage / 100);
            $this->savings = $this->income * ($budget->savings_percentage / 100);
            $this->remaining_balance = $this->calculateRemainingBalance();
        } else {
            $this->income = 0;
            $this->needs = 0;
            $this->wants = 0;
            $this->savings = 0;
            $this->remaining_balance = 0;
        }
    }

    public function calculateRemainingBalance()
    {
        $this->remaining_balance = $this->income - ($this->needs + $this->wants + $this->savings);
    }

    public function render()
    {
        return view('livewire.budget-chart');
    }
}
