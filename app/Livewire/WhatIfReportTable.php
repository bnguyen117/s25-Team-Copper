<?php

namespace App\Livewire;

use App\Models\WhatIfReport;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Livewire\Component;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Filament\Support\Enums\FontWeight;

class WhatIfReportTable extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table
            // Restrict the table to show only reports belonging to the current user
            ->query(WhatIfReport::where('user_id', Auth::id()))

            // Sets the table's title
            ->heading('Saved What-If Reports')

            // Loads the columns defined in getReportTableColumn()
            ->columns($this->getReportTableColumns())
            ->paginated(false)
            ->filters([])

            // Defines row specific actions like viewing or deleting a single report
            ->actions([
                // Adds a `View Report` button to open a slide-over modal with report details
                Tables\Actions\Action::make('View Report')
                    ->label('View Report')
                    ->button()
                    ->slideOver()

                    // Sets the modla header to `Debt Name - Algorithm Name Report for each report
                    ->modalHeading(fn ($record) => "{$record->debt->debt_name} - " . ucfirst($record->algorithm) . " Report")

                    // Defines the modal content by rendering a view with report data
                    ->modalContent(function ($record) {
                        $result = $record->toArray();
                        $result['debt_name'] = $record->debt->debt_name;
                        // Pass the report data to the report-modal view for display in the modal
                        return view('livewire.what-if.report-modal', ['result' => $result]);
                    })
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close'),
                
                // Adds a button to delete individual reports
                Tables\Actions\DeleteAction::make()
                    ->button()
            ])

            // Enables bulk actions like deleting multiple reports at once
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('delete')
                        ->requiresConfirmation()
                        ->action(fn (Collection $records) => $records->each->delete())
                ]),
            ]);
    }

    /**
     * Defines and returns the table's columns as an array
     */
    private function getReportTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('debt.debt_name')
                ->label('Debt')
                ->searchable()
                ->size('md')
                ->weight(FontWeight::Bold)
                // Formats the column to display as `Debt Name - Algorithm`
                ->formatStateUsing(fn ($record) => "{$record->debt->debt_name} - " . ucfirst(str_replace('-', ' ', $record->algorithm))), // Combine debt name and algorithm

            // A collapsible Panel to hold other columns
            Tables\Columns\Layout\Panel::make([
                Tables\Columns\Layout\Split::make([
                    
                    Tables\Columns\TextColumn::make('created_at')
                        ->label('Time Created')
                        ->description('Time Created', position: 'above')
                        ->sortable()
                        ->weight(FontWeight::Medium),
                        
                ])->from('lg'),
            ])->collapsible(),
        ];
    }

    /**
     * Renders the component's view.
     */
    public function render(): View
    {
        return view('livewire.what-if.report-table');
    }
}