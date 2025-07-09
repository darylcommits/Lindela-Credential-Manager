{{-- File: resources/views/credentials/index.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center animate-slide-up">
            <div>
                <h2 class="text-3xl font-bold gradient-text">
                    {{ __('Credentials Manager') }}
                </h2>
                <p class="text-gray-300 mt-1">{{ __('Manage your secure credentials') }}</p>
            </div>
            <div class="flex items-center space-x-4">
                <a href="{{ route('credentials.create') }}" 
                   class="bg-gradient-to-r from-yellow-600 to-yellow-500 text-black hover:from-yellow-500 hover:to-yellow-400 px-6 py-3 rounded-lg font-semibold transition-all duration-300 transform hover:scale-105 flex items-center space-x-2 shadow-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>{{ __('Add Credential') }}</span>
                </a>
            </div>
        </div>
    </x-slot>

    <!-- Custom Styles -->
    <style>
        .gradient-text {
            background: linear-gradient(135deg, #fbbf24, #f59e0b, #d97706);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .glass-card {
            background: rgba(0, 0, 0, 0.7);
            border: 1px solid rgba(255, 221, 0, 0.25);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.5), inset 0 1px 0 rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(15px);
        }
        
        .dashboard-container {
            background: linear-gradient(135deg, #000000 0%, #1a1a1a 30%, #2a2418 50%, #1a1a1a 70%, #000000 100%);
            min-height: 100vh;
        }

        .animate-slide-up {
            animation: slideUp 0.6s ease-out;
        }

        .animate-fade-in {
            animation: fadeIn 0.8s ease-out;
        }

        .animate-fade-out {
            animation: fadeOut 0.3s ease-out;
        }

        @keyframes slideUp {
            0% { transform: translateY(20px); opacity: 0; }
            100% { transform: translateY(0); opacity: 1; }
        }

        @keyframes fadeIn {
            0% { opacity: 0; }
            100% { opacity: 1; }
        }

        @keyframes fadeOut {
            0% { opacity: 1; }
            100% { opacity: 0; }
        }

        .credential-card {
            background: rgba(26, 26, 26, 0.8);
            border: 1px solid rgba(255, 221, 0, 0.2);
            transition: all 0.3s ease;
        }

        .credential-card:hover {
            border-color: rgba(255, 221, 0, 0.4);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(255, 221, 0, 0.1);
        }

        .credential-card.hidden-card {
            display: none !important;
        }

        .search-input {
            background: rgba(0, 0, 0, 0.6);
            border: 1px solid rgba(255, 221, 0, 0.3);
            color: #ffffff;
        }

        .search-input:focus {
            outline: none;
            border-color: #ffdd00;
            background: rgba(255, 221, 0, 0.1);
            box-shadow: 0 0 15px rgba(255, 221, 0, 0.2);
        }

        .search-input::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }

        .filter-btn {
            background: rgba(255, 221, 0, 0.1);
            border: 1px solid rgba(255, 221, 0, 0.3);
            color: #ffdd00;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .filter-btn:hover, .filter-btn.active {
            background: rgba(255, 221, 0, 0.2);
            border-color: #ffdd00;
            transform: translateY(-1px);
            box-shadow: 0 4px 15px rgba(255, 221, 0, 0.2);
        }

        .filter-btn.active {
            background: rgba(255, 221, 0, 0.3);
            color: #000;
            font-weight: bold;
        }

        .filter-btn .filter-count {
            display: inline-block;
            margin-left: 8px;
            padding: 2px 6px;
            background: rgba(255, 221, 0, 0.2);
            border-radius: 10px;
            font-size: 0.75rem;
            font-weight: bold;
        }

        .filter-btn.active .filter-count {
            background: rgba(0, 0, 0, 0.3);
            color: #000;
        }

        .empty-state {
            background: rgba(255, 221, 0, 0.05);
            border: 2px dashed rgba(255, 221, 0, 0.3);
        }

        .no-results-state {
            background: rgba(255, 100, 100, 0.05);
            border: 2px dashed rgba(255, 100, 100, 0.3);
            display: none;
        }

        .modal-overlay {
            background: rgba(0, 0, 0, 0.8);
            backdrop-filter: blur(10px);
        }

        .modal-content {
            animation: modalSlideIn 0.3s ease-out;
        }

        @keyframes modalSlideIn {
            0% { transform: scale(0.9) translateY(-10px); opacity: 0; }
            100% { transform: scale(1) translateY(0); opacity: 1; }
        }

        .action-btn {
            transition: all 0.2s ease;
        }

        .action-btn:hover {
            transform: scale(1.1);
        }

        .stats-counter {
            font-family: 'Courier New', monospace;
            font-weight: bold;
        }

        .clear-search {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(255, 221, 0, 0.2);
            color: #ffdd00;
            border: none;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            display: none;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .clear-search:hover {
            background: rgba(255, 221, 0, 0.3);
            transform: translateY(-50%) scale(1.1);
        }

        .clear-search.show {
            display: flex;
        }
    </style>

    <div class="py-8 dashboard-container">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Search and Filters -->
            <div class="glass-card rounded-2xl p-6 mb-8 animate-fade-in">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
                    <!-- Search Bar -->
                    <div class="flex-1 max-w-2xl">
                        <div class="relative">
                            <input type="text" 
                                   placeholder="Search by name, username, email, or website..." 
                                   class="w-full pl-12 pr-12 py-4 rounded-xl search-input"
                                   id="searchInput">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                            <button id="clearSearch" class="clear-search" title="Clear search">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Filter Buttons with Counters -->
                    <div class="flex items-center space-x-3">
                        <button class="filter-btn px-6 py-3 rounded-xl text-sm font-semibold active" data-filter="all">
                            {{ __('All') }}<span class="filter-count" id="count-all">0</span>
                        </button>
                        <button class="filter-btn px-6 py-3 rounded-xl text-sm font-semibold" data-filter="active">
                            {{ __('Active') }}<span class="filter-count" id="count-active">0</span>
                        </button>
                        <button class="filter-btn px-6 py-3 rounded-xl text-sm font-semibold" data-filter="expiring">
                            {{ __('Expiring') }}<span class="filter-count" id="count-expiring">0</span>
                        </button>
                        <button class="filter-btn px-6 py-3 rounded-xl text-sm font-semibold" data-filter="expired">
                            {{ __('Expired') }}<span class="filter-count" id="count-expired">0</span>
                        </button>
                    </div>
                </div>
                
                <!-- Results Summary -->
                <div class="mt-4 pt-4 border-t border-yellow-400/20">
                    <div class="flex items-center justify-between text-sm">
                        <div class="text-gray-400">
                            <span id="resultsText">Showing all credentials</span>
                        </div>
                        <div class="flex items-center space-x-4 text-gray-400">
                            <span>Total: <span class="stats-counter text-yellow-400" id="totalCount">0</span></span>
                            <span>Filtered: <span class="stats-counter text-yellow-400" id="filteredCount">0</span></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Credentials Grid -->
            <div id="credentialsContainer">
                @if(isset($credentials) && $credentials->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="credentialsGrid">
                        @foreach($credentials as $credential)
                            <div class="credential-card rounded-xl p-6 animate-fade-in" 
                                 data-credential-id="{{ $credential->id }}"
                                 data-name="{{ strtolower($credential->name) }}"
                                 data-username="{{ strtolower($credential->username ?? '') }}"
                                 data-email="{{ strtolower($credential->email ?? '') }}"
                                 data-website="{{ strtolower($credential->website_url ? parse_url($credential->website_url, PHP_URL_HOST) : '') }}"
                                 data-category="{{ strtolower($credential->category->name ?? 'uncategorized') }}"
                                 data-status="{{ 
                                    $credential->expires_at && $credential->expires_at->isPast() ? 'expired' : 
                                    ($credential->expires_at && $credential->expires_at->diffInDays() <= 30 ? 'expiring' : 'active') 
                                 }}">
                                
                                <!-- Credential Header -->
                                <div class="flex items-start justify-between mb-4">
                                    <div class="flex-1">
                                        <h3 class="text-lg font-semibold text-white mb-1">{{ $credential->name }}</h3>
                                        <p class="text-sm text-gray-400">{{ $credential->category->name ?? 'Uncategorized' }}</p>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        @if($credential->expires_at && $credential->expires_at->isPast())
                                            <span class="px-2 py-1 text-xs font-medium bg-red-500/20 text-red-400 rounded-full status-badge">
                                                {{ __('Expired') }}
                                            </span>
                                        @elseif($credential->expires_at && $credential->expires_at->diffInDays() <= 30)
                                            <span class="px-2 py-1 text-xs font-medium bg-orange-500/20 text-orange-400 rounded-full status-badge">
                                                {{ __('Expiring') }}
                                            </span>
                                        @else
                                            <span class="px-2 py-1 text-xs font-medium bg-green-500/20 text-green-400 rounded-full status-badge">
                                                {{ __('Active') }}
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Credential Details -->
                                <div class="space-y-2 mb-4">
                                    @if($credential->username)
                                        <div class="flex items-center text-sm">
                                            <svg class="w-4 h-4 text-yellow-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                            </svg>
                                            <span class="text-gray-300">{{ $credential->username }}</span>
                                        </div>
                                    @endif
                                    @if($credential->email)
                                        <div class="flex items-center text-sm">
                                            <svg class="w-4 h-4 text-yellow-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                                            </svg>
                                            <span class="text-gray-300">{{ $credential->email }}</span>
                                        </div>
                                    @endif
                                    @if($credential->website_url)
                                        <div class="flex items-center text-sm">
                                            <svg class="w-4 h-4 text-yellow-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                                            </svg>
                                            <span class="text-gray-300 truncate">{{ parse_url($credential->website_url, PHP_URL_HOST) }}</span>
                                        </div>
                                    @endif
                                </div>

                                <!-- Actions -->
                                <div class="flex items-center justify-between pt-4 border-t border-yellow-400/20">
                                    <div class="flex items-center space-x-2">
                                        <a href="{{ route('credentials.show', $credential) }}" 
                                           class="action-btn p-2 text-blue-400 hover:text-blue-300 hover:bg-blue-400/10 rounded-lg transition-all"
                                           title="View Details">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </a>
                                        <a href="{{ route('credentials.edit', $credential) }}" 
                                           class="action-btn p-2 text-yellow-400 hover:text-yellow-300 hover:bg-yellow-400/10 rounded-lg transition-all"
                                           title="Edit Credential">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </a>
                                        <button onclick="showDeleteModal('{{ route('credentials.destroy', $credential) }}', '{{ addslashes($credential->name) }}')" 
                                                class="action-btn p-2 text-red-400 hover:text-red-300 hover:bg-red-400/10 rounded-lg transition-all"
                                                title="Delete Credential">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </div>
                                    
                                    <div class="text-xs text-gray-400">
                                        {{ __('Updated') }} {{ $credential->updated_at->diffForHumans() }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- No Results State -->
                    <div id="noResultsState" class="glass-card rounded-xl p-12 text-center animate-fade-in no-results-state">
                        <svg class="mx-auto h-24 w-24 text-red-400/50 mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6-4h6m2 5.291A7.962 7.962 0 0118 12a8 8 0 01-8 8 8 8 0 01-8-8 8 8 0 018-8c2.027 0 3.9.753 5.334 2H8.5"/>
                        </svg>
                        <h3 class="text-xl font-semibold text-white mb-4">{{ __('No Matching Credentials') }}</h3>
                        <p class="text-gray-400 mb-8 max-w-md mx-auto">
                            {{ __('No credentials match your current search or filter criteria. Try adjusting your search terms or selecting a different filter.') }}
                        </p>
                        <button onclick="clearAllFilters()" 
                               class="inline-flex items-center space-x-2 bg-gradient-to-r from-red-600 to-red-500 text-white hover:from-red-500 hover:to-red-400 px-6 py-3 rounded-lg font-semibold transition-all duration-300 transform hover:scale-105">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            <span>{{ __('Clear All Filters') }}</span>
                        </button>
                    </div>

                    <!-- Pagination -->
                    @if(method_exists($credentials, 'links'))
                        <div class="mt-8">
                            {{ $credentials->links() }}
                        </div>
                    @endif
                    
                @else
                    <!-- Empty State -->
                    <div class="glass-card rounded-xl p-12 text-center animate-fade-in">
                        <div class="empty-state rounded-xl p-8">
                            <svg class="mx-auto h-24 w-24 text-yellow-400/50 mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-3a1 1 0 011-1h2.586l6.414-6.414A6 6 0 0121 9z"/>
                            </svg>
                            <h3 class="text-xl font-semibold text-white mb-4">{{ __('No Credentials Found') }}</h3>
                            <p class="text-gray-400 mb-8 max-w-md mx-auto">
                                {{ __('Start securing your digital life by adding your first credential. Your passwords and sensitive information will be encrypted and protected.') }}
                            </p>
                            <a href="{{ route('credentials.create') }}" 
                               class="inline-flex items-center space-x-2 bg-gradient-to-r from-yellow-600 to-yellow-500 text-black hover:from-yellow-500 hover:to-yellow-400 px-6 py-3 rounded-lg font-semibold transition-all duration-300 transform hover:scale-105">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                <span>{{ __('Add Your First Credential') }}</span>
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Enhanced Delete Confirmation Modal -->
    <div id="deleteModal" class="fixed inset-0 modal-overlay hidden items-center justify-center z-50">
        <div class="glass-card rounded-xl p-6 max-w-md w-full mx-4 modal-content">
            <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-500/20 mb-4">
                    <svg class="h-8 w-8 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-white mb-2">{{ __('Delete Credential') }}</h3>
                <p class="text-gray-400 mb-2">{{ __('Are you sure you want to delete') }}</p>
                <p class="text-yellow-400 font-semibold mb-4" id="deleteCredentialName">"Credential Name"</p>
                <p class="text-gray-400 text-sm mb-6">{{ __('This action cannot be undone and all associated data will be permanently removed.') }}</p>
                
                <div class="flex justify-center space-x-4">
                    <button onclick="closeDeleteModal()" 
                            class="px-6 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-all duration-200 transform hover:scale-105">
                        {{ __('Cancel') }}
                    </button>
                    <form id="deleteForm" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="px-6 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-all duration-200 transform hover:scale-105 flex items-center space-x-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            <span>{{ __('Delete Permanently') }}</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Global variables
        let allCredentials = [];
        let currentFilter = 'all';
        let currentSearchTerm = '';

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            initializeCredentials();
            setupEventListeners();
            updateCounts();
            updateResultsText();
        });

        // Initialize credentials array
        function initializeCredentials() {
            const credentialCards = document.querySelectorAll('.credential-card');
            allCredentials = Array.from(credentialCards).map(card => ({
                element: card,
                id: card.dataset.credentialId,
                name: card.dataset.name,
                username: card.dataset.username,
                email: card.dataset.email,
                website: card.dataset.website,
                category: card.dataset.category,
                status: card.dataset.status
            }));
        }

        // Setup all event listeners
        function setupEventListeners() {
            // Search functionality
            const searchInput = document.getElementById('searchInput');
            const clearSearchBtn = document.getElementById('clearSearch');
            
            if (searchInput) {
                searchInput.addEventListener('input', handleSearch);
                searchInput.addEventListener('keyup', function(e) {
                    if (e.key === 'Escape') {
                        clearSearch();
                    }
                });
            }

            if (clearSearchBtn) {
                clearSearchBtn.addEventListener('click', clearSearch);
            }

            // Filter functionality
            document.querySelectorAll('.filter-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    setActiveFilter(this.dataset.filter);
                });
            });

            // Modal event listeners
            const modal = document.getElementById('deleteModal');
            if (modal) {
                modal.addEventListener('click', function(e) {
                    if (e.target === this) {
                        closeDeleteModal();
                    }
                });
            }

            // Keyboard shortcuts
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closeDeleteModal();
                }
                if (e.ctrlKey && e.key === 'f') {
                    e.preventDefault();
                    searchInput.focus();
                }
            });
        }

        // Search functionality
        function handleSearch(e) {
            currentSearchTerm = e.target.value.toLowerCase().trim();
            const clearBtn = document.getElementById('clearSearch');
            
            if (currentSearchTerm) {
                clearBtn.classList.add('show');
            } else {
                clearBtn.classList.remove('show');
            }
            
            filterAndDisplayCredentials();
            updateResultsText();
        }

        function clearSearch() {
            const searchInput = document.getElementById('searchInput');
            const clearBtn = document.getElementById('clearSearch');
            
            searchInput.value = '';
            currentSearchTerm = '';
            clearBtn.classList.remove('show');
            
            filterAndDisplayCredentials();
            updateResultsText();
            searchInput.focus();
        }

        // Filter functionality
        function setActiveFilter(filter) {
            // Update button states
            document.querySelectorAll('.filter-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            document.querySelector(`[data-filter="${filter}"]`).classList.add('active');
            
            currentFilter = filter;
            filterAndDisplayCredentials();
            updateResultsText();
        }

        // Main filtering logic
        function filterAndDisplayCredentials() {
            let visibleCount = 0;
            const noResultsState = document.getElementById('noResultsState');
            const credentialsGrid = document.getElementById('credentialsGrid');
            
            allCredentials.forEach(credential => {
                let visible = true;
                
                // Apply search filter
                if (currentSearchTerm) {
                    const searchFields = [
                        credential.name,
                        credential.username,
                        credential.email,
                        credential.website,
                        credential.category
                    ].join(' ').toLowerCase();
                    
                    visible = searchFields.includes(currentSearchTerm);
                }
                
                // Apply status filter
                if (visible && currentFilter !== 'all') {
                    visible = credential.status === currentFilter;
                }
                
                // Show/hide credential
                if (visible) {
                    credential.element.style.display = 'block';
                    credential.element.classList.remove('hidden-card');
                    visibleCount++;
                } else {
                    credential.element.style.display = 'none';
                    credential.element.classList.add('hidden-card');
                }
            });
            
            // Update filtered count
            document.getElementById('filteredCount').textContent = visibleCount;
            
            // Show/hide no results state
            if (visibleCount === 0 && allCredentials.length > 0) {
                if (noResultsState) noResultsState.style.display = 'block';
                if (credentialsGrid) credentialsGrid.style.opacity = '0.3';
            } else {
                if (noResultsState) noResultsState.style.display = 'none';
                if (credentialsGrid) credentialsGrid.style.opacity = '1';
            }
        }

        // Update filter button counters
        function updateCounts() {
            const counts = {
                all: allCredentials.length,
                active: allCredentials.filter(c => c.status === 'active').length,
                expiring: allCredentials.filter(c => c.status === 'expiring').length,
                expired: allCredentials.filter(c => c.status === 'expired').length
            };
            
            Object.keys(counts).forEach(status => {
                const element = document.getElementById(`count-${status}`);
                if (element) {
                    element.textContent = counts[status];
                }
            });
            
            document.getElementById('totalCount').textContent = allCredentials.length;
            document.getElementById('filteredCount').textContent = allCredentials.length;
        }

        // Update results text
        function updateResultsText() {
            const resultsText = document.getElementById('resultsText');
            let text = '';
            
            if (currentSearchTerm && currentFilter !== 'all') {
                text = `Searching "${currentSearchTerm}" in ${currentFilter} credentials`;
            } else if (currentSearchTerm) {
                text = `Searching "${currentSearchTerm}" in all credentials`;
            } else if (currentFilter !== 'all') {
                text = `Showing ${currentFilter} credentials`;
            } else {
                text = 'Showing all credentials';
            }
            
            if (resultsText) {
                resultsText.textContent = text;
            }
        }

        // Clear all filters
        function clearAllFilters() {
            clearSearch();
            setActiveFilter('all');
        }

        // Delete modal functions
        function showDeleteModal(deleteUrl, credentialName) {
            const modal = document.getElementById('deleteModal');
            const form = document.getElementById('deleteForm');
            const nameElement = document.getElementById('deleteCredentialName');
            
            // Set the form action to the proper Laravel route
            if (form) form.action = deleteUrl;
            if (nameElement) nameElement.textContent = `"${credentialName}"`;
            
            if (modal) {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                
                // Focus on cancel button for accessibility
                setTimeout(() => {
                    const cancelBtn = modal.querySelector('button[onclick="closeDeleteModal()"]');
                    if (cancelBtn) cancelBtn.focus();
                }, 100);
            }
        }

        function closeDeleteModal() {
            const modal = document.getElementById('deleteModal');
            if (modal) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }
        }

        // Add some nice visual feedback for actions
        function showToast(message, type = 'success') {
            const toast = document.createElement('div');
            toast.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 ${
                type === 'success' ? 'bg-green-500' : 'bg-red-500'
            } text-white animate-slide-up`;
            toast.textContent = message;
            
            document.body.appendChild(toast);
            
            setTimeout(() => {
                toast.classList.add('animate-fade-out');
                setTimeout(() => {
                    document.body.removeChild(toast);
                }, 300);
            }, 3000);
        }

        // Add keyboard navigation for filter buttons
        document.addEventListener('keydown', function(e) {
            if (e.altKey) {
                switch(e.key) {
                    case '1':
                        e.preventDefault();
                        setActiveFilter('all');
                        break;
                    case '2':
                        e.preventDefault();
                        setActiveFilter('active');
                        break;
                    case '3':
                        e.preventDefault();
                        setActiveFilter('expiring');
                        break;
                    case '4':
                        e.preventDefault();
                        setActiveFilter('expired');
                        break;
                }
            }
        });
    </script>
</x-app-layout>