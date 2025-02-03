
<!-- Extend app.blade.php Page layout -->
<x-app-layout>

    <!-- Header -->
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Finances') }}
        </h2>
    </x-slot>

    <!-- Main content container holding the Filament form -->
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="text-center p-6 text-gray-900 dark:text-gray-100">
                    {{ __("🔨 Construction Zone 🔨") }}
                </div>
            </div>
        </div>
    </div>

</x-app-layout>