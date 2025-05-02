<?php

namespace App\Livewire;

use Livewire\Component;
use OpenAI\Laravel\Facades\OpenAI;
use League\CommonMark\CommonMarkConverter;
use App\Models\SavingsWhatIfReport;
use App\Services\WhatIf\Chat\SavingsWhatIfMessageBuilder;
use App\Services\WhatIf\Chat\SavingsWhatIfReportFormatter;
use \Illuminate\View\View;

/** A Livewire component for the AI chatbot Modal connected to SavingsWhatIfReports */

class SavingsWhatIfChatModal extends Component {
    public SavingsWhatIfReport $report; // Holds the current SavingsWhatIfReport record.
    public array $messages = []; // Holds the chat's history.
    public string $userInput = ''; // Holds the user's text input.
    public bool $showQuestions = false; // Flag for controlling visibility of questions list

    public function mount(): void  {
        this->initializeChat();
    }
    public function render(): View { 
        return view('livewire.what-if.savings-chat-interface', ['report' => $this->report]); 
    }

    // Handles sending a request to OpenAI when the user sends a message
    public function sendMessage(): void {
        // Ensures the user's input is not empty; Appends their meessages to the chat history.
        if (empty(trim($this->userInput))) return;
        $this->messages[] = ['role' => 'user', 'content' => $this->userInput];

        // Sends a request to OpenAI, appending response to chat history and clearing user input.
        $response = OpenAI::chat()->create([
            'model' => 'gpt-4o', 
            'messages' => $this->prepareMessagesForOpenAI()
        ]);
        this->messages[] = [
            'role' => 'assistant',
            'content' => $this
        ]
    }

    public function askQuestions(string $question): void {

    }

    public function toggleQuestions(): void {

    }

    private function initializeChat(): void {

    }

    private function buildSystemPrompt(SavingsWhatIfReport $report): string {

    }

    private function prepareMessagesForOpenAI(): array {

    }

    private function convertMarkdownToHtml(string $markdown): string {
        
    }
}