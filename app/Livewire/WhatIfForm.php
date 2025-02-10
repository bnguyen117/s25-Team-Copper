<?php
 
namespace App\Livewire;
 
use App\Models\Post;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Illuminate\Contracts\View\View;
use Livewire\Component;
 
class WhatIfForm extends Component implements HasForms
{
    use InteractsWithForms;
    
    public ?array $data = [];
    
    public function mount(): void
    {
        // Initialize the form with an empty state.
        $this->form->fill();
    }
    
    /**
     * Defines the form's field structure
     * 
     * Add, remove, or modify form fields as needed in the form's schema.
     */
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('Monthly Income')
                ->type('number')
                ->required()
                ->placeholder('Note! This form is not yet functional.'),
                TextInput::make('Monthly Expenses')->type('number')->required(),
                Select::make('Debt')
                ->options([
                    'Debt1' => 'Student Loan',
                    'Debt2' => 'Credit Card',
                    'Debt3' => 'Car',
                ])
                ->required(),
                Select::make('Financial Goal')
                ->options([
                    'Goal1' => 'Save for a new car',
                    'Goal2' => '$10,000 in savings',
                    'Goal3' => 'Purchase ring',
                ])
                ->required(),
                Select::make('What-If Analysis Algorithm')
                ->options([
                    'Algo1' => 'What if my interest rate changes?',
                    'Algo2' => 'What if I increase my monthly payment?',
                    'Algo3' => 'What if I decrease my income?',
                ])
                ->required(),
                Toggle::make('Ai Suggestion?')
                ->onColor('gray')
                ->offIcon('heroicon-m-arrow-right')
                ->onIcon('heroicon-m-bolt'),
                // ...
            ])->statePath('data');
    }
    
    /**
     * Outputs the form's current state
     * 
     * This method is for testing purposes, and simply dumps the form's state to the screen.
     */
    public function analyze(): void
    {
        dd($this->form->getState());
    }

    /**
     * Resets the form.
     */
    public function clearForm(): void{
        $this->form->fill();
    }
    
    /**
     * Renders the component's view.
     */
    public function render(): View
    {
        return view('livewire.what-if-form');
    }
}