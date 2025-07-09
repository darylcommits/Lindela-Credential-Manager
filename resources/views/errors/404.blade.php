
<x-guest-layout>
    <div class="text-center">
        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-yellow-100 mb-4">
            <svg class="h-8 w-8 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6-4h6m2 5.291A7.962 7.962 0 0112 15c-2.34 0-4.29-1.265-5.41-3.134C6.59 11.865 7.59 11 9 11h6c1.41 0 2.59.865 2.59 1.866z" />
            </svg>
        </div>
        <h1 class="text-4xl font-bold text-gray-900 mb-4">404</h1>
        <h2 class="text-xl font-semibold text-gray-700 mb-4">Page Not Found</h2>
        <p class="text-gray-600 mb-6">The page you're looking for doesn't exist.</p>
        <a href="{{ route('dashboard') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg font-medium transition duration-200">
            Return to Dashboard
        </a>
    </div>
</x-guest-layout>