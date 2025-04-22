<?php

namespace App\Livewire;

use Livewire\Component;
use OpenAI\Laravel\Facades\OpenAI;
use League\CommonMark\CommonMarkConverter;
use App\Services\Budgeting\BudgetFormatter;
use Illuminate\View\View;

class BudgetingChat extends Component
{
    public array $messages = [];
    public string $userInput = '';
    public bool $showQuestions = false;

    protected $listeners = ['refreshBudgetingChat' => 'refreshChat'];

    public function mount(): void {$this->initializeChat();}
    public function render(): View {return view('livewire.budgeting-chat');}

    public function refreshChat(): void {
        if (!empty($this->messages))  $this->messages[0] = ['role' => 'system', 'content' => $this->buildSystemPrompt()];
        else $this->messages[] = ['role' => 'system', 'content' => $this->buildSystemPrompt()];
    }

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

    /* Handles asking a predefined question on click */
    public function askQuestion(string $question): void {
        $this->userInput = $question;
        $this->sendMessage();
        $this->showQuestions=false;
    }

    /* Toggle the visibility of the predefined questions list */
    public function toggleQuestions(): void {$this->showQuestions = !$this->showQuestions;}

    private function initializeChat(): void {
        $this->messages[] = ['role' => 'system', 'content' => $this->buildSystemPrompt()];
        $this->messages[] = ['role' => 'assistant', 'content' => $this->convertMarkdownToHtml($this->buildInitialMessage())];
    }

    private function buildSystemPrompt(): string {
        return
            "You are an AI budgeting assistant designed to help users manage their personal fincances. " .
            "Your goal is to assist with creating, optimizing, and sticking to a budget. " .
            "You can provide advice on expense tracking, saving for goals, debt management, and income allocation. " .
            "Here is the user's financial information:\n\n" .
            (new BudgetFormatter)->generateSummary() . "\n\n" .
            "Financial goals are considered wants and are part of the user's budgeted wants. " .
            "Debts are considered needs and are part of the user's budgeted needs " .
            "Ensure that when performing calculations on financial goal amounts that you prioritize accuracy " .
            "Ask for specific details (e.g., income, expenses, debts) if needed to give tailored advice. " .
            "When explaining calculations or equations, do not use LaTeX or special formatting (e.g., \\text{}, \\frac{}, or [ ]. " .
            "Format monetary values to two decimal places and keep explanations clear and simple.";
    }

    private function buildInitialMessage(): string
    {
        return 
            "Hello! I'm your budgeting assistant here to help you manage your finances. " .
            "I can assist with budgeting advice, tracking expenses, Financial goals, or managing your debts. " .
            "What would you like to focus on today?";
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