<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between w-full">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Community') }}
            </h2>
        </div>
    </x-slot>

    <!-- Notification for Friendly badge -->
    @if (session('badge_awarded'))
        <div 
            x-data="{ show: true }" 
            x-show="show" 
            x-init="setTimeout(() => show = false, 4000)"
            class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6"
            role="alert"
        >
            <strong class="font-bold">🎉 {{ session('badge_awarded') }}</strong>
        </div>
    @endif

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 space-y-10">

                <!-- Friends Menu Dropdown -->
                <div class="flex justify-end">
                    <x-dropdown align="right">
                        <x-slot name="trigger">
                            <button class="flex items-center px-4 py-2 border rounded-md text-[#00d4f5] dark:text-white bg-white dark:bg-gray-800 hover:text-[#00b6db] dark:hover:text-white focus:outline-none">
                                Menu ⏷
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <a href="{{ route('friends.search') }}"
                               class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                🔍 Search for Friends
                            </a>
                            <a href="{{ route('friends.requests') }}"
                               class="flex items-center justify-between px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                <span>👥 Friend Requests</span>
                                <span class="ml-2 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-full">
                                    {{ Auth::user()->receivedFriendRequests->count() }}
                                </span>
                            </a>
                            <a href="{{ route('groups.create') }}"
                               class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                🧩 Create Group
                            </a>
                        </x-slot>
                    </x-dropdown>
                </div>

                <!-- Your Friends Section -->
                <div class="space-y-6">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">
                        Your Friends
                    </h3>

                    <ul id="friendsList" class="space-y-4">
                        @foreach($friends as $friend)
                            <li class="w-3/4 p-4 bg-gray-100 dark:bg-gray-700 rounded-lg space-y-2">
                                <div class="flex items-center space-x-4">
                                    @if($friend->avatar)
                                        <img src="{{ asset('storage/' . $friend->avatar) }}" class="w-10 h-10 rounded-full" alt="Avatar">
                                    @else
                                        <svg class="w-12 h-12 text-gray-300" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 2a5 5 0 015 5v1a5 5 0 11-10 0V7a5 5 0 015-5zm0 14c-5.523 0-10 4.477-10 10h20c0-5.523-4.477-10-10-10z"/>
                                        </svg>
                                    @endif
                                    <span class="text-gray-900 dark:text-gray-100 font-medium">
                                        {{ $friend->display_name }}
                                    </span>
                                </div>

                                <!-- Badge display -->
                                @if($friend->user && $friend->user->badges->isNotEmpty())
                                    <div class="flex flex-wrap gap-2 mt-2">
                                        @foreach ($friend->user->badges as $badge)
                                            <div x-data="{ open: false }" class="relative w-10 h-10 cursor-pointer">
                                                <img 
                                                    src="{{ asset('images/badges/' . $badge->icon) }}" 
                                                    alt="{{ $badge->name }}" 
                                                    class="w-full h-full rounded-full shadow hover:opacity-80"
                                                    @click="open = true"
                                                >
                                                <div 
                                                    x-show="open" 
                                                    x-cloak 
                                                    @click.away="open = false"
                                                    class="absolute z-50 top-10 left-0 bg-white dark:bg-gray-900 text-sm p-2 rounded shadow-md w-40"
                                                >
                                                    <p class="text-green-600 font-semibold">{{ $badge->name }}</p>
                                                    <p class="text-gray-600 dark:text-gray-300 text-xs">{{ $badge->description }}</p>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-sm text-gray-500 dark:text-gray-400 italic mt-1">No badges yet</p>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>

                <!-- Your Groups Section -->
                <div class="space-y-6">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">
                        Your Groups
                    </h3>

                    <input type="text" id="searchGroups" placeholder="Search your groups..." class="w-3/4 px-4 py-2 border rounded-lg dark:bg-gray-700 dark:text-white">

                    <ul id="personalGroupsList" class="space-y-2">
                        @foreach($userGroups as $group)
                            <li class="w-3/4 p-3 bg-gray-100 dark:bg-gray-700 rounded-lg">
                                <a href="{{ route('groups.show', $group->id) }}" class="text-blue-500">
                                    {{ $group->name }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <!-- Public Groups Section -->
                <div class="space-y-6">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">
                        Public Groups
                    </h3>

                    <input type="text" id="searchPublicGroups" placeholder="Search public groups..." class="w-3/4 px-4 py-2 border rounded-lg dark:bg-gray-700 dark:text-white">

                    <ul id="publicGroupsList" class="space-y-2">
                        @foreach($publicGroups as $group)
                            <li class="w-3/4 p-3 bg-gray-100 dark:bg-gray-700 rounded-lg flex justify-between">
                                <a href="{{ route('groups.show', $group->id) }}" class="text-blue-500">
                                    {{ $group->name }}
                                </a>
                                <span class="w-3/4 text-gray-600 dark:text-gray-300">
                                    {{ $group->members_count }} members
                                </span>
                            </li>
                        @endforeach
                    </ul>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
  
                    