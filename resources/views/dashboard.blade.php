<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <!-- Make smaller boxes for different components to fit into.  Aligns them horizontally-->
    <div class="flow-root mt-12 mx-4">
        <!-- For the debt/income ratio -->
        <div class="float-left w-40 rounded-lg bg-white dark:bg-gray-800 shadow-sm">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                {{ __("Debt/Income ratio graph here") }}
            </div>
        </div>
        <!-- For the rewards section -->
        <div class="float-right w-40 rounded-lg bg-white dark:bg-gray-800 shadow-sm">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                {{ __("Rewards go here") }}
            </div>
        </div>
    </div>

    <div class="py-12">
        <div class="max-w-8xl mx-auto sm:px-6 lg:px-8 py-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <!-- Required for modal suport {{ __("You're logged in!") }}-->
                     @livewire('budget-form')

                </div>
            </div>
        </div>
    </div>


</x-app-layout>
