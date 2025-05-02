<?php

namespace App\Livewire;

use App\Models\FinancialGoal;
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
use Filament\Forms\Components\Select;

// Filament Support
use Filament\Support\Enums\FontWeight;


class UserGoalTable extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table
            // Filter goals to only those belonging to the current user.
            ->query(FinancialGoal::where('user_id', Auth::id()))
            ->heading('Financial Goals')
            ->columns($this->getGoalTableColumns())
            ->paginated(false)
            ->filters([
                //
            ])
            ->headerActions([
                // Create goals with the current user's ID.
                CreateAction::make()
                    ->button()
                    ->slideOver()
                    ->form($this->getFormFields(false))
                    // Ensure the new goal is assigned to the currently authenicated user before saving.
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['user_id'] = Auth::id();
                        return $data;
                    })
                    ->after(function () {
                        $this->dispatch('refreshBudgetingChat');
                    }),
            ])
            ->actions([
                // Edit button for updating a record.
                EditAction::make()
                    ->button()
                    ->slideOver()
                    ->form($this->getFormFields(true))
                    ->after(function () {
                        $this->dispatch('refreshBudgetingChat');
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
                    // Bulk action to delete all selected records.
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
    private function getGoalTableColumns(): array {
        return
        [
            TextColumn::make('goal_name')
                ->searchable()
                ->size('md')
                ->wrap()
                ->weight(FontWeight::Bold),

                // Group additional columns in a collapsible panel.
                Panel::make([
                    // Ensure columns align horizontally on large screens and above.
                    Split::make([
                        TextColumn::make('target_amount')
                            ->numeric()
                            ->description('Target', position: 'above')
                            ->money('usd')
                            ->sortable()
                            ->weight(FontWeight::Medium),

                        TextColumn::make('current_amount')
                            ->numeric()
                            ->description('Current', position: 'above')
                            ->money('usd')
                            ->sortable()
                            ->weight(FontWeight::Medium),

                        TextColumn::make('priority')
                            ->description('Priority', position: 'above')
                            ->sortable()
                            ->searchable(),

                        TextColumn::make('status')
                            ->description('Status', position: 'above')
                            ->sortable()
                            ->searchable(),

                        TextColumn::make('achieve_by')
                            ->description('Achieve By', position: 'above')
                            ->date()
                            ->sortable(),
                            
                    ])->from('lg'),
                ])->collapsible(),
        ];
    }

    /**
     * Defines and returns the form fields for creating and editing goals.
     */
    private function getFormFields(bool $isEdit): array{
        // Define the fields for creating or editing a financial goal
        $fields = [
            TextInput::make('goal_name')
                ->maxLength(50)
                ->required(),

            TextInput::make('target_amount')
                ->required()
                ->numeric()
                ->placeholder('How much do you need to save?')
                ->minValue(0)
                ->maxValue(99999999.99)
                ->prefix('$'),

            TextInput::make('current_amount')
                ->required()
                ->numeric()
                ->placeholder('How much do you currently have?')
                ->minValue(0)
                ->maxValue(99999999.99)
                ->prefix('$'),

            Select::make('priority')
                ->required()
                ->placeholder('How urgent is this goal?')
                ->options([
                    'low' => 'Low',
                    'medium' => 'Medium',
                    'high' => 'High',
                ]),

            TextInput::make('description')
                ->columnSpanFull(),

            DatePicker::make('achieve_by')
                ->required(),
        ];

        // If editing an existing goal, allow users to modify their status.
        if ($isEdit) {
            $fields[] = Select::make('status')
            ->required()
            ->options([
                'active' => 'Active',
                'completed' => 'Completed',
                'abandoned' => 'Abandoned',
            ]);
        }
        return $fields;
    }

     /**
     * Returns and renders the table's view.
     */
    public function render(): View
    {
        return view('livewire.user-goal-table');
    }
}
