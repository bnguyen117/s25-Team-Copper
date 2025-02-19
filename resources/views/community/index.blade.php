
<!-- Extend app.blade.php Page layout -->
<x-app-layout>

    <!-- Header -->
    <x-slot name="header">
    <div class="flex items-center justify-between w-full">
        <!-- Page Title -->
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Community') }}
        </h2>

        <!-- Middle: Create Group Link -->
        <a href="{{ route('groups.create') }}" class="text-blue-500 hover:text-blue-700 ml-4">
            Create Group
        </a>

     <!-- Friend Search Button-->
     <div class="flex items-center space-x-6">
        <a href="{{ route('friends.search') }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 px-4 py-2 border rounded-lg">
                🔍 Search for Friends
            </a>

            <!-- Friend Requests Dropdown (Correct Alignment & Wider) -->
            <x-dropdown align="right">
                <x-slot name="trigger">
                    <button class="relative flex items-center justify-between min-w-[220px] px-4 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition">
                        Friend Requests

                        <!-- Notification Badge -->
                        <span id="friendRequestsCount" class="ml-2 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-full">
                            {{ Auth::user()->receivedFriendRequests->count() }}
                        </span>

                        <!-- Dropdown Arrow Icon -->
                        <svg class="ml-2 w-4 h-4 text-gray-500 dark:text-gray-300" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </x-slot>

                <x-slot name="content">
                    <div class="p-4 text-gray-700 dark:text-gray-300 font-semibold">Pending Friend Requests</div>

                    <div id="friendRequestsList">
                        @forelse(Auth::user()->receivedFriendRequests as $request)
                            <div class="flex items-center justify-between px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 request-{{ $request->id }}">
                                <!-- Avatar -->
                                <img src="{{ asset('storage/' . $request->sender->avatar) }}" class="w-12 h-12 rounded-full mr-3" alt="Avatar">

                                <!-- Request Text -->
                                <div class="flex-1">
                                    <p class="font-semibold">{{ $request->sender->display_name }}</p>
                                    <p class="text-xs text-gray-500">wants to be your friend.</p>
                                </div>

                                <!-- Actions -->
                                <form method="POST" action="{{ route('friends.accept', $request->id) }}" class="inline-block accept-request" data-request-id="{{ $request->id }}">
                                    @csrf
                                    <button type="submit" class="text-green-500 hover:text-green-700 px-3 py-1 rounded-lg">✅</button>
                                </form>

                                <form method="POST" action="{{ route('friends.decline', $request->id) }}" class="inline-block decline-request" data-request-id="{{ $request->id }}">
                                    @csrf
                                    <button type="submit" class="text-red-500 hover:text-red-700 px-3 py-1 rounded-lg">❌</button>
                                </form>
                            </div>
                        @empty
                            <div class="p-4 text-center text-gray-500">No pending requests.</div>
                        @endforelse
                    </div>
                </x-slot>
            </x-dropdown>
            </div>
    </div>
</x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 grid grid-cols-3 gap-6">
                
                <!-- Left Column: Personal Groups -->
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Your Groups</h3>
                    <input type="text" id="searchGroups" placeholder="Search your groups..." class="w-full px-4 py-2 border rounded-lg dark:bg-gray-700 dark:text-white">
                    <ul id="personalGroupsList" class="space-y-2">
                        @foreach($userGroups as $group)
                            <li class="p-3 bg-gray-100 dark:bg-gray-700 rounded-lg">
                                <a href="{{ route('groups.show', $group->id) }}" class="text-blue-500">{{ $group->name }}</a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <!-- Middle Column: Public Groups -->
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Public Groups</h3>
                    <input type="text" id="searchPublicGroups" placeholder="Search public groups..." class="w-full px-4 py-2 border rounded-lg dark:bg-gray-700 dark:text-white">
                    <ul id="publicGroupsList" class="space-y-2">
                        @foreach($publicGroups as $group)
                            <li class="p-3 bg-gray-100 dark:bg-gray-700 rounded-lg flex justify-between">
                                <a href="{{ route('groups.show', $group->id) }}" class="text-blue-500">{{ $group->name }}</a>
                                <span class="text-gray-600 dark:text-gray-300">{{ $group->members_count }} members</span>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <!-- Right Column: Friends List -->
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Friends</h3>
                    <input type="text" id="searchFriends" placeholder="Search friends..." class="w-full px-4 py-2 border rounded-lg dark:bg-gray-700 dark:text-white">
                    <ul id="friendsList" class="space-y-2">
                        @foreach($friends as $friend)
                            <li class="flex items-center space-x-4 p-3 bg-gray-100 dark:bg-gray-700 rounded-lg">
                                <img src="{{ asset('storage/' . $friend->avatar) }}" class="w-10 h-10 rounded-full" alt="Avatar">
                                <span class="text-gray-900 dark:text-gray-100">{{ $friend->display_name }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>

            </div>
        </div>
    </div>

</x-app-layout>