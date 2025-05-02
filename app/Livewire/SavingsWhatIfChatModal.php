<?php

namespace App\Livewire;

use Livewire\Component;
use OpenAI\Laravel\Facades\OpenAI;
use League\CommonMark\CommonMarkConverter;
use App\Models\SavingsWhatIfReport;
use App\Services\WhatIf\Chat\SavingsWhatIfMessageBuilder;
use App\Services\WhatIf\Chat\SavingWhatIfReportFormatter;
use \Illuminate\View\View;

/** A Livewire component for the AI chatbot Modal connected to SavingsWhatIfReports */

class SavingsWhatIfChatModal extends Component {
    public SavingsWhatIfReport $report; // Holds the current SavingsWhatIfReport record.
    public array $messages = []; // Holds the chat's history.
    public string $userInput = ''; // Holds the user's text input.
    public bool $showQuestions = false; // Flag for controlling visibility of questions list

    public function mount(): void  {
        $this->initializeChat();
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
        $this->messages[] = [
            'role' => 'assistant',
            'content' => $this->convertMarkdownToHtml($response->choices[0]->message->content)
        ];
        $this->userInput = '';
    }

    // Handles asking predefined questions on click
    public function askQuestions(string $question): void {
        $this->userInput = $question;
        $this->sendMessage();
        $this->showQuestions=false;
    }

    // Toggle the visibility of the predefined questions list
    public function toggleQuestions(): void {
        $this->showQuestions = !$this->showQuestions;
    }

    // Initialize the chat history with a system prompt and pre-made welcome message.
    private function initializeChat(): void {
         // Append the system prompt and the initial chat message to the chat history.
         $this->messages[] = ['role' => 'system', 'content' => $this->buildSystemPrompt($this->report)];
         $this->messages[] = ['role' => 'assistant', 'content' =>
         $this->convertMarkdownToHtml((new SavingsWhatIfMessageBuilder)->buildInitialMessage($this->report))];
    }

    private function buildSystemPrompt(SavingsWhatIfReport $report): string {
        $prompt =  "You are an AI financial advisor. Here is the user's Savings What-If Report data:\n\n" .
        (new SavingWhatIfReportFormatter)->generateSummary($report) . "\n\n" .
        "This report uses the '" . $report->what_if_scenario . "' algorithm.\n" .
        "If the algorithm is 'savings-change', 'total_months' and 'total_interest_earned' reflect the 'new_savings_change' scenario.\n" .
        "If the algorithm is 'saving-interest-rate-change', 'total_months' and 'total_interest_earned' reflect the 'new_interest_rate' scenario.\n" .
        "Assist the user by providing accurate financial advice based on this data.\n" .
        "Clarify assumptions when needed, and format monetary values to two decimal places.\n" .
        "When explaining calculations or equations, do not use LaTeX or special formatting (e.g., \\text{}, \\frac{}, or [ ].";

        return $prompt;
    }

    private function prepareMessagesForOpenAI(): array {
        return array_map(function ($message) {
            return [
                'role' => $message['role'],
                'content' => $message['content']
            ];
        }, $this->messages);
    }

    private function convertMarkdownToHtml(string $markdown): string {
        return (new CommonMarkConverter())->convert($markdown)->getContent();
    }
}