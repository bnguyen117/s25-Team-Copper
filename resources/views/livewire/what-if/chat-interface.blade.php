<!-- 
    Renders the AI chat interface for the slide-over modal, triggered by 'Chat with AI'
    in the WhatIfReport table. Used by the WhatIfChatModal component to show bot messages and user input.
-->

<!-- Container applying a flex-column layout for chat messages and user input -->
<div class="h-full flex flex-col">

    <!-- Chat Messages Container with scrolling functionality -->
    <div class="flex-1 overflow-y-auto p-4 space-y-4 chat-messages">

        <!-- Loop through every message in the chat history -->
        @foreach ($messages as $message)

            <!-- If the message has a role of 'system' do not display it -->
            @if ($message['role'] === 'system')

            <!-- Otherwise, display the message -->
            @else

                <!-- Set user messages to the right and bot messages to the left -->
                <div class="{{ $message['role'] === 'user' ? 'text-right' : 'text-left' }}">

                    <!-- 
                        User Messages: blue background, white text
                        Bot Messages: light-gray background, dark-gray text 
                    -->
                    <span class="inline-block p-2 rounded-lg {{ $message['role'] === 'user' ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-800' }} chat-message">
                        {!! $message['content'] !!}
                    </span>

                </div>
            @endif
        @endforeach
    </div>

    <!-- User Input Container -->
    <div class="p-4 border-t">

        <!-- Flex container for side-by-side input field and send button -->
        <div class="flex space-x-2">

            <!-- Text input field for user messages -->
            <input 
                wire:model="userInput"
                wire:keydown.enter.prevent="sendMessage"
                type="text" 
                class="flex-1 p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                placeholder="Ask me anything about your report..."
            >

            <!-- Submission button for the user to submit their messages -->
            <button 
                wire:click="sendMessage" 
                type="button" 
                class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 disabled:bg-blue-300"
                wire:loading.attr="disabled"
            >
                <span wire:loading.remove>Send</span>
                <span wire:loading>Sending...</span>
            </button>

        </div>
    </div>
</div>