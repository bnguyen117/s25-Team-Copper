<?php

namespace App\Livewire;

use App\Models\Debt;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Livewire\Component;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class DebtForm extends Component implements HasForms
{
    use InteractsWithForms;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('debt_name')
                    ->required()
                    ->maxLength(50)
                    ->placeholder('Your debt\'s name'),
                Forms\Components\TextInput::make('amount')
                    ->required()
                    ->numeric()
                    ->prefix('$')
                    ->placeholder('Your debt amount')
                    ->minValue(0)
                    ->maxValue(99999999.99),
                Forms\Components\TextInput::make('interest_rate')
                    ->required()
                    ->numeric()
                    ->suffix('%')
                    ->placeholder('Your debt\'s interest rate')
                    ->minValue(0)
                    ->maxValue(99.99)
                    ->rule('decimal:2'),
                Forms\Components\TextInput::make('minimum_payment')
                    ->numeric()
                    ->prefix('$')
                    ->placeholder('Your debt\'s minimum payment')
                    ->minValue(0)
                    ->maxValue(99999999.99),
                Forms\Components\Textarea::make('description')
                    ->columnSpanFull(),
                Forms\Components\DatePicker::make('due_date'),
            ])
            ->statePath('data')
            ->model(Debt::class);
    }

    /**
     * Defines what should happen on form submission.
     */
    public function create(): void
    {
        // Get the form's state and store it inside of $data.
        $data = $this->form->getState();

        // Set the user_id foreign key to the current user's primary key.
        $data['user_id'] = Auth::id();

        // Create a new Debt record with the passed form state.
        $record = Debt::create($data);

        $this->form->model($record)->saveRelationships();

        // Clear the form's state after submission.
        $this->form->fill([]);

        // Send notification.
        Notification::make()
            ->title('Debt Saved!')
            ->icon('heroicon-o-document-text')
            ->iconColor('gray')
            ->send();
    }

    /**
     * Resets the form.
     * Is tied to the clear form button in this form's view file.
     */
    public function clearForm(): void
    {
        $this->form->fill([]);
    }

    /**
     * Renders this form to the page.
     */
    public function render(): View
    {
        return view('livewire.debt-form');
    }
}
