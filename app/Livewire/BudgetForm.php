<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Budget;
use Illuminate\Support\Facades\Auth;

class BudgetForm extends Component
{
    public $income, $expenses, $savings, $remaining_balance;
    public $useAI = false; // Toggle AI recommendation

    public function mount()
    {
        $budget = Budget::where('user_id', Auth::id())->latest()->first();

        if ($budget) {
            $this->income = $budget->income;
            $this->expenses = $budget->budgeted_needs + $budget->budgeted_wants;
            $this->savings = $budget->budgeted_savings;
            $this->remaining_balance = $budget->remaining_balance;
        }
    }

    public function calculateRemainingBalance()
    {
        $this->remaining_balance = $this->income - ($this->expenses + $this->savings);
    }

    public function useAIRecommendations()
    {
        $this->useAI = true;

        // AI Budget Suggestions (Basic Algorithm)
        $this->expenses = $this->income * 0.50; // 50% Needs
        $this->savings = $this->income * 0.20; // 20% Savings
        $this->remaining_balance = $this->income - ($this->expenses + $this->savings);
    }

    public function saveBudget()
    {
        $this->validate([
            'income' => 'required|numeric|min:0',
            'expenses' => 'required|numeric|min:0',
            'savings' => 'required|numeric|min:0',
        ]);

        Budget::updateOrCreate(
            ['user_id' => Auth::id()],
            [
                'income' => $this->income,
                'budgeted_needs' => $this->expenses * 0.70, // 70% of expenses are needs
                'budgeted_wants' => $this->expenses * 0.30, // 30% of expenses are wants
                'budgeted_savings' => $this->savings,
                'remaining_balance' => $this->remaining_balance,
            ]
        );

        session()->flash('success', 'Budget saved successfully!');
    }

    public function render()
    {
        return view('livewire.budget-form');
    }
}
