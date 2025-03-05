<?php
 
namespace App\Livewire;
 
use App\Services\WhatIfAnalysisService;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
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
    public $what_if_algorithm;
    public $new_monthly_payment;
    public $analysis_result;

    

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
                ->type('number')                                                // Field will hold only numbers
                ->required(),                                                   // Field is required before submission

                TextInput::make('monthly_expenses')
                ->type('number')
                ->required(),

                Select::make('debt_name')
                ->options(fn () => $this->getCurrentUserDebts())                // Reference the getCurrentUserDebts method
                ->required(),

                Select::make('financial_goal')
                ->options(fn () => $this->getCurrentUserGoals())                // Reference the getCurrentUserGoals method
                ->required(),

                Select::make('what_if_algorithm')                               // A list of what what-if algorithms
                ->options([
                    'Algo1' => 'What if my interest rate changes?',
                    'Algo2' => 'What if I change my monthly payment?',
                    'Algo3' => 'What if I decrease my income?',
                ])
                -> reactive()                                                   // Allows the field's selected option to modify the form                        
                -> required(),

                TextInput::make('new_monthly_payment')
                ->type('number')
                ->visible(fn ($get) => $get('what_if_algorithm') === 'Algo2')   // Only visible if Algo2 is selected
                ->required(fn ($get) => $get('what_if_algorithm') === 'Algo2')  // Only required if Algo2 is selected
                ->minValue(0),                                                  // Input Value must be > 0
                ]);
    }
    
    /**
     * Called on form submission
     */
    public function analyze(): void
    {
        $state = $this->form->getState();                                       // Stores the form's current information in an array
        $service = new WhatIfAnalysisService();                                 // Create a whatIfAnalysisService object

        if ($state['what_if_algorithm'] === 'Algo2') {                          // Calls algo and stores the results  
            $this->analysis_result = $service->changeMonthlyPaymentScenario(
                $state['debt_name'],                                            
                $state['new_monthly_payment'],                                   
                $state['monthly_income'],
                $state['monthly_expenses']
            );
        }

        
    }

    /**
     * Renders the component's view.
     */
    public function render(): View
    {
        return view('livewire.what-if-form', ['result' => $this->analysis_result]);// Pass analysis_result as 'result'
    }

    /**
     * Gets the current authenicated user's debts.
     */
    private function getCurrentUserDebts(): array
    {
        return \App\Models\Debt::where('user_id', Auth::id())                 // Get debts from the currently authenicated user
                    ->pluck('debt_name', 'id')                                // Extract debt_name as value, id as key
                    ->toArray();                                              // Convert to an array
    }

    /**
     * Gets the current authenicated user's goals
     */
    private function getCurrentUserGoals(): array
    {
        return \App\Models\FinancialGoal::where('user_id', Auth::id())        // Get goals from the currently authenicated user
                            ->pluck('goal_name', 'id')                        // Extract goal_name as value, id as key
                            ->toArray();                                      // Convert to an array
    }

    /**
     * Resets the form.
     */
    private function clearForm(): void{
        $this->form->fill();
    }
}