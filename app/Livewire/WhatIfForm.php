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

    public $monthly_income;         // User's monthly income
    public $monthly_expenses;       // User's monthly exspenses
    public $debt_name;              // Selected Debt
    public $financial_goal;         // Selected Financial Goal
    public $algorithm;              // User's chosen WhatIf algorithm
    public $new_interest_rate;      // New interest rate input for 'interest-rate-change' algo
    public $new_monthly_payment;    // New monthly payment input for 'payment-change' algo
    public $report;                 // Stores the result of a what-if analysis algorithm.


    public function mount(): void
    {
        $this->clearForm();                                                    // Initialize the form with an empty state.
    }
    
    public function form(Form $form): Form                                     // Defines the form's field structure.
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
                ->options(fn () => $this->getCurrentUserDebts())                // Returns the getCurrentUserDebts() method.
                ->required(),

                Select::make('financial_goal')
                ->options(fn () => $this->getCurrentUserGoals())                // Returns the getCurrentUserGoals() method.
                ->required(),

                Select::make('algorithm')                                       // A list of what what-if analysis algorithms.
                ->options([
                    'interest-rate-change' => 'What if my interest rate changes?',
                    'payment-change' => 'What if I change my monthly payment?',
                ])
                -> reactive()                                                   // Allows the selected algo to control visibility of other fields.                      
                -> required(),

                /**
                 * Algorithm Specific Fields.
                 */
                TextInput::make('new_interest_rate')
                ->type('number')
                ->label('New Interest Rate (%)')
                ->visible(fn ($get) => $get('algorithm') === 'interest-rate-change')   // Only visible if interest-rate-change is selected
                ->required(fn ($get) => $get('algorithm') === 'interest-rate-change')  // Only required if interest-rate-change is selected
                ->minValue(0)
                ->maxValue(100),

                TextInput::make('new_monthly_payment')
                ->type('number')
                ->visible(fn ($get) => $get('algorithm') === 'payment-change')  // Only visible if payment-change is selected
                ->required(fn ($get) => $get('algorithm') === 'payment-change') // Only required if payment-change is selected
                ->minValue(0),                                                  
                ]);
    }
    
    /**
     * Handles submission and runs the selected algorithm
     */
    public function analyze(): void
    {
        $state = $this->form->getState();                                 // Stores the form's field information as an array.
        $service = new WhatIfAnalysisService();                           // Create a 'whatIfAnalysisService' object

        if ($state['algorithm'] === 'interest-rate-change') {             // If the chosen algorithm is 'interest-rate-change'
            $result = $service->interestRateChangeScenario(               // Call the corresponding algo method and store 'result'
                $state['debt_name'],                                            
                $state['new_interest_rate'],                                   
                $state['monthly_income'],
                $state['monthly_expenses']
            );
        } 
        
        elseif ($state['algorithm'] === 'payment-change') {               // If the chosen algorithm is 'payment-change'
            $result = $service->changeMonthlyPaymentScenario(             // Call the corresponding algo method and store 'result'
                $state['debt_name'],                                            
                $state['new_monthly_payment'],                                   
                $state['monthly_income'],
                $state['monthly_expenses']
            );
        }

        // Save the report to the database if no error occurred.
        if (isset($result['error'])) {
            $this->report = $result;                                      // Store the error
        } else {
            $this->report = WhatIfReport::create([                        // Save the report to the database and store a copy in $this->report.
                'user_id' => Auth::id(),
                'debt_id' => $state['debt_name'],
                'algorithm' => $state['algorithm'],
                'original_amount' => $result['original_amount'],
                'current_payment' => $result['current_payment'],
                'minimum_payment' => $result['minimum_payment'] ?? null,
                'new_interest_rate' => $result['new_interest_rate'] ?? null,
                'new_payment' => $result['new_payment'] ?? null,
                'total_months' => $result['total_months'],
                'total_interest_paid' => $result['total_interest_paid'],
                'timeline' => $result['timeline'],
            ]);
        }
    }

    /**
     * Pass the resulting analysis report as 'report' and render the component's view.
     */
    public function render(): View
    {
        return view('livewire.what-if.form', ['report' => $this->report]);
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
        $this->form->fill();                                                  // Clear the form's state.
        $this->report = null;                                                 // Clear the reports data.
    }
}