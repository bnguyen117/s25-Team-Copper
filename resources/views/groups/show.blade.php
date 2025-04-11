<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-4">
            @if ($group->image)
                <img src="{{ asset('storage/' . $group->image) }}" alt="Group Avatar" class="w-12 h-12 rounded-full">
            @else
                <!-- Default Inline Group SVG -->
                <svg class="w-12 h-12 text-gray-300" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5s-3 1.34-3 3 1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/>
                </svg>
            @endif

            <!-- Group name goes here -->
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ $group->name }}
            </h2>
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
                    <div class="flex items-start space-x-4 mb-6 border-b pb-4">
                        @if($message->user->avatar)
                            <img src="{{ asset('storage/' . $message->user->avatar) }}" class="w-10 h-10 rounded-full" alt="User Avatar">
                        @else
                            <svg class="w-10 h-10 text-gray-300" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2a5 5 0 015 5v1a5 5 0 11-10 0V7a5 5 0 015-5zm0 14c-5.523 0-10 4.477-10 10h20c0-5.523-4.477-10-10-10z"/>
                            </svg>
                        @endif

                        <div>
                            <p class="font-semibold text-gray-900 dark:text-white">{{ $message->user->display_name }}</p>
                            <p class="text-sm text-gray-500">{{ $message->created_at->diffForHumans() }}</p>
                            <p class="mt-2 text-gray-800 dark:text-gray-300">{{ $message->body }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500">No messages yet. Be the first to post!</p>
                @endforelse

                <!-- Pagination Links -->
                <div class="mt-6">
                    {{ $messages->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>