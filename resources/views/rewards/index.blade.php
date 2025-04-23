
<!-- Extend app.blade.php Page layout -->
<x-app-layout>

    <!-- Header -->
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Rewards') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                <!-- Your Achievements -->
                <h3 class="text-center text-2xl font-bold text-gray-900 dark:text-gray-100 mb-6">
                    Your Achievements
                </h3>

                <!-- Badge Grid -->
                <div class="grid grid-cols-3 gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
                    @if(auth()->user()->badges->isEmpty())
                    <div class="col-span-3 text-center text-gray-500 dark:text-gray-400 italic">
                        You have no badges yet. Explore the app!
                    </div>
                    
                    @else
                    <!-- DB part -->
                        @foreach (auth()->user()->badges as $badge)
                            <div class="p-2 rounded-full shadow-md hover:opacity-80">
                            <img src="{{ asset('images/badges/' . $badge->icon ) }}" 
                                    alt="{{ $badge->name }}" 
                                    class="w-full h-auto rounded-full">
                            <p class="text-center text-sm mt-2 text-white">{{ $badge->name }}</p>
                            </div>
                        @endforeach
                    @endif
                </div>

            <!-- Current Goals Section -->
            <div class="bg-[#A9DFFF] shadow-sm sm:rounded-lg p-6 mt-8">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Your Goal(s) Progress</h3>
                <div class="p-4 border border-gray-600 rounded-md">
                    <p class="text-gray-700">You have no active goals.</p>
                </div>
                <a href="https://prod.s25-team-copper.stspreview.com/finance#goals" class="mt-4 w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded text-center block">
                + Add or Edit Goals
                </a>
            </div>
            </div>
        </div>
    </div>

</x-app-layout>
