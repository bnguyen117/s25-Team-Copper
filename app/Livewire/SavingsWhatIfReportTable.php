<?php

namespace App\Livewire;

use App\Models\SavingsWhatIfReport;
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

class SavingsWhatIfReportTable extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    public function table(Table $table): Table {
        return $table
            // Fetch only SavingsWhatIfReport records belonging to the currently authenticated user.
            ->query(SavingsWhatIfReport::where('user_id', Auth::id()))
            ->heading('Saved Savings What-If Reports')
            ->columns($this->getReportTableColumns())
            ->paginated(false)
            ->filters([])

            // Defines row specific actions like viewing or deleting a single report.
            ->actions([
                Tables\Actions\ActionGroup::make([
                    // An action for viewing individual SavingsWhatIfReports.
                    Tables\Actions\Action::make('View Report')
                        ->icon('heroicon-o-eye')
                        ->label('View Report')
                        ->slideOver(),
                        
                    // An action for opening an AI chatbot modal to discuss a SavingsWhatIfReport.
                    Tables\Actions\Action::make('chat')
                        ->icon('heroicon-o-chat-bubble-left-right')
                        ->label('Chat with AI')
                        ->slideOver(),

                ])
            ])

            // Enable bulk actions like deleting multiple reports at once.
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('delete')
                        ->requiresConfirmation()
                        ->action(fn (collection $records) => $records->each->delete())
                ]),
            ]);
    }

    private function getReportTableColumns(): array {
        return [
            //To do:  Add column for savings report name

            //To do:  Add collapsible panel to hold created_at date column.
        ];
    }

    // Renders the component's view
    public function render(): View {
        return view('livewire.what-if.report-table');
    }
}