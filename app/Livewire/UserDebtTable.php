<?php

namespace App\Livewire;

use App\Models\Debt;
use Livewire\Component;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

// Tables
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Panel;
use Filament\Tables\Table;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\BulkActionGroup;

// Forms
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;


// Filament Support
use Filament\Support\Enums\FontWeight;

// Badge Service
use App\Services\Rewards\BadgeService;

class UserDebtTable extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    protected $listeners = ['refreshDebtTable' => '$refresh'];

    public function table(Table $table): Table
    {
        return $table
            // Filter Debts to only those belonging to the current user.
            ->query(Debt::where('user_id', Auth::id()))
            ->heading('Debts')
            ->columns($this->getDebtTableColumns())
            ->paginated(false)
            ->filters([
                //
            ])
            // Actions that are placed in-line with the table's header.
            ->headerActions([
                // Create new Debts with the current user's id.
                CreateAction::make()
                    ->button()
                    ->slideOver()
                    ->form($this->getFormFields())
                    // Ensure the new debt is assigned to the currently authenicated user before saving.
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['user_id'] = Auth::id();
                        return $data;
                    })
                    ->after(function (array $data, Debt $record) {
                        //after a debt is created, call badge service to see if the action qualifies for a badge
                        app(BadgeService::class)->awardDebtPoints($record->user);
                        $this->dispatch('refreshBudgetingChat');
                        session(['debt_action_occurred' => true]);
                    }),

            ])
            ->actions([
                // Edit button for updating a record.
                EditAction::make()
                    ->button()
                    ->slideOver()
                    ->form($this->getFormFields())
                    ->after(function () {
                        $this->dispatch('refreshBudgetingChat');
                        session(['debt_action_occurred' => true]);
                    }),
                // Delete button for deleting a record.
                DeleteAction::make()
                    ->button()
                    ->after(function () {
                        $this->dispatch('refreshBudgetingChat');
                    }),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    BulkAction::make('delete')
                        ->requiresConfirmation()
                        ->action(fn (Collection $records) => $records->each->delete())
                        ->after(function () {
                            $this->dispatch('refreshBudgetingChat');
                        }),
                ]),
            ]);
    }

    /**
     * Defines and returns the table's columns as an array.
     */
    private function getDebtTableColumns(): array 
    {
        return
        [
            TextColumn::make('debt_name')
                ->searchable()
                ->size('md')
                ->weight(FontWeight::Bold),
            TextColumn::make('category')
                ->sortable()
                ->weight(FontWeight::Medium),

            // Group additional columns in a collapsible panel.
            Panel::make([
                // Ensure columns align horizontally on large screens and above.
                Split::make([
                    TextColumn::make('amount')
                        ->numeric()
                        ->description('Debt Amount', position: 'above')
                        ->money('usd')
                        ->sortable()
                        ->weight(FontWeight::Medium),

                    TextColumn::make('monthly_payment')
                        ->numeric()
                        ->description('Monthly Payment', position: 'above')
                        ->money('usd')
                        ->sortable()
                        ->weight(FontWeight::Medium),

                    TextColumn::make('interest_rate')
                        ->numeric()
                        ->description('Interest Rate', position: 'above')
                        ->suffix('%')
                        ->sortable()
                        ->weight(FontWeight::Medium),

                    TextColumn::make('minimum_payment')
                        ->numeric()
                        ->description('Minimum Payment', position: 'above')
                        ->money('usd')
                        ->sortable()
                        ->weight(FontWeight::Medium),

                    TextColumn::make('due_date')
                        ->date()
                        ->description('Due Date', position: 'above')
                        ->sortable()
                        ->weight(FontWeight::Medium),
                ])->from('lg'),
            ])->collapsible(),
        ];
    }

    /**
     * Defines and returns the form fields for creating and editing debts.
     */
    private function getFormFields(): array
    {
        return
        [
            TextInput::make('debt_name')
                ->placeholder("Your debt's name")
                ->required(),

            TextInput::make('amount')
                ->required()
                ->formatStateUsing(fn ($state) => number_format($state, 2, '.', ''))
                ->rules(['numeric', 'decimal:0,2'])
                ->placeholder('Your total debt amount')
                ->minValue(0)
                ->maxValue(99999999.99)
                ->prefix('$'),
            
            TextInput::make('monthly_payment')
                ->numeric()
                ->required()
                ->placeholder('Your planned monthly payment')
                ->minValue(0)
                ->maxValue(99999999.99)
                ->prefix('$'),

            TextInput::make('interest_rate')
                ->required()
                ->rules(['numeric', 'between:0,100', 'decimal:0,2'])
                ->placeholder('Your annual interest rate')
                ->suffix('%')
                ->minValue(0)
                ->maxValue(99.99),

            Select::make('category')
                ->required()
                ->label('Category')
                ->options([
                    'Credit Card' => 'Credit Card',
                    'Student Loan' => 'Student Loan',
                    'Auto Loan' => 'Auto Loan',
                    'Mortgage' => 'Mortgage',
                    'Personal Loan' => 'Personal Loan',
                    'Medical Debt' => 'Medical Debt',
                    'Other' => 'Other'
            ])
            ->default('Other'),

            TextInput::make('minimum_payment')
                ->required()
                ->numeric()
                ->placeholder('Your minimum required monthly payment')
                ->prefix('$')
                ->minValue(0)
                ->maxValue('99999999.99'),

            TextInput::make('description')
                ->columnSpanFull(),
                
            DatePicker::make('due_date'),
        ];
    }

    /**
     * Returns and renders the table's view.
     */
    public function render(): View
    {
        return view('livewire.user-debt-table');
    }
}