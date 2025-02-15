<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Profile Display Section -->
            <div class="p-6 bg-white dark:bg-gray-800 shadow sm:rounded-lg text-center">
                <!-- Avatar (Larger & Centered) -->
                <div class="flex justify-center">
                    @if (Auth::user()->avatar)
                        <img src="{{ asset('storage/' . Auth::user()->avatar) }}" 
                             class="w-40 h-40 rounded-full border-4 border-gray-300 dark:border-gray-600 shadow-lg" 
                             alt="User Avatar">
                    @else
                        <!-- Default Avatar -->
                        <svg class="w-40 h-40 text-gray-300" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2a5 5 0 015 5v1a5 5 0 11-10 0V7a5 5 0 015-5zm0 14c-5.523 0-10 4.477-10 10h20c0-5.523-4.477-10-10-10z"/>
                        </svg>
                    @endif
                </div>

                <!-- Display User's Actual Name -->
                <h1 class="mt-4 text-2xl font-semibold text-gray-900 dark:text-gray-100">
                    {{ Auth::user()->name }}
                </h1>
            </div>

            <!-- Profile Information Form -->
            <div class="p-6 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <!-- Update Password -->
            <div class="p-6 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <!-- Delete Account -->
            <div class="p-6 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>