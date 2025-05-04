<x-app-layout>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8"> <!-- Centered & Limited Width -->

        <h2 class="text-xl font-bold mt-4 mb-4 text-center dark:text-white">Search for Friends</h2>

        <!-- Search Form -->
        <form method="GET" action="{{ route('friends.search') }}" class="mb-4 flex justify-center">
            <input type="text" name="query" placeholder="Search users..." class="border p-2 rounded-lg w-1/2">
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 ml-2 rounded-lg">Search</button>
        </form>

        <!-- Search Results -->
        <ul class="space-y-4">
            @forelse($users as $user)
                <li class="flex items-center justify-between p-4 border rounded-lg bg-gray-100 dark:bg-gray-800">
                    <div class="flex items-center space-x-4">
                        <!-- Avatar -->
                        @if($user->avatar)
                            <img src="{{ asset('storage/' . $user->avatar) }}" class="w-12 h-12 rounded-full" alt="Avatar">
                        @else
                            <svg class="w-12 h-12 text-gray-300" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2a5 5 0 015 5v1a5 5 0 11-10 0V7a5 5 0 015-5zm0 14c-5.523 0-10 4.477-10 10h20c0-5.523-4.477-10-10-10z"/>
                            </svg>
                        @endif
                        
                        <!-- User Info -->
                        <div>
                            <p class="font-semibold text-gray-900 dark:text-gray-100">{{ $user->display_name }}</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $user->email }}</p>
                        </div>
                    </div>

                    <!-- Friend Request Button -->
                    @php
                        $isFriend = Auth::user()->friends->contains($user->id);
                        $requestSent = Auth::user()->sentFriendRequests->contains('receiver_id', $user->id);
                    @endphp

                    @if($isFriend)
                        <button class="bg-yellow-500 text-white px-4 py-2 rounded-lg cursor-not-allowed">Friends</button>
                    @elseif($requestSent)
                        <button class="bg-blue-500 text-white px-4 py-2 rounded-lg cursor-not-allowed">Request Sent</button>
                    @else
                        <form method="POST" action="{{ route('friends.sendRequest', $user->id) }}">
                            @csrf
                            <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded-lg">Add Friend</button>
                        </form>
                    @endif
                </li>
            @empty
                <p class="text-gray-600 dark:text-gray-400 text-center">No users found.</p>
            @endforelse
        </ul>

    </div> <!-- End of Centered Wrapper -->
</x-app-layout>