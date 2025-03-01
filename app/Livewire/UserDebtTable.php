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


class UserDebtTable extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

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
                    }),
            ])
            ->actions([
                // Edit button for updating a record.
                EditAction::make()
                    ->button()
                    ->slideOver()
                    ->form($this->getFormFields()),
                // Delete button for deleting a record.
                DeleteAction::make()
                    ->button()
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    BulkAction::make('delete')
                        ->requiresConfirmation()
                        ->action(fn (Collection $records) => $records->each->delete())
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

                    TextColumn::make('category')
                        ->label('Category')
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
                ->required(),

            TextInput::make('amount')
                ->required()
                ->numeric()
                ->minValue(0)
                ->maxValue(99999999.99)
                ->prefix('$'),

            TextInput::make('interest_rate')
                ->required()
                ->numeric()
                ->suffix('%')
                ->minValue(0)
                ->maxValue(99.99)
                ->rule('decimal:2'),

            Select::make('category') // Dropdown Added Here
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
                ->numeric()
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