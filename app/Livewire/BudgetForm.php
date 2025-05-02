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
            $this->needs = $this->income * ($budget->needs_percentage / 100);
            $this->wants = $this->income * ($budget->wants_percentage / 100);
            $this->savings = $this->income * ($budget->savings_percentage / 100);
            $this->remaining_balance = $this->calculateRemainingBalance();
        }
        else{
            $this->income = 0;
            $this->needs = 0;
            $this->wants = 0;
            $this->savings = 0;
            $this->remaining_balance = 0;
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
        $budget = Budget::where('user_id', Auth::id()) ->first();
        return $this->income - $budget->needs_progress - $budget->wants_progress - $budget->savings_progress;
    }

    protected function validateBudgetAmounts()
    {
        $budget = Budget::where('user_id', Auth::id())->first();

        // Check each category
        if ($this->needs < ($budget->needs_progress ?? 0)) {
            session()->flash('error',
             "Cannot set Needs budget to $" . number_format($this->needs, 2) . 
             ". Already spent $" . number_format($budget->needs_progress, 2) . ".");
            return false;
        }
        if ($this->wants < ($budget->wants_progress ?? 0)) {
            session()->flash('error', 
            "Cannot set Wants budget to $" . number_format($this->wants, 2) . 
            ". Already spent $" . number_format($budget->wants_progress, 2) . ".");
            return false;
        }
        if ($this->savings < ($budget->savings_progress ?? 0)) {
            session()->flash('error', 
            "Cannot set Savings budget to $" . number_format($this->savings, 2) . 
            ". Already spent $" . number_format($budget->savings_progress, 2) . ".");
            return false;
        }

        return true;
    }



    public function defaultBudget()
    {
        $this->validate([
            'income' => 'required|numeric|min:0',
        ]);

        // 50/30/20 rule
        $this->needs = $this->income * 0.50;
        $this->wants = $this->income * 0.30;
        $this->savings = $this->income * 0.20;
        if (!$this->validateBudgetAmounts()) return;

        $existingBudget = Budget::where('user_id', Auth::id())->first();
        Budget::updateOrCreate(
            ['user_id' => Auth::id()],
            [
                'income' => $this->income,
                'budgeted_needs' => $this->needs,
                'budgeted_wants' => $this->wants,
                'budgeted_savings' => $this->savings,
                'needs_progress' => $existingBudget->needs_progress,
                'wants_progress' => $existingBudget->wants_progress,
                'savings_progress' => $existingBudget->savings_progress,
                'needs_percentage' => 50,
                'wants_percentage' => 30,
                'savings_percentage' => 20,
                'remaining_balance' => $this->calculateRemainingBalance(),
            ]
        );

        session()->flash('success', 'Default Budget Created');
        $this->dispatch('refreshBudgetTable');
        $this->dispatch('refreshBudgetChart');
        $this->dispatch('refreshBudgetingChat');
    }

    public function prioritizeDebts()
    {
        $this->validate([
            'income' => 'required|numeric|min:0',
        ]);

        $this->needs = $this->income * 0.80;
        $this->wants = $this->income * 0.10;
        $this->savings = $this->income * 0.10;
        if (!$this->validateBudgetAmounts()) return;

        $existingBudget = Budget::where('user_id', Auth::id())->first();
        Budget::updateOrCreate(
            ['user_id' => Auth::id()],
            [
                'income' => $this->income,
                'budgeted_needs' => $this->needs,
                'budgeted_wants' => $this->wants,
                'budgeted_savings' => $this->savings,
                'needs_progress' => $existingBudget->needs_progress,
                'wants_progress' => $existingBudget->wants_progress,
                'savings_progress' => $existingBudget->savings_progress,
                'needs_percentage' => 80,
                'wants_percentage' => 10,
                'savings_percentage' => 10,
                'remaining_balance' => $this->calculateRemainingBalance(),
            ]
        );

        session()->flash('success', 'Debt-Prioritized Budget Created');
        $this->dispatch('refreshBudgetTable');
        $this->dispatch('refreshBudgetChart');
        $this->dispatch('refreshBudgetingChat');
    }

    public function prioritizeSavings()
    {
        $this->validate([
            'income' => 'required|numeric|min:0',
        ]);

        $this->needs = $this->income * 0.50;
        $this->wants = $this->income * 0.10;
        $this->savings = $this->income * 0.40;
        if (!$this->validateBudgetAmounts()) return;

        $existingBudget = Budget::where('user_id', Auth::id())->first();
        Budget::updateOrCreate(
            ['user_id' => Auth::id()],
            [
                'income' => $this->income,
                'budgeted_needs' => $this->needs,
                'budgeted_wants' => $this->wants,
                'budgeted_savings' => $this->savings,
                'needs_progress' => $existingBudget->needs_progress,
                'wants_progress' => $existingBudget->wants_progress,
                'savings_progress' => $existingBudget->savings_progress,
                'needs_percentage' => 50,
                'wants_percentage' => 10,
                'savings_percentage' => 40,
                'remaining_balance' => $this->calculateRemainingBalance(),
            ]
        );

        session()->flash('success', 'Savings-Prioritized Budget Created');
        $this->dispatch('refreshBudgetTable');
        $this->dispatch('refreshBudgetChart');
        $this->dispatch('refreshBudgetingChat');
    }

    public function prioritizeWants()
    {
        $this->validate([
            'income' => 'required|numeric|min:0',
        ]);
        $this->needs = $this->income * 0.50;
        $this->wants = $this->income * 0.40;
        $this->savings = $this->income * 0.10;
        if (!$this->validateBudgetAmounts()) return;

        $existingBudget = Budget::where('user_id', Auth::id())->first();
        Budget::updateOrCreate(
            ['user_id' => Auth::id()],
            [
                'income' => $this->income,
                'budgeted_needs' => $this->needs,
                'budgeted_wants' => $this->wants,
                'budgeted_savings' => $this->savings,
                'needs_progress' => $existingBudget->needs_progress,
                'wants_progress' => $existingBudget->wants_progress,
                'savings_progress' => $existingBudget->savings_progress,
                'needs_percentage' => 50,
                'wants_percentage' => 40,
                'savings_percentage' => 10,
                'remaining_balance' => $this->calculateRemainingBalance(),
            ]
        );

        session()->flash('success', 'Wants-Prioritized Budget Created');
        $this->dispatch('refreshBudgetTable');
        $this->dispatch('refreshBudgetChart');
        $this->dispatch('refreshBudgetingChat');
    }

    public function render()
    {
        return view('livewire.budget-form');
    }
}