
<x-guest-layout>
    <div class="text-center">
        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-4">
            <svg class="h-8 w-8 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>
        <h1 class="text-4xl font-bold text-gray-900 mb-4">500</h1>
        <h2 class="text-xl font-semibold text-gray-700 mb-4">Server Error</h2>
        <p class="text-gray-600 mb-6">Something went wrong on our end. Please try again later.</p>
        <a href="{{ route('dashboard') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg font-medium transition duration-200">
            Return to Dashboard
        </a>
    </div>
</x-guest-layout>