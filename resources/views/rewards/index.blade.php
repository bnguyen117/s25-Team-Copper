
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
            <div x-data="{ selectedBadge: null }" class="grid grid-cols-3 gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
                @if(auth()->user()->badges->isEmpty())
                <div class="col-span-3 text-center text-gray-500 dark:text-gray-400 italic">
                    You have no badges yet. Explore the app!
            </div>

                @else
                    @foreach (auth()->user()->badges as $badge)
                        <div 
                            @click="selectedBadge = { 
                                name: '{{ addslashes($badge->name) }}', 
                                description: '{{ addslashes($badge->description) }}'
                            }"
                            class="p-2 rounded-full shadow-md hover:opacity-80 cursor-pointer transition-opacity"
                        >
                            <img 
                                src="{{ asset('images/badges/' . $badge->icon ) }}" 
                                alt="{{ $badge->name }}" 
                                class="w-full h-auto rounded-full"
                            >
                        </div>
                    @endforeach
                @endif

                <!-- Centered Modal -->
                <div 
                    x-show="selectedBadge" 
                    x-cloak
                    class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black bg-opacity-50"
                    @click.away="selectedBadge = null"
                    @keydown.escape.window="selectedBadge = null"
                >
                    <div class="bg-white rounded-lg w-full max-w-xs md:max-w-sm relative">
                        <button 
                            @click="selectedBadge = null"
                            class="absolute top-2 right-2 text-gray-500 hover:text-gray-700 p-1"
                        >
                            X
                        </button>
                        <div class="p-6 pt-8 text-center">
                        <h3 class="text-xl font-semibold mb-2 text-green-500" x-text="selectedBadge?.name"></h3>
                            <p class="text-gray-600 text-sm" x-text="selectedBadge?.description"></p>
                        </div>
                    </div>
                </div>
            </div>


            <!-- Current Goals Section -->
            <div class="bg-[#A9DFFF] shadow-sm sm:rounded-lg p-6 mt-8">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Your Goal(s) Progress</h3>
                <div class="p-4 border border-gray-600 rounded-md">
                    <p class="text-gray-700">You have no active goals.</p>
                </div>
            </div>
            </div>
        </div>
    </div>

    <!-- User Goals Table Section -->
    <div id="goals" class="mb-16 m-auto max-w-[21rem] sm:max-w-sm md:max-w-2xl lg:max-w-7xl lg:px-8">
        @livewire('user-goal-table')
     </div>

</x-app-layout>
