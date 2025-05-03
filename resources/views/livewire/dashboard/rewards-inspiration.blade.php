<!-- Rewards & Inspiration -->
<div class="w-full md:w-1/2 bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 text-center">
    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 border-b pb-2">Rewards & Inspiration</h3>
    <p class="mt-4 text-lg font-semibold text-gray-400">
        You got <span class="text-green-400">{{ auth()->user()->badges()->count() }}</span> badge{{ auth()->user()->badges()->count() !== 1 ? 's' : '' }}
    </p>

    <a href="{{ route('rewards') }}" class="mt-3 px-4 py-2 bg-blue-600 text-white rounded-lg block text-center">View Rewards</a>
    
    <!-- Inspirational Quote -->
    <div x-data="{ quotes: [
        'Believe in yourself and all that you are.',
        'Keep going! Your hardest times often lead to the greatest moments of your life.',
        'Hardships often prepare ordinary people for an extraordinary destiny.',
        'Success is the sum of small efforts, repeated day in and day out.',
        'Do what you can, with what you have, where you are.',
        'Your limitation—it’s only your imagination.'
    ], quote: '',
    updateQuote() {
        this.quote = this.quotes[Math.floor(Math.random() * this.quotes.length)];
    }}"
    x-init="updateQuote(); setInterval(() => updateQuote(), 50000)"
    class="mt-4 italic text-gray-600 dark:text-gray-300 text-center">
        <span x-text="quote"></span>
    </div>
</div>