<x-app-layout>
<x-slot name="header">
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            @if ($group->image)
                <img src="{{ asset('storage/' . $group->image) }}" alt="Group Avatar" class="w-12 h-12 rounded-full">
            @else
                <!-- Default Inline Group SVG -->
                <svg class="w-12 h-12 text-gray-300" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5s-3 1.34-3 3 1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/>
                </svg>
            @endif

            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ $group->name }}
            </h2>
        </div>

        @if (Auth::id() === $group->creator_id)
            <form method="POST" action="{{ route('groups.destroy', $group->id) }}" onsubmit="return confirm('Are you sure you want to delete this group?');">
                @csrf
                @method('DELETE')
                <x-danger-button>Delete</x-danger-button>
            </form>
        @endif
    </div>
</x-slot>
     <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Message Posting Form -->
            <div class="bg-white dark:bg-gray-800 p-4 rounded shadow">
                <form method="POST" action="{{ route('messages.store', $group->id) }}">
                    @csrf
                    <textarea name="body" rows="3" class="w-full p-2 rounded border dark:bg-gray-700 dark:text-white" placeholder="Post a message..." required></textarea>
                    <div class="flex justify-end mt-2">
                        <x-primary-button>Post</x-primary-button>
                    </div>
                </form>
            </div>

            <!-- Message Board -->
            <div class="bg-white dark:bg-gray-800 p-4 rounded shadow">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Message Board</h3>

                @forelse ($messages as $message)
                        @include('components.message', ['message' => $message,'group'=> $group])
                @empty
                    <p class="text-gray-500">No messages yet. Be the first to post!</p>
                @endforelse
                {{ $messages->links() }}

                <div class="mt-6">
                   
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
                
                    