<nav x-data="{ open: false }" class="navigation">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">

            <!-- Back Button for Mobile (Hidden on screens >= 640px) -->
            <button onclick="window.history.back()" class="sm:hidden flex items-center text-gray-400">
                <img src="{{ asset('images/navbar/back-arrow.png') }}" alt="Back arrow" class="size-5" />
            </button>

            <!-- Centered Logo for Mobile (Visible on screens <= 640px) -->
            <a href="{{ route('dashboard') }}" class="sm:hidden flex items-center">
                <x-application-logo/>
            </a>

            <!-- Logo for Desktop (Visible on screens >= 640px) -->
            <a href="{{ route('dashboard') }}" class="hidden sm:flex items-center"> 
                <x-application-logo/>
            </a>

            <div class="flex items-center">
                <!-- Toggle Dark/Light Theme -->
                <button 
                    class="toggle-mode hidden sm:block p-2 rounded-md transition hover:bg-gray-100 dark:hover:bg-gray-700" 
                    aria-label="Toggle dark mode"
                >
                    <img src="{{ asset('images/welcome/sun.png') }}" alt="Light mode" class="size-8 dark:hidden"/>
                    <img src="{{ asset('images/welcome/moon.png') }}" alt="Dark mode" class="size-7 hidden dark:block"/>
                </button>
            
                <!-- Hamburger Toggle for Mobile (Visible on screens <= 640px) -->
                <div class=" flex items-center sm:hidden">
                    <button @click="open = true">
                        <img src="{{ asset('images/navbar/hamburger.png') }}" alt="Hamburger icon" class="size-5" />
                    </button>
                </div>

                <!-- Settings Dropdown for Desktop (Visible on screens >= 640px) -->
                <div class="hidden sm:flex sm:items-center sm:ms-6">
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                                <div>{{ Auth::user()->name }}</div>

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
        </div>
    </div>

    <!-- Slide-Out Menu for Mobile -->
    <div x-cloak :class="open ? 'translate-x-0' : 'translate-x-full'" class="slide-out-menu">

        <!-- User Information Section -->
        <div class=" pl-4 my-3 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <div class="font-medium pb-1 text-base text-gray-800 dark:text-gray-200">{{ Auth::user()->name }}</div>
                    <div class="font-medium pb-1 text-sm text-gray-500">{{ Auth::user()->email }}</div>
                </div>

                <div class="flex items-center space-x-2">
                    <!-- Toggle Dark/Light Theme -->
                    <button 
                        class="toggle-mode sm:hidden p-2 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700"
                        aria-label="Toggle dark mode"
                    >
                        <img src="{{ asset('images/welcome/sun.png') }}" alt="Light mode" class="size-7 dark:hidden" />
                        <img src="{{ asset('images/welcome/moon.png') }}" alt="Dark mode" class="size-7 hidden dark:block" />
                    </button>
                    <!-- Close Slide-Out Menu -->
                    <button 
                        @click="open = false" 
                        class="sm:hidden p-2"
                        aria-label="Close menu"
                    >
                        <img src="{{ asset('images/navbar/exit.png') }}" alt="exit icon" class="size-5" />
                    </button>
                </div>
            </div>
        </div>

         <!-- Links  -->
        <div class=" pl-1">
            <!-- Dashboard Link -->
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>

            <!-- Profile Link -->
            <x-responsive-nav-link :href="route('profile.edit')" :active="request()->routeIs('profile.edit')">
                {{ __('Profile') }}
            </x-responsive-nav-link>

            <!-- Logout Form -->
            <form method="POST" action="{{ route('logout') }}">
                @csrf

                <!-- Logout Link -->
                <x-responsive-nav-link :href="route('logout')"
                        onclick="event.preventDefault();
                                    this.closest('form').submit();">
                    {{ __('Log Out') }}
                </x-responsive-nav-link>
            </form>
        </div>
    </div>
</nav>