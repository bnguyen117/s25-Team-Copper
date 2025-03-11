
<!-- 
    Renders the chat interface for the slide-over model displayed when clicking
    the 'Chat with AI' button on a WhatIfReport record. 
    Used by the WhatIfChatModal component to display bot messages and user input.
-->
<div class="h-full flex flex-col">

    <!-- Chat Messages -->
    <div class="flex-1 overflow-y-auto p-4 space-y-4">
        @foreach ($messages as $message)
            @if ($message['role'] === 'system')
            @else
                <div class="{{ $message['role'] === 'user' ? 'text-right' : 'text-left' }}">
                    <span class="inline-block p-2 rounded-lg {{ $message['role'] === 'user' ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-800' }} chat-message">
                        {!! $message['content'] !!}
                    </span>
                </div>
            @endif
        @endforeach
    </div>

    <!-- Input Form -->
    <div class="p-4 border-t">
        <div class="flex space-x-2">
            <input 
                wire:model="userInput" 
                type="text" 
                class="flex-1 p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                placeholder="Ask me anything about your report..."
            >
            <button 
                wire:click="sendMessage" 
                type="button" 
                class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600"
            >
                Send
            </button>
        </div>
    </div>
</div>