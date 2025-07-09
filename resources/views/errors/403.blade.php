
<x-guest-layout>
    <div class="text-center">
        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-4">
            <svg class="h-8 w-8 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
            </svg>
        </div>
        <h1 class="text-4xl font-bold text-gray-900 mb-4">403</h1>
        <h2 class="text-xl font-semibold text-gray-700 mb-4">Access Forbidden</h2>
        <p class="text-gray-600 mb-6">You don't have permission to access this resource.</p>
        <a href="{{ route('dashboard') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg font-medium transition duration-200">
            Return to Dashboard
        </a>
    </div>
</x-guest-layout>