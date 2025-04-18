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

    public function form(Form $form): Form
    {
        return $form
            ->schema(
                [
                    TextInput::make('income')
                        ->label('Monthly Income')
                        ->type('number')
                        ->numeric()
                        ->prefix('$')
                        ->minValue(0)
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
        $this->validate([
            'income' => 'required|numeric|min:0',
        ]);

        Budget::updateOrCreate(
            ['user_id' => Auth::id()],
            [
                'income' => $this->income,
                'needs_percentage' => 0.50,
                'wants_percentage' => 0.30,
                'savings_percentage' => 0.20,
            ]
        );

        $this->mount();
    }

    public function prioritizeDebts()
    {
        $this->validate([
            'income' => 'required|numeric|min:0',
        ]);

        Budget::updateOrCreate(
            ['user_id' => Auth::id()],
            [
                'income' => $this->income,
                'needs_percentage' => 0.80,
                'wants_percentage' => 0.10,
                'savings_percentage' => 0.10,
            ]
        );

        $this->mount();
    }

    public function prioritizeSavings()
    {
        $this->validate([
            'income' => 'required|numeric|min:0',
        ]);

        Budget::updateOrCreate(
            ['user_id' => Auth::id()],
            [
                'income' => $this->income,
                'needs_percentage' => 0.50,
                'wants_percentage' => 0.10,
                'savings_percentage' => 0.40,
            ]
        );

        $this->mount();
    }

    public function prioritizeWants()
    {
        $this->validate([
            'income' => 'required|numeric|min:0',
        ]);

        Budget::updateOrCreate(
            ['user_id' => Auth::id()],
            [
                'income' => $this->income,
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
