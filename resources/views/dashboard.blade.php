<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight text-center">
            {{ Auth::user()->name }}'s Dashboard
        </h2>
    </x-slot>

    <!-- Make smaller boxes for different components to fit into.  Aligns them horizontally-->
    <div class="flow-root mt-12 mx-4">
        <!-- For the debt/income ratio -->
        <div class="float-left w-40 rounded-lg bg-white dark:bg-gray-800 shadow-sm">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                {{ ("Debt/Income ratio graph here") }}
            </div>
        </div>
        <!-- Rewards and Leaderboard -->
        <div class="float-right w-40 rounded-lg bg-white dark:bg-gray-800 shadow-sm">
                 <div class="p-6 text-gray-900 dark:text-gray-100">
                     <h3 class="text-black-600"> You've new rewards <span class="text-black-500"></span></h3>
                       <a href="{{ url('http://s25-team-copper.test/rewards') }}" class="mt-3 px-4 py-2 bg-blue-600 text-white rounded-lg block text-center">Rewards</a>
            </div>
        </div>
    </div>

        <!-- Your Goal Progress -->
        <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Your Goal Progress</h3>
                <a href="{{ url('http://s25-team-copper.test/finance') }}" class="mt-3 px-4 py-2 bg-blue-600 text-white rounded-lg block text-center">Edit Goals</a>
            </div>
        </div>


    <div class="py-12">
        <div class="max-w-8xl mx-auto sm:px-6 lg:px-8 py-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <!-- Required for modal suport {{ ("You're logged in!") }}-->
                     @livewire('budget-form')

                </div>
            </div>
        </div>
    </div>


</x-app-layout>