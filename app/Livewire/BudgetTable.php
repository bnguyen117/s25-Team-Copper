<?php

namespace App\Livewire;

use App\Models\Budget;
use Livewire\Component;

// Form
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;

// Table
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Panel;

// Illuminate
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class BudgetTable extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table
            ->query(Budget::where('user_id', Auth::id()))
            ->heading('Budgets')
            ->paginated(false)
            ->columns([
                Tables\Columns\TextColumn::make('income')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('expenses')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('savings')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('remaining_balance')
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                    ->button()
                    ->slideOver()
                    ->form($this->getFormFields())
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['user_id'] = Auth::id();
                        return $data;
                    }),
            ])
            ->actions([
                EditAction::make()
                    ->button()
                    ->slideOver()
                    ->form($this->getFormFields()),
                DeleteAction::make()
                    ->button(),
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
     * Note: (Thank you Darrick!)
     */
    private function getBudgetTableColumns(): array 
    {
        return
        [
            Panel::make([
                Split::make([
                    TextColumn::make('income')
                        ->numeric()
                        ->description('Monthly Income', position: 'above')
                        ->money('usd')
                        ->sortable()
                        ->weight(FontWeight::Medium),

                    TextColumn::make('expenses')
                        ->numeric()
                        ->description('Monthly Expenses', position: 'above')
                        ->money('usd')
                        ->sortable()
                        ->weight(FontWeight::Medium),

                    TextColumn::make('savings')
                        ->numeric()
                        ->description('Monthly Savings', position: 'above')
                        ->money('usd')
                        ->sortable()
                        ->weight(FontWeight::Medium),

                    // Note: Will not be user-entered after testing
                    TextColumn::make('remaining_balance')
                        ->numeric()
                        ->description('Remaining Balance', position: 'above')
                        ->money('usd')
                        ->sortable()
                        ->weight(FontWeight::Medium),

                ])->from('lg'),
            ])->collapsible(),
        ];
    }

    /**
     * Defines and returns the form fields for creating and editing debts.
     * Note: (Thank you Darrick!)
     */
    private function getFormFields(): array
    {
        return
        [
            TextInput::make('income')
                ->required()
                ->numeric()
                ->minValue(0)
                ->prefix('$'),

            TextInput::make('expenses')
                ->required()
                ->numeric()
                ->minValue(0)
                ->prefix('$'),

            TextInput::make('savings')
                ->required()
                ->numeric()
                ->minValue(0)
                ->prefix('$'),

            TextInput::make('remaining_balance')
                ->required()
                ->numeric()
                ->minValue(0)
                ->prefix('$'),
            
        ];
    }

    public function render(): View
    {
        return view('livewire.budget-table');
    }
}
