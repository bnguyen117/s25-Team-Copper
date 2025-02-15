<nav x-data="{ open: false, openRequests: false }" class="navigation">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">

            <!-- Back Button for Mobile (Hidden on screens >= 640px) -->
            <button onclick="window.history.back()" class="sm:hidden flex items-center text-gray-400">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                </svg>
            </button>

            <!-- Centered Logo for Mobile -->
            <a href="{{ route('dashboard') }}" class="sm:hidden flex items-center">
                <x-application-logo class="block h-9 w-auto fill-current text-gray-800 dark:text-gray-200" />
            </a>

            <!-- Logo for Desktop -->
            <a href="{{ route('dashboard') }}" class="hidden sm:flex items-center"> 
                <x-application-logo class="block h-9 w-auto fill-current text-gray-800 dark:text-gray-200" />
            </a>

            <!-- Friend Search Button (Vertically Centered) -->
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

            <!-- User Profile Dropdown -->
            <x-dropdown align="right" width="48">
                <x-slot name="trigger">
                    <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                        <!-- Avatar -->
                        @if(Auth::user()->avatar)
                            <img class="h-10 w-10 rounded-full mr-2" src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="User Avatar">
                        @else
                            <!-- Default Avatar -->
                            <svg class="h-10 w-10 text-gray-300 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2a5 5 0 015 5v1a5 5 0 11-10 0V7a5 5 0 015-5zm0 14c-5.523 0-10 4.477-10 10h20c0-5.523 4.477-10-10-10z"/>
                            </svg>
                        @endif

                        <!-- Username -->
                        <div>{{ Auth::user()->name }}</div>

                        <!-- Dropdown Icon -->
                        <div class="ms-1">
                            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    </button>
                </x-slot>

                <x-slot name="content">
                    <x-dropdown-link :href="route('profile.edit')">
                        {{ __('Profile') }}
                    </x-dropdown-link>

                    <!-- Authentication -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-dropdown-link :href="route('logout')"
                            onclick="event.preventDefault();
                                this.closest('form').submit();">
                            {{ __('Log Out') }}
                        </x-dropdown-link>
                    </form>
                </x-slot>
            </x-dropdown>
        </div>
    </div>
</nav>