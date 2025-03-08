<?php
 
namespace App\Livewire;
 
use App\Services\WhatIfAnalysisService;
use App\Models\WhatIfReport;
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

    // User's monthly income
    public $monthly_income;

    // User's monthly exspenses
    public $monthly_expenses;

    // Selected Debt
    public $debt_name;

    // Selected Financial Goal
    public $financial_goal;

    // Chosen WhatIf Algorithm
    public $algorithm;

    // New interest rate input for algo 1
    public $new_interest_rate;

    // New monthly payment input for algo 2
    public $new_monthly_payment;

    // Stores the result of the analysis
    public $analysis_result;

    /**
     * Initialize the form with an empty state.
     */
    public function mount(): void
    {
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

                Select::make('algorithm')                                       // A list of what what-if algorithms
                ->options([
                    'interest-rate' => 'What if my interest rate changes?',
                    'payment-change' => 'What if I change my monthly payment?',
                ])
                -> reactive()                                                   // Allows the selected algo to control visibility of other fields                      
                -> required(),

                /**
                 * Algorithm Specific Fields.
                 */
                TextInput::make('new_interest_rate')
                ->type('number')
                ->label('New Interest Rate (%)')
                ->visible(fn ($get) => $get('algorithm') === 'interest-rate')   // Only visible if algo1 is selected
                ->required(fn ($get) => $get('algorithm') === 'interest-rate')  // Only required if algo1 is selected
                ->minValue(0)
                ->maxValue(100),

                TextInput::make('new_monthly_payment')
                ->type('number')
                ->visible(fn ($get) => $get('algorithm') === 'payment-change')  // Only visible if algo2 is selected
                ->required(fn ($get) => $get('algorithm') === 'payment-change') // Only required if algo2 is selected
                ->minValue(0),                                                  
                ]);
    }
    
    /**
     * Handles submission and runs the selected algorithm
     */
    public function analyze(): void
    {
        $state = $this->form->getState();                                       // Stores the form's field info into an array
        $service = new WhatIfAnalysisService();                                 // Create a whatIfAnalysisService object

        if ($state['algorithm'] === 'interest-rate') {                          // If the chosen algorithm is algo1
            $this->analysis_result = $service->changeInterestRateScenario(      // Calls algo1 method and stores results
                $state['debt_name'],                                            
                $state['new_interest_rate'],                                   
                $state['monthly_income'],
                $state['monthly_expenses']
            );
            $this->analysis_result['algorithm'] = 'interest-rate';              // Saves the algorithm chosen within the result
        } 
        
        elseif ($state['algorithm'] === 'payment-change') {                     // If the chosen algorithm is algo2
            $this->analysis_result = $service->changeMonthlyPaymentScenario(    // Calls algo2 method and stores results
                $state['debt_name'],                                            
                $state['new_monthly_payment'],                                   
                $state['monthly_income'],
                $state['monthly_expenses']
            );
            $this->analysis_result['algorithm'] = 'payment-change';             // Saves the algorithm chosen within the result
        }

        // Save the report to the database if no error occurred.
        if (!isset($this->analysis_result['error'])) {
            WhatIfReport::create([
                'user_id' => Auth::id(),
                'debt_id' => $state['debt_name'],
                'algorithm' => $this->analysis_result['algorithm'],
                'original_amount' => $this->analysis_result['original_amount'],
                'current_payment' => $this->analysis_result['current_payment'],
                'minimum_payment' => $this->analysis_result['minimum_payment'] ?? null,
                'new_interest_rate' => $this->analysis_result['new_interest_rate'] ?? null,
                'new_payment' => $this->analysis_result['new_payment'] ?? null,
                'total_months' => $this->analysis_result['total_months'],
                'total_interest_paid' => $this->analysis_result['total_interest_paid'],
                'timeline' => $this->analysis_result['timeline'],
            ]);
        }
    }

    /**
     * Renders the component's view.
     */
    public function render(): View
    {
        return view('livewire.what-if.form', ['result' => $this->analysis_result]);// Passes analysis_result as 'result' to the view
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
     * Resets the form and report.
     */
    public function clearForm(): void{
        $this->form->fill();
        $this->analysis_result = null;
    }
}