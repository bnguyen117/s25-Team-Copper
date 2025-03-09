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
            // Fetch only WhatIfReport records belonging to the currently authenticated user.
            ->query(WhatIfReport::where('user_id', Auth::id()))
            ->heading('Saved What-If Reports')
            ->columns($this->getReportTableColumns())
            ->paginated(false)
            ->filters([])

            // Defines row specific actions like viewing or deleting a single report.
            ->actions([

                // An action for viewing individual WhatIfReports.
                Tables\Actions\Action::make('View Report')
                    ->button()
                    ->label('View Report')
                    ->slideOver()
                    ->modalHeading(fn ($record) => "{$record->debt->debt_name} - " . ucfirst($record->algorithm) . " Report")
                    ->modalContent(function ($record) {
                        return view('livewire.what-if.report-modal', ['report' => $record]);
                    })
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close'),

                // An action for opening an AI chatbot modal to discuss a WhatIfReport.
                Tables\Actions\Action::make('chat')
                    ->label('Chat with AI')
                    ->button()
                    ->slideOver()
                    ->modalHeading(fn ($record) => "AI Advisor for {$record->debt->debt_name} - " . ucfirst($record->algorithm))
                    ->modalContent(function ($record) {
                        return view('livewire.what-if.chat-modal', ['report' => $record]);
                    })
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close'),
                

                // A delete action for deleting individual WhatIFReports.
                Tables\Actions\DeleteAction::make()
                    ->button()
            ])

            // Enables bulk actions like deleting multiple reports at once.
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
                ->formatStateUsing(fn ($record) => "{$record->debt->debt_name} - " . ucfirst(str_replace('-', ' ', $record->algorithm))),

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