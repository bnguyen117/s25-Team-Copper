<div class="ml-{{ $message->parent_id ? '8' : '0' }} mb-6 pl-4 {{ $message->parent_id ? 'bg-gray-700/30 rounded-md' : 'border-l-2 border-gray-300' }}">
    <div class="flex items-start space-x-4 text-sm {{ $message->parent_id ? 'opacity-90 scale-[0.95]' : 'text-base' }}">
        @if($message->user->avatar)
            <img src="{{ asset('storage/' . $message->user->avatar) }}" class="w-10 h-10 rounded-full" alt="User Avatar">
        @else
            <svg class="w-10 h-10 text-gray-300" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 2a5 5 0 015 5v1a5 5 0 11-10 0V7a5 5 0 015-5zm0 14c-5.523 0-10 4.477-10 10h20c0-5.523-4.477-10-10-10z"/>
            </svg>
        @endif

        <div class="w-full">
            <p class="font-semibold text-gray-900 dark:text-white">{{ $message->user->display_name }}</p>
            <p class="text-sm text-gray-500">{{ $message->created_at->diffForHumans() }}</p>

            <!-- Inline Edit -->
            @if (request('edit') == $message->id && $message->user_id === auth()->id())
                <form method="POST" action="{{ route('messages.update', [$group->id, $message->id]) }}" class="mt-2">
                    @csrf
                    @method('PUT')
                    <textarea name="body" class="w-full p-2 rounded border dark:bg-gray-700 dark:text-white" required>{{ $message->body }}</textarea>
                    <div class="flex gap-2 mt-2">
                        <x-primary-button>Save</x-primary-button>
                        <a href="{{ route('groups.show', $group->id) }}" class="text-sm text-gray-400 hover:text-gray-200">Cancel</a>
                    </div>
                </form>
            @else
                <p class="mt-2 text-gray-800 dark:text-gray-300">{{ $message->body }}</p>
            @endif

            @if ($message->parent_id)
                <p class="text-xs text-gray-400 italic">↪ replying to {{ $message->parent->user->display_name ?? 'message' }}</p>
            @endif

            <!-- Reply Form second try -->
            @if (!(request('edit') == $message->id && $message->user_id === auth()->id()))
                <form method="POST" action="{{ route('messages.store', $group->id) }}" class="mt-2">
                    @csrf
                    <input type="hidden" name="parent_id" value="{{ $message->id }}">
                    <textarea name="body" rows="2" class="w-full p-2 rounded border dark:bg-gray-700 dark:text-white" placeholder="Reply..." required></textarea>
                    <div class="flex justify-end mt-2">
                        <x-primary-button>Reply</x-primary-button>
                    </div>
                </form>
            @endif

            <!-- Edit/Delete -->
            @if($message->user_id === auth()->id() && request('edit') !== $message->id)
                <div class="flex gap-4 mt-2 text-sm text-gray-500">
                    <a href="{{ route('groups.show', ['group' => $group->id, 'edit' => $message->id]) }}" class="text-blue-500 hover:underline">✏️ Edit</a>
                    <form method="POST" action="{{ route('messages.destroy', [$group->id, $message->id]) }}" onsubmit="return confirm('Delete this message?')" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-500 hover:text-red-700">🗑️ Delete</button>
                    </form>
                </div>
            @endif

            <!-- Replies -->
            <div class="mt-4 space-y-4">
                @foreach ($message->replies as $reply)
                    @include('components.message', ['message' => $reply, 'group' => $group])
                @endforeach
            </div>
        </div>
    </div>
</div>
