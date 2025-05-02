<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Pending Friend Requests
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @forelse ($requests as $request)
                <div class="flex items-center justify-between bg-white dark:bg-gray-800 p-4 rounded shadow">
                    <div class="flex items-center space-x-4">
                        @if ($request->sender->avatar)
                            <img src="{{ asset('storage/' . $request->sender->avatar) }}" class="w-12 h-12 rounded-full" alt="Avatar">
                        @else
                            <div class="w-12 h-12 bg-gray-300 rounded-full flex items-center justify-center text-white">
                                {{ strtoupper(substr($request->sender->name, 0, 1)) }}
                            </div>
                        @endif
                        <div>
                            <p class="font-semibold text-gray-800 dark:text-white">{{ $request->sender->display_name }}</p>
                            <p class="text-sm text-gray-500">wants to be your friend</p>
                        </div>
                    </div>
                    <div class="flex space-x-2">
                        <form method="POST" action="{{ route('friends.accept', $request->id) }}">
                            @csrf
                            <button class="text-green-600 hover:text-green-800 px-3 py-1 border border-green-600 rounded-lg">Accept</button>
                        </form>
                        <form method="POST" action="{{ route('friends.decline', $request->id) }}">
                            @csrf
                            <button class="text-red-600 hover:text-red-800 px-3 py-1 border border-red-600 rounded-lg">Decline</button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="text-center text-gray-500 dark:text-gray-400">You have no pending friend requests.</div>
            @endforelse
        </div>
    </div>
</x-app-layout>
