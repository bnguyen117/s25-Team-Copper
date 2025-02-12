<?php

namespace App\Livewire;

use App\Models\Debt;
use Livewire\Component;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

// Tables
use Filament\Tables;
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

// Filament Support
use Filament\Support\Enums\FontWeight;


class UserDebtTable extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table
            ->query(Debt::where('user_id', Auth::id()))
            ->heading('Debts')
            ->columns($this->getDebtTableColumns())
            ->paginated(false)
            ->filters([
                //
            ])
            ->headerActions([ // Actions that are placed in-line with the table's header .
                CreateAction::Make('Create Debt')
                    ->button()
                    ->slideOver()
                    ->form($this->getDebtFormFields())
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['user_id'] = Auth::id();
                        return $data;
                    }),
            ])
            ->actions([ // Actions that are placed in-line with each row in the table.
                EditAction::make('edit')
                    ->button()
                    ->slideOver()
                    ->form($this->getDebtFormFields()),
                DeleteAction::make('Delete')
                    ->button()
            ])
            ->bulkActions([ // Actions that affect multiple selected rows in the table.
                BulkActionGroup::make([
                    BulkAction::make('delete')
                        ->requiresConfirmation()
                        ->action(fn (Collection $records) => $records->each->delete())
                ]),
            ]);
    }

    // Defines and returns the columns of the Debt table.
    private function getDebtTableColumns(): array 
    {
        return
        [
            TextColumn::make('debt_name')
            ->searchable()
            ->size('md')
            ->weight(FontWeight::Bold),

            Panel::make([
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
                ])->from('lg'),
            ])->collapsible(),
        ];
    }

    // Defines and returns the form fields for creating and editing debts.
    private function getDebtFormFields(): array
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

    public function render(): View
    {
        return view('livewire.user-debt-table');
    }
}