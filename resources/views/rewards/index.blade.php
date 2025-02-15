
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

                <!-- Badge Grid (stand-in for now) -->
                <div class="grid grid-cols-3 gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
                    <button class="p-2 rounded-full shadow-md hover:opacity-80">
                        <img src="https://images.vexels.com/media/users/3/143188/isolated/preview/5f44f3160a09b51b4fa4634ecdff62dd-money-icon.png?w=360" alt="Badge" class="w-full h-auto rounded-full">
                    </button>
                    <button class="p-2 rounded-full shadow-md hover:opacity-80">
                        <img src="https://production-tcf.imgix.net/app/uploads/2016/02/02182858/2014-1-2-the-high-cost-of-student-debt-jill-2.jpg" alt="Badge" class="w-full h-auto rounded-full">
                    </button>
                    <!-- Add more badges as needed -->
                </div>
            </div>

            <!-- Current Goals Section -->
            <div class="bg-[#A9DFFF] shadow-sm sm:rounded-lg p-6 mt-8">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Current Goals</h3>
                <div class="p-4 border border-gray-600 rounded-md">
                    <p class="text-gray-700">You have no active goals.</p>
                </div>
                <button class="mt-4 w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                    + Add New Goal
                </button>
            </div>
        </div>
    </div>
</x-app-layout>
