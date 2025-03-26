<?php

namespace App\Livewire;

use Livewire\Component;
use OpenAI\Laravel\Facades\OpenAI;
use League\CommonMark\CommonMarkConverter;
use App\Models\WhatIfReport;
use App\Services\WhatIf\Chat\WhatIfMessageBuilder;
use App\Services\WhatIf\Chat\WhatIfReportFormatter;
use \Illuminate\View\View;

/** A Livewire component for the AI chatbot Modal connected to WhatIfReports. */
class WhatIfChatModal extends Component
{
    public WhatIfReport $report;    // Holds the current WhatIfReport record.
    public array $messages = [];    // Holds the chat's history.
    public string $userInput = '';  // Holds the user's text input.


    public function mount(): void { $this->initializeChat(); }
    public function render(): View { return view('livewire.what-if.chat-interface'); }


    /** Handles sending a request to OpenAI when the user sends a message. */
    public function sendMessage(): void
    {
        // Ensure the user's input is not empty; Append their message to the chat history.
        if (empty(trim($this->userInput)))  return;
        $this->messages[] = ['role' => 'user', 'content' => $this->userInput];

        // Send a request to OpenAI; Append response to the chat history; Clear user input.
        $response = OpenAI::chat()->create(['model' => 'gpt-4o', 'messages' => $this->prepareMessagesForOpenAI()]);
        $this->messages[] = ['role' => 'assistant', 'content' => $this->convertMarkdownToHtml($response->choices[0]->message->content)];
        $this->userInput = '';
    }


    /** Initialize the chat history with a system prompt and welcome message. */
    private function initializeChat(): void
    {   
        // Append the system prompt and the initial chat message to the chat history.
        $this->messages[] = ['role' => 'system', 'content' => $this->buildSystemPrompt($this->report)];
        $this->messages[] = ['role' => 'assistant', 'content' =>
        $this->convertMarkdownToHtml((new WhatIfMessageBuilder)->buildInitialMessage($this->report))];
    }


    /**
     * Builds the system prompt.
     * Gives the bot context on the user's financial situation and the current WhatIfReport.
     */
    private function buildSystemPrompt(WhatIfReport $report): string
    {
        if ($report->analysis_type == 'debt') {
            $prompt =  "You are an AI financial advisor. Here is the user's What-If Report data:\n\n" .
            (new WhatIfReportFormatter)->generateSummary($report) . "\n\n" .
            "This report uses the '" . $report->debt_what_if_scenario . "' algorithm.\n" .
            "If the algorithm is 'payment-change', 'total_months' and 'total_interest_paid' reflect the 'new_payment' scenario.\n" .
            "If the algorithm is 'interest-rate-change', 'total_months' and 'total_interest_paid' reflect the 'new_interest_rate' scenario.\n" .
            "Assist the user by providing accurate financial advice based on this data.\n" .
            "Clarify assumptions when needed, and format monetary values to two decimal places.\n" .
            "When explaining calculations or equations, do not use LaTeX or special formatting (e.g., \\text{}, \\frac{}, or [ ].";
        }
        elseif ($report->analysis_type == 'savings') {
            $prompt = "You are an AI financial advisor. Here is the user's What-If Report data:\n\n" .
            (new WhatIfReportFormatter)->generateSummary($report) . "\n\n" .
            "This report uses the '" . $report->savings_what_if_scenario . "' algorithm.\n" .
            "If the algorithm is 'interest-rate-change', 'total_saved' and 'total_interest_earned' reflect the 'new_interest_rate' scenario.\n" .
            "If the algorithm is 'savings-change', 'total_saved' and 'total_interest_earned' reflect the 'new_monthly_savings' scenario.\n" .
            "Assist the user by providing accurate financial advice based on this data.\n" .
            "Clarify assumptions when needed, and format monetary values to two decimal places.\n" .
            "When explaining calculations or equations, do not use LaTeX or special formatting (e.g., \\text{}, \\frac{}, or [ ].";
        }
        return $prompt;
    }

    
    /**
     * Preps the chat history for OpenAI.
     * Strips HTML tags from the 'content' of messages and returns the modified chat history.
     */
    private function prepareMessagesForOpenAI(): array
    {
        return array_map(fn(array $message): array => [
            'role' => $message['role'],
            'content' => strip_tags($message['content'])],
            $this->messages);
    }

    
    /** Convert Markdown to HTML. */
    private function convertMarkdownToHtml(string $markdown): string
    {
        return (new CommonMarkConverter())->convert($markdown)->getContent();
    }
}