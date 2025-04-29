
<!-- Container applying a flex-column layout for chat messages and user input -->
<div class="h-full flex flex-col">
    <div class="flex-1 overflow-y-auto p-4 space-y-4 chat-messages">
        <!-- Loop through every message in the chat history -->
        @foreach ($messages as $message)
            <!-- If the message has a role of 'system' do not display it -->
            @if ($message['role'] === 'system')
            <!-- Otherwise, display the message -->
            @else
                <div class="{{ $message['role'] === 'user' ? 'text-right' : 'text-left' }}">
                    <span class="inline-block p-2 rounded-lg {{ $message['role'] === 'user' ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-800' }} chat-message">
                        {!! $message['content'] !!}
                    </span>
                </div>
            @endif
        @endforeach
    </div>

    <!-- User Input Container -->
    <div class="pt-4 border-t">
        <div class="flex flex-col sm:flex-row sm:space-x-2 space-y-2 sm:space-y-0 px-1 sm:px-0 small:max-w-lg">
                <input 
                    wire:model="userInput"
                    wire:keydown.enter.prevent="sendMessage"
                    type="text" 
                    class="w-full sm:w-[75%] p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                    placeholder="Ask me about your finances..."
                >
                <button 
                    wire:click="sendMessage" 
                    type="button" 
                    class="w-full sm:w-[12.5%] p-2 lg:p-0 bg-blue-500 text-white rounded-lg hover:bg-blue-600 disabled:bg-blue-300"
                    wire:loading.attr="disabled"
                >
                    <span wire:loading.remove wire:target="sendMessage, askQuestion">Send</span>
                    <span wire:loading wire:target="sendMessage, askQuestion">Sending...</span>
                </button>
                <button wire:click='toggleQuestions' type='button' class="shimmer-btn w-full sm:w-[12.5%] p-2 lg:p-0">
                    <span class="flex items-center justify-center">Quick Questions</span>
                </button>
        </div>

        @if ($showQuestions)
            <div class="px-2 mt-2 space-y-2">
                <button
                    wire:click="askQuestion('Tell me what you know about my financial state')"
                    type="button"
                    class="shimmer-btn p-2 w-full text-left"
                >
                    Tell me what you know about my financial state.
                </button>

                <button
                    wire:click="askQuestion('What\'s the best way to save $500 dollars in 3 months?')"
                    type="button"
                    class="shimmer-btn p-2 w-full text-left"
                >
                    What's the best way to save $500 dollars in 3 months?
                </button>
                <button
                    wire:click="askQuestion('How should I prioritize my expenses?')"
                    type="button"
                    class="shimmer-btn mb-2 p-2 w-full text-left"
                >
                    How should I prioritize my expenses?
                </button>
            </div>
        @endif
    </div>
</div>