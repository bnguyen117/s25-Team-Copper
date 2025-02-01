
<!-- Extend app.blade.php Page layout -->
<x-app-layout>

    <!-- Header -->
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('What If Analysis') }}
        </h2>
    </x-slot>

    <!-- Main content container holding the Filament form -->
    <div class="pt-8 m-auto max-w-[21rem] sm:max-w-sm md:max-w-2xl lg:max-w-7xl lg:px-8">
         @livewire('what-if-form')
    </div>

</x-app-layout>