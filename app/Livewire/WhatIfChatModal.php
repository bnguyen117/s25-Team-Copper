<?php

namespace App\Livewire;

use Livewire\Component;
use OpenAI\Laravel\Facades\OpenAI;
use League\CommonMark\CommonMarkConverter;
use App\Models\WhatIfReport;

/**
 * A Livewire component that provides the AI-driven chatbot interface for What-If Reports
 */
class WhatIfChatModal extends Component
{
    /** 
     * The What-If Report record passed from the WhatIfReportTable. 
     * An array to hold the chat messages.
     * User input from the chatbox.
     */
    public $report;
    public array $messages = [];
    public string $userInput = '';


    public function mount(WhatIfReport $report): void
    {
        // Initialize the given WhatIfReport within this component
        $this->report = $report;

        // Converter object to convert Markdown responses returned by OpenAI to HTML.
        $converter = new CommonMarkConverter();

        // Generate a report summary for the system prompt
        $reportSummary = $this->generateReportSummary();

        /**
         * A System prompt that provides context to the chatbot about the report and algorithm.
         * Not shown to the user.
         */
        $systemPrompt = 
            "You are an AI financial advisor. Here is the user's What-If Report data:\n\n" .
            "$reportSummary\n" .
            "This report uses the '{$this->report->what_if_scenario}' algorithm. " .
            "If the algorithm is 'payment-change', 'total_months' and 'total_interest_paid' reflect the 'new_payment' scenario, not the 'current_payment'. " .
            "If the algorithm is 'interest-rate-change', 'total_months' and 'total_interest_paid' reflect the 'new_interest_rate' scenario. " .
            "Assist the user by providing accurate financial advice based on this data. Clarify assumptions when needed, and format monetary values to two decimal places in responses.";
        
        
        // Append the system prompt to the messages array.
        $this->messages[] = [
            'role' => 'system',
            'content' => $systemPrompt,
        ];

        // Append the Initial message the user sees to the messages array.
        $this->messages[] = [
            'role' => 'assistant',
            'content' => $converter->convert($this->buildInitialMessage())->getContent(),
        ];
    }

    /**
     * Makes a request to OpenAI using the user's input message
     * and appends the bot's response to the messages array.
     */
    public function sendMessage(): void
    {
        // Do not make a request to OpenAI if the user's input is empty.
        if (empty(trim($this->userInput))) {
            return;
        }

        // Add user's message to the messages array.
        $this->messages[] = [
            'role' => 'user',
            'content' => $this->userInput,
        ];

        /**
         * Strip away HTML formatting from messages in the messages array.
         * Store in $openAiMessages.
         * 
         * This is important since OpenAI expects either
         * plain text or Markdown in a request.
         */
        $openAiMessages = array_map(function (array $message): array {
            return [
                'role' => $message['role'],
                'content' => strip_tags($message['content']),
            ];
        }, $this->messages);


        /**
         * Send a chat request to OpenAI with the HTML stripped messages.
         * We pass the whole chat history so OpenAI has the full context of the conversation.
         * Store respone in $response.
         */
        $response = OpenAI::chat()->create([
            'model' => 'gpt-4o-mini',
            'messages' => $openAiMessages,
        ]);

        /** Convert the AI's Markdown response to HTML and append it to messages
         *  
         * This is important since the app is not setup to display raw Markdown
         * Converting the responses to HTML allows the user to see the proper formating returned by the bot.
        */
        $converter = new CommonMarkConverter();
        $this->messages[] = [
            'role' => 'assistant',
            'content' => $converter->convert($response->choices[0]->message->content)->getContent(),
        ];

        // Clear the user's input field after submission
        $this->userInput = '';
    }


    /**
     * Builds a string summary of the $report for the system prompt.
     * 
     * The purpose of this reportSummary is to give the bot data and
     * context on the specific report it will be disccussing.
     */
    private function generateReportSummary(): string
    {
        $report = $this->report;
        $summary = 
            "Debt Name: {$report->debt->debt_name}\n" .
            "What-If Scenario Algorithm: {$report->what_if_scenario}\n" .
            "Original Debt Amount: \$" . number_format($report->original_debt_amount, 2) . "\n" .
            "Current Monthly Debt Payment: \$" . number_format($report->current_monthly_debt_payment, 2) . "\n" .
            "Total Months Until Full Repayment: {$report->total_months}\n" .
            "Total Interest Paid: \$" . number_format($report->total_interest_paid, 2) . "\n";

        if ($report->minimum_monthly_debt_payment) {
            $summary .= "Minimum Monthly Debt Payment: \$" . number_format($report->minimum_monthly_debt_payment, 2) . "\n";
        }
        if ($report->new_interest_rate) {
            $summary .= "New Interest Rate: " . number_format($report->new_interest_rate, 2) . "%\n";
        }
        if ($report->new_monthly_debt_payment) {
            $summary .= "New Monthly Debt Payment: \$" . number_format($report->new_monthly_debt_payment, 2) . "\n";

        }

        return $summary;
    }

    /**
     * Returns the initial message the user sees from the bot.
     * Customized based on the scenario within $report.
     */
    private function buildInitialMessage(): string
    {
        // Non-scenrio specific introduction.
        $message =
            "Hello! I'm here to help with your financial planning based on your What-If Report " .
            "for **{$this->report->debt->debt_name}** using the **{$this->report->what_if_scenario}** scenario. " .
            "Here's your report summary:\n\n";

        // Extended for payment-change scenarios.
        if ($this->report->what_if_scenario === 'payment-change') {

            $message .=
                "**Original Debt Details**\n" .
                "- **Monthly Payment**: \${$this->report->current_monthly_debt_payment}/month\n" .
                "- **Debt Amount**: \$" . number_format($this->report->original_debt_amount, 2) . "\n" .
                "- **Interest Rate**: " . number_format($this->report->debt->interest_rate ?? 0, 2) . "%\n\n" .

                "**New Payment Details**\n" .
                "- **New Monthly Payment**: \${$this->report->new_monthly_debt_payment}/month\n" .
                "- **Total Months to Pay Off**: {$this->report->total_months}\n" .
                "- **Total Interest Paid** \$: " . number_format($this->report->total_interest_paid, 2) . "$\n";
        } 

        // Extended for interest-rate-change scenarios.
        elseif ($this->report->what_if_scenario === 'interest-rate-change') {

            $message .=
                "**Original Debt Details**\n" .
                "- **Interest Rate**: " . number_format($this->report->debt->interest_rate ?? 0, 2) . "%\n" .
                "- **Monthly Payment**: \${$this->report->current_monthly_debt_payment}/month\n" .
                "- **Debt Amount**: \$" . number_format($this->report->original_debt_amount, 2) . "\n\n" .

                "New Interest Rate Impact Details" .
                "- **New Interest Rate**: {$this->report->new_interest_rate}%\n" .
                "- **Total Months to Pay Off**: {$this->report->total_months}\n" .
                "- **Total Interest Paid** \$: " . number_format($this->report->total_interest_paid, 2) . "\n";
        }
       
        // Ending line.
        $message .= "\nHow can I assist you with this report today?";

        return $message;
    }

    /**
     * Renders the chat interface view.
     */
    public function render(): \Illuminate\View\View
    {
        return view('livewire.what-if.chat-interface');
    }
}