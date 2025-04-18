<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Budget;
use Illuminate\Support\Facades\Auth;

class BudgetForm extends Component
{
    public $income, $needs, $wants, $savings, $remaining_balance;
    public $useAI = false; // Toggle AI recommendation

    public function mount()
    {
        $budget = Budget::where('user_id', Auth::id())->latest()->first();

        if ($budget) {
            $this->income = $budget->income;
            $this->needs = $this->income * $budget->needs_percentage;
            $this->wants = $this->income * $budget->wants_percentage;
            $this->savings = $this->income * $budget->savings_percentage;
            $this->remaining_balance = $this->calculateRemainingBalance();
        }
        
        Budget::updateOrCreate(
            ['user_id' => Auth::id()],
            [
                'income' => $this->income,
                'budgeted_needs' => $this->needs,
                'budgeted_wants' => $this->wants,
                'budgeted_savings' => $this->savings,
                'remaining_balance' => $this->remaining_balance,
            ]
        );
    }

    public function calculateRemainingBalance()
    {
        $this->remaining_balance = $this->income - ($this->needs + $this->wants + $this->savings);
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
        ]);

        Budget::updateOrCreate(
            ['user_id' => Auth::id()],
            [
                'income' => $this->income,
                'budgeted_needs' => $this->needs,
                'budgeted_wants' => $this->wants,
                'budgeted_savings' => $this->savings,
                'remaining_balance' => $this->remaining_balance,
            ]
        );

        session()->flash('success', 'Budget saved successfully!');
    }

    public function defaultBudget()
    {
        Budget::updateOrCreate(
            ['user_id' => Auth::id()],
            [
                'needs_percentage' => 0.50,
                'wants_percentage' => 0.30,
                'savings_percentage' => 0.20,
            ]
        );

        $this->mount();
    }

    public function prioritizeDebts()
    {
        Budget::updateOrCreate(
            ['user_id' => Auth::id()],
            [
                'needs_percentage' => 0.80,
                'wants_percentage' => 0.10,
                'savings_percentage' => 0.10,
            ]
        );

        $this->mount();
    }

    public function prioritizeSavings()
    {
        Budget::updateOrCreate(
            ['user_id' => Auth::id()],
            [
                'needs_percentage' => 0.50,
                'wants_percentage' => 0.10,
                'savings_percentage' => 0.40,
            ]
        );

        $this->mount();
    }

    public function prioritizeWants()
    {
        
        Budget::updateOrCreate(
            ['user_id' => Auth::id()],
            [
                'needs_percentage' => 0.50,
                'wants_percentage' => 0.40,
                'savings_percentage' => 0.10,
            ]
        );

        $this->mount();
    }

    public function render()
    {
        return view('livewire.budget-form');
    }
}
