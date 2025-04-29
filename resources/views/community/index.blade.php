<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between w-full">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Community') }}
            </h2>
        </div>
        <div class="flex items-center justify-between w-full">
            <a href="{{ route('groups.create') }}" class="text-blue-500 hover:text-blue-700 ml-4">Create Group</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 space-y-6">
                
                <!-- Friends Search -->
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Find Friends</h3>
                    <div class="w-1/3">
                    <a href="{{ route('friends.search') }}" class="block text-center text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 px-4 py-2 border rounded-lg w-1/3">🔍 Search for Friends</a>
                    </div>
                    <!-- Friend Requests -->
                    <x-dropdown align="left">
                        <x-slot name="trigger">
                            <button class="relative flex items-center justify-between w-1/3 px-4 py-2 border rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none">
                                Friend Requests
                                <span id="friendRequestsCount" class="ml-2 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-full">
                                    {{ Auth::user()->receivedFriendRequests->count() }}
                                </span>
                            </button>
                        </x-slot>
                        <x-slot name="content">
                            <div class="p-4 text-gray-700 dark:text-gray-300 font-semibold">Pending Friend Requests</div>
                            <div id="friendRequestsList" class="min-w-[30rem] max-w-[50rem]">
                                @forelse(Auth::user()->receivedFriendRequests as $request)
                                    <div class="flex items-center justify-between px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700">
                                        <img src="{{ asset('storage/' . $request->sender->avatar) }}" class="w-12 h-12 rounded-full mr-3" alt="Avatar">
                                        <div class="flex-1">
                                            <p class="font-semibold">{{ $request->sender->display_name }}</p>
                                            <p class="text-xs text-gray-500">wants to be your friend.</p>
                                        </div>
                                        <form method="POST" action="{{ route('friends.accept', $request->id) }}">
                                            @csrf
                                            <button type="submit" class="text-green-500 hover:text-green-700 px-3 py-1 rounded-lg">✅</button>
                                        </form>
                                        <form method="POST" action="{{ route('friends.decline', $request->id) }}">
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

                <!-- Friends List -->
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Your Friends</h3>
                    <input type="text" id="searchFriends" placeholder="Search friends..." class="w-1/3 px-4 py-2 border rounded-lg dark:bg-gray-700 dark:text-white">
                    <ul id="friendsList" class="space-y-2">
                        @foreach($friends as $friend)
                            <li class="flex items-center space-x-4 p-3 bg-gray-100 dark:bg-gray-700 rounded-lg">
                            <!-- Avatar -->
                             @if($friend->avatar)
                             <img src="{{ asset('storage/' . $friend->avatar) }}" class="w-10 h-10 rounded-full" alt="Avatar">
                            @else
                            <svg class="w-12 h-12 text-gray-300" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2a5 5 0 015 5v1a5 5 0 11-10 0V7a5 5 0 015-5zm0 14c-5.523 0-10 4.477-10 10h20c0-5.523-4.477-10-10-10z"/>
                            </svg>
                            @endif   
                             <span class="text-gray-900 dark:text-gray-100">{{ $friend->display_name }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <!-- Groups Search -->
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Your Groups</h3>
                    <input type="text" id="searchGroups" placeholder="Search your groups..." class="w-1/3 px-4 py-2 border rounded-lg dark:bg-gray-700 dark:text-white">
                    <ul id="personalGroupsList" class="space-y-2">
                        @foreach($userGroups as $group)
                            <li class="p-3 bg-gray-100 dark:bg-gray-700 rounded-lg">
                                <a href="{{ route('groups.show', $group->id) }}" class="text-blue-500">{{ $group->name }}</a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <!-- Public Groups -->
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Public Groups</h3>
                    <input type="text" id="searchPublicGroups" placeholder="Search public groups..." class="w-1/3 px-4 py-2 border rounded-lg dark:bg-gray-700 dark:text-white">
                    <ul id="publicGroupsList" class="space-y-2">
                        @foreach($publicGroups as $group)
                            <li class="p-3 bg-gray-100 dark:bg-gray-700 rounded-lg flex justify-between">
                                <a href="{{ route('groups.show', $group->id) }}" class="text-blue-500">{{ $group->name }}</a>
                                <span class="text-gray-600 dark:text-gray-300">{{ $group->members_count }} members</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
