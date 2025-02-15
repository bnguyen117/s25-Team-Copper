<?php
 
namespace App\Livewire;
 
use App\Models\Debt;
use App\Models\FinancialGoal;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\View\View;
use Livewire\Component;
 
class WhatIfForm extends Component implements HasForms
{
    use InteractsWithForms;

    public $monthly_income;
    public $monthly_expenses;
    public $debt_name;
    public $financial_goal;
    public $what_if_analysis_algorithm;
    public $ai_suggestion;

    

    public function mount(): void
    {
        // Initialize the form with an empty state.
        $this->form->fill();
    }
    
    /**
     * Defines the form's field structure.
     */
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('monthly_income')
                ->type('number')
                ->required(),
                TextInput::make('monthly_expenses')
                ->type('number')
                ->required(),
                Select::make('debt_name')
                ->options(fn () => $this->getCurrentUserDebts())
                ->required(),
                Select::make('financial_goal')
                ->options(fn () => $this->getCurrentUserGoals())
                ->required(),
                Select::make('what_if_analysis_algorithm')
                ->options([
                    'Algo1' => 'What if my interest rate changes?',
                    'Algo2' => 'What if I increase my monthly payment?',
                    'Algo3' => 'What if I decrease my income?',
                ])
                ->required(),
                Toggle::make('ai_suggestion')
                ->onColor('gray')
                ->offIcon('heroicon-m-arrow-right')
                ->onIcon('heroicon-m-bolt'),
                // ...
                ]);
    }
    
    /**
     * Outputs the form's current state
     * This method is for testing purposes, and simply dumps the form's state to the screen.
     */
    public function analyze(): void
    {
        dd($this->form->getState());
    }

    /**
     * Renders the component's view.
     */
    public function render(): View
    {
        return view('livewire.what-if-form');
    }

    /**
     * Gets the current authenicated user's debts.
     */
    private function getCurrentUserDebts(): array
    {
        return Debt::where('user_id', Auth::id())   // Get debts from the currently authenicated user
                    ->pluck('debt_name', 'id')      // Eactract debt_name as value, id as key
                    ->toArray();                    // Convert to an array
    }

    /**
     * Gets the current authenicated user's goals
     */
    private function getCurrentUserGoals(): array
    {
        return FinancialGoal::where('user_id', Auth::id())  // Get debts from the currently authenicated user
                            ->pluck('goal_name', 'id')      // Eactract goal_name as value, id as key
                            ->toArray();                    // Convert to an array
    }

    /**
     * Resets the form.
     */
    private function clearForm(): void{
        $this->form->fill();
    }
}