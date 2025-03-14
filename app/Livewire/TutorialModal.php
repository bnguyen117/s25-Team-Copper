<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Livewire\Component;

class TutorialModal extends Component
{
    public $isOpen = false;
    public $step = 1;
    public $totalSteps = 6;

    /** Retrieves the user's record and opens the modal if it's their first login. */
    public function mount() 
    {
        $user = User::find(Auth::id());
        if ($user && $user->first_login) {
            $this->isOpen = true;
        }
    }

    /** Increments the step if it's less than the total steps. */
    public function nextStep() { if ($this->step < $this->totalSteps) $this->step++; }

    /** Decrements the step if it's greater than 1. */
    public function previousStep() { if ($this->step > 1) $this->step--; }

    /** Sets the user's first_login to false and closes the tutorial */
    public function close()
    {
        $user = User::find(Auth::id());
        if ($user && $user->first_login) {
            $user->update(['first_login' => false]);
        }
        $this->isOpen = false;
    }

    /** Renders the tutorial modl view. */
    public function render() { return view('livewire.tutorial-modal'); }
}