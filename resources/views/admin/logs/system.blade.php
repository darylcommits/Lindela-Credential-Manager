{{-- File: resources/views/admin/logs/system.blade.php --}}
{{-- 
    IMPORTANT: For filtering to work properly, your Laravel Controller needs to handle these request parameters:
    
    Controller Example (app/Http/Controllers/Admin/LogController.php):
    
    public function system(Request $request)
    {
        $query = SystemLog::query(); // Replace with your actual log model
        
        // Date filtering (date-only, no time)
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        // Type filtering
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        
        // Order by newest first
        $query->orderBy('created_at', 'desc');
        
        // Paginate results
        $logs = $query->paginate(20);
        
        return view('admin.logs.system', compact('logs'));
    }
    
    Note: Using whereDate() ensures proper date filtering without time components.
    For single-date filters (like "Yesterday Only"), date_from and date_to will be the same.
--}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col xl:flex-row xl:justify-between xl:items-center gap-3 lg:gap-4 animate-slide-up">
            <div class="flex-1 min-w-0">
                <h2 class="text-xl sm:text-2xl lg:text-3xl font-bold gradient-text truncate">System Logs</h2>
                <p class="text-gray-300 mt-1 text-xs sm:text-sm lg:text-base">Monitor system activity and access logs</p>
            </div>
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 lg:gap-3 shrink-0">
                <a href="{{ route('admin.logs.export') }}" 
                   class="bg-gradient-to-r from-green-600 to-green-500 text-white hover:from-green-500 hover:to-green-400 px-3 sm:px-4 lg:px-6 py-2 lg:py-3 rounded-lg font-semibold transition-all duration-300 transform hover:scale-105 flex items-center justify-center space-x-2 shadow-lg text-xs sm:text-sm lg:text-base">
                    <svg class="w-3 h-3 sm:w-4 sm:h-4 lg:w-5 lg:h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <span class="whitespace-nowrap">Export Logs</span>
                </a>
                <a href="{{ route('admin.logs.credential-access') }}" 
                   class="bg-gradient-to-r from-yellow-600 to-yellow-500 text-black hover:from-yellow-500 hover:to-yellow-400 px-4 sm:px-6 py-2 sm:py-3 rounded-lg font-semibold transition-all duration-300 transform hover:scale-105 flex items-center justify-center space-x-2 shadow-lg text-sm sm:text-base">
                    <svg class="w-3 h-3 sm:w-4 sm:h-4 lg:w-5 lg:h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1721 9z"/>
                    </svg>
                    <span class="hidden md:inline whitespace-nowrap">Credential Access Logs</span>
                    <span class="md:hidden whitespace-nowrap">Credentials</span>
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

        @keyframes slideUp {
            0% { transform: translateY(20px); opacity: 0; }
            100% { transform: translateY(0); opacity: 1; }
        }

        @keyframes fadeIn {
            0% { opacity: 0; }
            100% { opacity: 1; }
        }

        .log-item {
            background: rgba(26, 26, 26, 0.8);
            border: 1px solid rgba(255, 221, 0, 0.1);
            transition: all 0.3s ease;
        }

        .log-item:hover {
            border-color: rgba(255, 221, 0, 0.3);
            transform: translateX(2px);
        }

        .section-header {
            border-bottom: 2px solid rgba(255, 221, 0, 0.2);
            position: relative;
        }

        .section-header::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 60px;
            height: 2px;
            background: linear-gradient(90deg, #ffdd00, #f59e0b);
        }

        /* Custom Pagination Styles */
        .pagination-container {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 4px;
            margin-top: 1.5rem;
            flex-wrap: wrap;
        }

        .pagination-btn {
            background: rgba(255, 221, 0, 0.1);
            border: 1px solid rgba(255, 221, 0, 0.3);
            color: #ffdd00;
            padding: 6px 12px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 12px;
            font-weight: 500;
            transition: all 0.3s ease;
            min-width: 36px;
            text-align: center;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 4px;
        }

        .pagination-btn:hover:not(.disabled) {
            background: rgba(255, 221, 0, 0.2);
            border-color: #ffdd00;
            color: #ffdd00;
        }

        .pagination-btn.active {
            background: rgba(255, 221, 0, 0.3);
            border-color: #ffdd00;
            color: #000;
            font-weight: 700;
        }

        .pagination-btn.disabled {
            background: rgba(255, 255, 255, 0.05);
            border-color: rgba(255, 255, 255, 0.1);
            color: rgba(255, 255, 255, 0.3);
            cursor: not-allowed;
        }

        .pagination-info {
            color: #ffdd00;
            font-size: 12px;
            margin: 8px 0;
            text-align: center;
            width: 100%;
        }

        /* Responsive Design */
        @media (min-width: 640px) {
            .pagination-btn {
                font-size: 14px;
                padding: 8px 16px;
                min-width: 44px;
            }
            .pagination-info {
                font-size: 14px;
                margin: 16px 0;
            }
        }

        @media (max-width: 480px) {
            .log-item:hover {
                transform: none;
            }
            .pagination-container {
                gap: 2px;
            }
            .pagination-btn {
                font-size: 11px;
                padding: 4px 8px;
                min-width: 32px;
            }
        }

        /* Loading States */
        .loading {
            opacity: 0.6;
            pointer-events: none;
        }

        .spinner {
            border: 2px solid rgba(255, 221, 0, 0.3);
            border-radius: 50%;
            border-top: 2px solid #ffdd00;
            width: 16px;
            height: 16px;
            animation: spin 1s linear infinite;
            display: inline-block;
            margin-right: 8px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Calendar Input Styling */
        .calendar-input::-webkit-calendar-picker-indicator {
            background: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20' fill='%23FFDD00'%3e%3cpath fill-rule='evenodd' d='M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z' clip-rule='evenodd'/%3e%3c/svg%3e") no-repeat;
            background-size: 16px 16px;
            cursor: pointer;
            width: 20px;
            height: 20px;
            filter: brightness(1.2);
        }

        input[type="date"]::-webkit-calendar-picker-indicator {
            background: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20' fill='%23FFDD00'%3e%3cpath fill-rule='evenodd' d='M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z' clip-rule='evenodd'/%3e%3c/svg%3e") no-repeat;
            background-size: 16px 16px;
            cursor: pointer;
            width: 20px;
            height: 20px;
            opacity: 1;
            filter: brightness(1.2);
        }

        /* Style the date input text */
        input[type="date"] {
            color-scheme: dark;
        }

        input[type="date"]::-webkit-datetime-edit {
            color: white;
        }

        input[type="date"]::-webkit-datetime-edit-fields-wrapper {
            color: white;
        }

        input[type="date"]::-webkit-datetime-edit-text {
            color: rgba(255, 255, 255, 0.7);
        }

        input[type="date"]::-webkit-datetime-edit-month-field,
        input[type="date"]::-webkit-datetime-edit-day-field,
        input[type="date"]::-webkit-datetime-edit-year-field {
            color: white;
        }
    </style>

    <div class="py-3 sm:py-4 lg:py-6 xl:py-8 dashboard-container">
        <div class="max-w-7xl mx-auto px-3 sm:px-4 lg:px-6 xl:px-8">
            <!-- Filters -->
            <div class="glass-card rounded-lg lg:rounded-xl xl:rounded-2xl p-3 sm:p-4 lg:p-6 mb-4 sm:mb-6 lg:mb-8 animate-fade-in">
                <div class="space-y-3 lg:space-y-4">
                    <!-- Filter Title -->
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 lg:gap-4">
                        <h3 class="text-sm sm:text-base lg:text-lg font-semibold text-white">Filter Logs</h3>
                        <div class="flex items-center gap-2">
                            <span class="text-xs sm:text-sm text-gray-400" id="filterStatus">
                                @php
                                    $activeFilters = [];
                                    $dateFrom = request('date_from');
                                    $dateTo = request('date_to');
                                    $type = request('type');
                                    
                                    // Check if it's a single date filter
                                    $isSingleDate = $dateFrom && $dateTo && $dateFrom === $dateTo;
                                    
                                    if($isSingleDate) {
                                        $date = \Carbon\Carbon::parse($dateFrom);
                                        $today = \Carbon\Carbon::today();
                                        $yesterday = \Carbon\Carbon::yesterday();
                                        
                                        if($date->isSameDay($today)) {
                                            $dateLabel = "Today's logs";
                                        } elseif($date->isSameDay($yesterday)) {
                                            $dateLabel = "Yesterday's logs";
                                        } else {
                                            $dateLabel = $date->format('M d, Y') . " logs";
                                        }
                                        
                                        if($type) {
                                            $dateLabel .= " (" . ucfirst($type) . " only)";
                                        }
                                        
                                        $statusText = "Showing: " . $dateLabel;
                                    } else {
                                        if($dateFrom) {
                                            $fromDate = \Carbon\Carbon::parse($dateFrom);
                                            $activeFilters[] = 'From: ' . $fromDate->format('M d, Y');
                                        }
                                        if($dateTo) {
                                            $toDate = \Carbon\Carbon::parse($dateTo);
                                            $activeFilters[] = 'To: ' . $toDate->format('M d, Y');
                                        }
                                        if($type) {
                                            $activeFilters[] = 'Type: ' . ucfirst($type);
                                        }
                                        
                                        $statusText = count($activeFilters) > 0 ? 'Filtered by: ' . implode(', ', $activeFilters) : 'No filters applied';
                                    }
                                @endphp
                                @if(count($activeFilters) > 0 || $isSingleDate)
                                    <span class="text-yellow-400">{{ $statusText }}</span>
                                    <span class="inline-flex items-center ml-2 px-2 py-1 bg-yellow-500/20 text-yellow-400 rounded-full text-xs">
                                        {{ $isSingleDate ? '1 date filter' : count($activeFilters) . ' filter' . (count($activeFilters) > 1 ? 's' : '') }} active
                                    </span>
                                @else
                                    No filters applied
                                @endif
                            </span>
                            <button onclick="clearFilters()" id="clearBtn" 
                                    class="text-xs text-orange-400 hover:text-orange-300 underline transition-colors duration-200"
                                    style="display: {{ (request()->has('date_from') || request()->has('date_to') || request()->has('type')) ? 'inline' : 'none' }}">
                                Clear All
                            </button>
                        </div>
                    </div>
                    
                    <!-- Filter Controls -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2 lg:gap-3">
                        <div class="space-y-1">
                            <label class="text-xs text-gray-300 block">From Date</label>
                            <input type="date" 
                                   id="dateFromFilter" 
                                   value="{{ request('date_from') }}"
                                   onchange="autoApplyFilters()"
                                   class="w-full bg-black/40 border border-yellow-400/30 text-white rounded-lg px-2 lg:px-3 py-2 text-xs sm:text-sm focus:outline-none focus:border-yellow-400 focus:ring-1 focus:ring-yellow-400 calendar-input">
                        </div>
                        
                        <div class="space-y-1">
                            <label class="text-xs text-gray-300 block">To Date</label>
                            <input type="date" 
                                   id="dateToFilter" 
                                   value="{{ request('date_to') }}"
                                   onchange="autoApplyFilters()"
                                   class="w-full bg-black/40 border border-yellow-400/30 text-white rounded-lg px-2 lg:px-3 py-2 text-xs sm:text-sm focus:outline-none focus:border-yellow-400 focus:ring-1 focus:ring-yellow-400 calendar-input">
                        </div>
                        
                        <div class="space-y-1">
                            <label class="text-xs text-gray-300 block">Log Type</label>
                            <select id="typeFilter" 
                                    onchange="autoApplyFilters()"
                                    class="w-full bg-black/40 border border-yellow-400/30 text-white rounded-lg px-2 lg:px-3 py-2 text-xs sm:text-sm focus:outline-none focus:border-yellow-400 focus:ring-1 focus:ring-yellow-400">
                                <option value="" {{ !request('type') ? 'selected' : '' }}>All Types</option>
                                <option value="system" {{ request('type') == 'system' ? 'selected' : '' }}>System</option>
                                <option value="user" {{ request('type') == 'user' ? 'selected' : '' }}>User</option>
                                <option value="credential" {{ request('type') == 'credential' ? 'selected' : '' }}>Credential</option>
                                <option value="otp" {{ request('type') == 'otp' ? 'selected' : '' }}>OTP</option>
                            </select>
                        </div>
                        
                        <div class="space-y-1">
                            <label class="text-xs text-gray-300 block">Quick Filters</label>
                            <select id="quickFilter" 
                                    onchange="applyQuickFilter()"
                                    class="w-full bg-black/40 border border-yellow-400/30 text-white rounded-lg px-2 lg:px-3 py-2 text-xs sm:text-sm focus:outline-none focus:border-yellow-400 focus:ring-1 focus:ring-yellow-400">
                                <option value="">Select Quick Filter</option>
                                <option value="today">Today Only</option>
                                <option value="yesterday">Yesterday Only</option>
                                <option value="last_week">Last 7 Days</option>
                                <option value="last_month">Last 30 Days</option>
                                <option value="this_month">This Month</option>
                            </select>
                        </div>
                        
                        <div class="space-y-1 sm:col-span-2 lg:col-span-4">
                            <label class="text-xs text-gray-300 block">&nbsp;</label>
                            <div class="flex gap-2">
                                <button onclick="setToday()" 
                                        class="flex-1 bg-gradient-to-r from-blue-600 to-blue-500 text-white hover:from-blue-500 hover:to-blue-400 px-3 py-2 rounded-lg font-semibold transition-all duration-300 text-xs sm:text-sm">
                                    Today Only
                                </button>
                                <button onclick="setYesterday()" 
                                        class="flex-1 bg-gradient-to-r from-purple-600 to-purple-500 text-white hover:from-purple-500 hover:to-purple-400 px-3 py-2 rounded-lg font-semibold transition-all duration-300 text-xs sm:text-sm">
                                    Yesterday Only
                                </button>
                                <button onclick="setThisWeek()" 
                                        class="flex-1 bg-gradient-to-r from-green-600 to-green-500 text-white hover:from-green-500 hover:to-green-400 px-3 py-2 rounded-lg font-semibold transition-all duration-300 text-xs sm:text-sm">
                                    This Week
                                </button>
                                <button onclick="clearFilters()" id="clearBtn" 
                                        class="flex-1 bg-gradient-to-r from-gray-600 to-gray-500 text-white hover:from-gray-500 hover:to-gray-400 px-3 py-2 rounded-lg font-semibold transition-all duration-300 text-xs sm:text-sm"
                                        style="display: {{ (request()->has('date_from') || request()->has('date_to') || request()->has('type')) ? 'block' : 'none' }}">
                                    Clear All
                                </button>
                            </div>
                        </div>
                    </div>
                    
                   
                    
                    <!-- Loading Indicator -->
                    <div id="filterLoading" class="hidden flex items-center justify-center py-2">
                        <div class="spinner mr-2"></div>
                        <span class="text-xs text-yellow-400">Applying filters...</span>
                    </div>
                </div>
            </div>

            <!-- System Logs -->
            <div class="glass-card rounded-lg lg:rounded-xl xl:rounded-2xl animate-fade-in">
                <div class="p-3 sm:p-4 lg:p-6 pb-2 sm:pb-3 lg:pb-4 section-header">
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2">
                        <h3 class="text-base sm:text-lg lg:text-xl font-bold text-white">System Activity Logs</h3>
                        <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-4">
                            <span class="text-xs lg:text-sm text-gray-400">
                                @if(isset($logs))
                                    Showing {{ $logs->count() }} of {{ $logs->total() }} logs
                                @else
                                    No logs available
                                @endif
                            </span>
                            <button onclick="refreshLogs()" id="refreshBtn"
                                    class="bg-gradient-to-r from-blue-600 to-blue-500 text-white hover:from-blue-500 hover:to-blue-400 px-2 lg:px-3 py-1 lg:py-2 rounded text-xs lg:text-sm font-medium transition-all duration-300">
                                <svg class="w-3 h-3 lg:w-4 lg:h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                                Refresh
                            </button>
                        </div>
                    </div>
                </div>

                <div class="p-3 sm:p-4 lg:p-6 pt-2 sm:pt-3 lg:pt-4" id="logsContent">
                    @if(isset($logs) && $logs->count() > 0)
                        {{-- Check if filters are applied but results seem unfiltered --}}
                        @if((request()->has('date_from') || request()->has('date_to') || request()->has('type')) && $logs->count() == $logs->total())
                            <div class="mb-4 p-3 bg-orange-500/20 border border-orange-500/30 rounded-lg">
                                <div class="flex items-start space-x-3">
                                    <svg class="w-5 h-5 text-orange-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16c-.77.833.192 2.5 1.732 2.5z"/>
                                    </svg>
                                    <div>
                                        <h4 class="text-sm font-medium text-orange-400">Backend Filtering Not Implemented</h4>
                                        <p class="mt-1 text-xs text-orange-300">
                                            Filters are applied in the URL but the controller isn't filtering the database query. 
                                            Please implement the filtering logic in your Laravel controller using the request parameters: 
                                            <code class="bg-black/30 px-1 rounded">date_from</code>, 
                                            <code class="bg-black/30 px-1 rounded">date_to</code>, and 
                                            <code class="bg-black/30 px-1 rounded">type</code>.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif
                        
                        <div class="space-y-2 lg:space-y-3" id="logsContainer">
                            @foreach($logs as $log)
                            <div class="log-item rounded-lg xl:rounded-xl p-2 sm:p-3 lg:p-4" 
                                 data-type="{{ $log->type ?? 'system' }}"
                                 data-datetime="{{ ($log->created_at ?? now())->toISOString() }}"
                                 data-date="{{ ($log->created_at ?? now())->format('Y-m-d') }}">
                                <div class="flex items-start space-x-2 sm:space-x-3 lg:space-x-4">
                                    <div class="flex-shrink-0">
                                        <div class="w-2 h-2 sm:w-2.5 sm:h-2.5 lg:w-3 lg:h-3 bg-yellow-500 rounded-full mt-1.5 sm:mt-2"></div>
                                    </div>
                                    
                                    <div class="min-w-0 flex-1">
                                        <div class="flex flex-col xl:flex-row xl:items-start xl:justify-between gap-1 sm:gap-2 lg:gap-4">
                                            <div class="flex-1 min-w-0">
                                                <p class="text-xs sm:text-sm lg:text-base font-medium text-white break-words leading-relaxed">
                                                    {{ $log->message ?? 'System Event' }}
                                                </p>
                                                <div class="mt-1 sm:mt-2 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-1 text-xs text-gray-400">
                                                    <span class="flex items-center space-x-1 truncate">
                                                        <svg class="w-2.5 h-2.5 sm:w-3 sm:h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                                        </svg>
                                                        <span class="truncate">{{ $log->user->name ?? 'System' }}</span>
                                                    </span>
                                                    <span class="flex items-center space-x-1 truncate">
                                                        <svg class="w-2.5 h-2.5 sm:w-3 sm:h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                        </svg>
                                                        <span class="truncate">{{ ($log->created_at ?? now())->format('M d, Y H:i:s') }}</span>
                                                    </span>
                                                    <span class="flex items-center space-x-1 truncate">
                                                        <svg class="w-2.5 h-2.5 sm:w-3 sm:h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 01-9 9m9-9H3m9 9v-9"/>
                                                        </svg>
                                                        <span class="truncate">{{ $log->ip_address ?? request()->ip() }}</span>
                                                    </span>
                                                    @if($log->type ?? false)
                                                        <span class="inline-flex items-center px-1.5 sm:px-2 py-0.5 sm:py-1 rounded-full text-xs font-medium mt-1 md:mt-0 {{ 
                                                            ($log->type === 'credential') ? 'bg-yellow-500/20 text-yellow-400' : 
                                                            (($log->type === 'otp') ? 'bg-blue-500/20 text-blue-400' : 
                                                            (($log->type === 'user') ? 'bg-green-500/20 text-green-400' : 'bg-gray-500/20 text-gray-400'))
                                                        }}">
                                                            {{ ucfirst($log->type) }}
                                                        </span>
                                                    @endif
                                                </div>
                                                
                                                @if($log->details ?? false)
                                                    <div class="mt-2 text-xs lg:text-sm text-gray-300 bg-black/30 rounded p-2">
                                                        <div class="break-all max-h-32 overflow-y-auto">
                                                            {{ is_string($log->details) ? $log->details : json_encode($log->details, JSON_PRETTY_PRINT) }}
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <!-- Custom Pagination -->
                        @if(method_exists($logs, 'hasPages') && $logs->hasPages())
                            <div class="pagination-container">
                                {{-- Previous Page Link --}}
                                @if($logs->onFirstPage())
                                    <span class="pagination-btn disabled">
                                        <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                        </svg>
                                        <span class="hidden sm:inline">Prev</span>
                                    </span>
                                @else
                                    <a href="{{ $logs->appends(['date_from' => request('date_from'), 'date_to' => request('date_to'), 'type' => request('type')])->previousPageUrl() }}" class="pagination-btn">
                                        <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                        </svg>
                                        <span class="hidden sm:inline">Prev</span>
                                    </a>
                                @endif

                                {{-- Pagination Elements --}}
                                @php
                                    $currentPage = $logs->currentPage();
                                    $lastPage = $logs->lastPage();
                                    $startPage = max(1, $currentPage - 2);
                                    $endPage = min($lastPage, $currentPage + 2);
                                @endphp

                                @if($startPage > 1)
                                    <a href="{{ $logs->appends(['date_from' => request('date_from'), 'date_to' => request('date_to'), 'type' => request('type')])->url(1) }}" class="pagination-btn">1</a>
                                    @if($startPage > 2)
                                        <span class="pagination-btn disabled">...</span>
                                    @endif
                                @endif

                                @for($page = $startPage; $page <= $endPage; $page++)
                                    @if($page == $currentPage)
                                        <span class="pagination-btn active">{{ $page }}</span>
                                    @else
                                        <a href="{{ $logs->appends(['date_from' => request('date_from'), 'date_to' => request('date_to'), 'type' => request('type')])->url($page) }}" class="pagination-btn">{{ $page }}</a>
                                    @endif
                                @endfor

                                @if($endPage < $lastPage)
                                    @if($endPage < $lastPage - 1)
                                        <span class="pagination-btn disabled">...</span>
                                    @endif
                                    <a href="{{ $logs->appends(['date_from' => request('date_from'), 'date_to' => request('date_to'), 'type' => request('type')])->url($lastPage) }}" class="pagination-btn">{{ $lastPage }}</a>
                                @endif

                                {{-- Next Page Link --}}
                                @if($logs->hasMorePages())
                                    <a href="{{ $logs->appends(['date_from' => request('date_from'), 'date_to' => request('date_to'), 'type' => request('type')])->nextPageUrl() }}" class="pagination-btn">
                                        <span class="hidden sm:inline">Next</span>
                                        <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                        </svg>
                                    </a>
                                @else
                                    <span class="pagination-btn disabled">
                                        <span class="hidden sm:inline">Next</span>
                                        <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                        </svg>
                                    </span>
                                @endif

                                {{-- Pagination Info --}}
                                <div class="pagination-info">
                                    Showing {{ $logs->firstItem() ?? 0 }} to {{ $logs->lastItem() ?? 0 }} of {{ $logs->total() ?? 0 }} results
                                </div>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-6 sm:py-8 lg:py-12">
                            <svg class="mx-auto h-8 w-8 sm:h-12 sm:w-12 lg:h-16 lg:w-16 text-gray-400/50 mb-3 sm:mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            <h3 class="text-sm sm:text-base lg:text-lg font-medium text-gray-400 mb-1 sm:mb-2">No logs found</h3>
                            @if(request()->has('date_from') || request()->has('date_to') || request()->has('type'))
                                <p class="text-xs sm:text-sm text-gray-500 mb-3">No system logs match your current filters.</p>
                                <div class="text-xs text-gray-400 mb-4">
                                    <strong>Applied Filters:</strong><br>
                                    @if(request('date_from'))
                                        From: {{ \Carbon\Carbon::parse(request('date_from'))->format('M d, Y') }}<br>
                                    @endif
                                    @if(request('date_to'))
                                        To: {{ \Carbon\Carbon::parse(request('date_to'))->format('M d, Y') }}<br>
                                    @endif
                                    @if(request('type'))
                                        Type: {{ ucfirst(request('type')) }}<br>
                                    @endif
                                    @if(request('date_from') && request('date_to') && request('date_from') === request('date_to'))
                                        <span class="text-yellow-400">(Single day filter)</span>
                                    @endif
                                </div>
                                <button onclick="clearFilters()" class="mt-3 sm:mt-4 text-xs sm:text-sm text-orange-400 hover:text-orange-300 underline transition-colors duration-200">
                                    Clear filters to see all logs
                                </button>
                            @else
                                <p class="text-xs sm:text-sm text-gray-500">No system logs available.</p>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        // Global state
        let isLoading = false;
        let filterTimeout = null;

        // Auto-apply filters with debouncing
        function autoApplyFilters() {
            if (isLoading) return;
            
            // Clear existing timeout
            if (filterTimeout) {
                clearTimeout(filterTimeout);
            }
            
            // Show loading indicator
            showFilterLoading(true);
            
            // Debounce the filter application
            filterTimeout = setTimeout(() => {
                applyFilters();
            }, 800);
        }

        // Apply filters function
        function applyFilters() {
            if (isLoading) return;
            
            setLoading(true);
            const dateFrom = document.getElementById('dateFromFilter').value;
            const dateTo = document.getElementById('dateToFilter').value;
            const type = document.getElementById('typeFilter').value;
            
            // Build query parameters
            const params = new URLSearchParams();
            
            if (dateFrom) {
                params.set('date_from', dateFrom);
                console.log('Setting date_from:', dateFrom);
            }
            
            if (dateTo) {
                params.set('date_to', dateTo);
                console.log('Setting date_to:', dateTo);
            }
            
            if (type) {
                params.set('type', type);
                console.log('Setting type:', type);
            }
            
            // Preserve other query parameters if any (except page)
            const currentParams = new URLSearchParams(window.location.search);
            currentParams.forEach((value, key) => {
                if (!['date_from', 'date_to', 'type', 'page'].includes(key)) {
                    params.set(key, value);
                }
            });
            
            // Build final URL
            const url = `${window.location.pathname}${params.toString() ? '?' + params.toString() : ''}`;
            console.log('Navigating to URL:', url);
            console.log('Filter parameters:', Object.fromEntries(params));
            
            // Navigate to filtered results
            window.location.href = url;
        }

        // Quick filter application
        function applyQuickFilter() {
            const quickFilter = document.getElementById('quickFilter').value;
            if (!quickFilter) return;
            
            const today = new Date();
            let fromDate = null;
            let toDate = null;
            
            switch (quickFilter) {
                case 'today':
                    // Show only today's logs
                    fromDate = formatDate(today);
                    toDate = formatDate(today);
                    break;
                case 'yesterday':
                    // Show only yesterday's logs
                    const yesterday = new Date(today);
                    yesterday.setDate(yesterday.getDate() - 1);
                    fromDate = formatDate(yesterday);
                    toDate = formatDate(yesterday);
                    break;
                case 'last_week':
                    // Show last 7 days
                    const lastWeek = new Date(today);
                    lastWeek.setDate(lastWeek.getDate() - 7);
                    fromDate = formatDate(lastWeek);
                    toDate = formatDate(today);
                    break;
                case 'last_month':
                    // Show last 30 days
                    const lastMonth = new Date(today);
                    lastMonth.setDate(lastMonth.getDate() - 30);
                    fromDate = formatDate(lastMonth);
                    toDate = formatDate(today);
                    break;
                case 'this_month':
                    // Show this month only
                    const firstDayOfMonth = new Date(today.getFullYear(), today.getMonth(), 1);
                    fromDate = formatDate(firstDayOfMonth);
                    toDate = formatDate(today);
                    break;
            }
            
            // Set the date inputs
            if (fromDate) document.getElementById('dateFromFilter').value = fromDate;
            if (toDate) document.getElementById('dateToFilter').value = toDate;
            
            // Reset quick filter dropdown
            document.getElementById('quickFilter').value = '';
            
            // Apply filters
            autoApplyFilters();
        }

        // Set specific date functions
        function setToday() {
            const today = new Date();
            const todayStr = formatDate(today);
            document.getElementById('dateFromFilter').value = todayStr;
            document.getElementById('dateToFilter').value = todayStr;
            autoApplyFilters();
        }

        function setYesterday() {
            const yesterday = new Date();
            yesterday.setDate(yesterday.getDate() - 1);
            const yesterdayStr = formatDate(yesterday);
            document.getElementById('dateFromFilter').value = yesterdayStr;
            document.getElementById('dateToFilter').value = yesterdayStr;
            autoApplyFilters();
        }

        function setThisWeek() {
            const today = new Date();
            const firstDayOfWeek = new Date(today);
            const day = today.getDay();
            const diff = today.getDate() - day + (day === 0 ? -6 : 1); // Monday as first day
            firstDayOfWeek.setDate(diff);
            
            document.getElementById('dateFromFilter').value = formatDate(firstDayOfWeek);
            document.getElementById('dateToFilter').value = formatDate(today);
            autoApplyFilters();
        }

        // Format date for input (YYYY-MM-DD)
        function formatDate(date) {
            return date.getFullYear() + '-' + 
                   String(date.getMonth() + 1).padStart(2, '0') + '-' + 
                   String(date.getDate()).padStart(2, '0');
        }

        // Clear all filters
        function clearFilters() {
            if (isLoading) return;
            
            setLoading(true);
            document.getElementById('dateFromFilter').value = '';
            document.getElementById('dateToFilter').value = '';
            document.getElementById('typeFilter').value = '';
            document.getElementById('quickFilter').value = '';
            
            // Navigate to page without filters
            window.location.href = window.location.pathname;
        }

        // Refresh logs without changing filters
        function refreshLogs() {
            if (isLoading) return;
            
            setLoading(true);
            window.location.reload();
        }

        // Show/hide filter loading indicator
        function showFilterLoading(show) {
            const loadingEl = document.getElementById('filterLoading');
            if (loadingEl) {
                if (show) {
                    loadingEl.classList.remove('hidden');
                } else {
                    loadingEl.classList.add('hidden');
                }
            }
        }

        // Set loading state
        function setLoading(loading) {
            isLoading = loading;
            const refreshBtn = document.getElementById('refreshBtn');
            const logsContent = document.getElementById('logsContent');
            const clearBtn = document.getElementById('clearBtn');
            
            if (loading) {
                if (refreshBtn) {
                    refreshBtn.disabled = true;
                    refreshBtn.innerHTML = '<div class="spinner"></div>Loading...';
                }
                if (clearBtn) clearBtn.style.pointerEvents = 'none';
                if (logsContent) logsContent.classList.add('loading');
                showFilterLoading(false);
            } else {
                if (refreshBtn) {
                    refreshBtn.disabled = false;
                    refreshBtn.innerHTML = `
                        <svg class="w-3 h-3 lg:w-4 lg:h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        Refresh
                    `;
                }
                if (clearBtn) clearBtn.style.pointerEvents = 'auto';
                if (logsContent) logsContent.classList.remove('loading');
                showFilterLoading(false);
            }
        }

        // Update filter status display
        function updateFilterStatus() {
            const statusEl = document.getElementById('filterStatus');
            const clearBtn = document.getElementById('clearBtn');
            const dateFrom = document.getElementById('dateFromFilter').value;
            const dateTo = document.getElementById('dateToFilter').value;
            const type = document.getElementById('typeFilter').value;
            
            let status = 'No filters applied';
            const hasFilters = dateFrom || dateTo || type;
            
            if (hasFilters) {
                const filters = [];
                if (dateFrom) {
                    const fromDate = new Date(dateFrom);
                    filters.push(`From: ${fromDate.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}`);
                }
                if (dateTo) {
                    const toDate = new Date(dateTo);
                    filters.push(`To: ${toDate.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}`);
                }
                if (type) filters.push(`Type: ${type.charAt(0).toUpperCase() + type.slice(1)}`);
                
                // Show special cases
                if (dateFrom && dateTo && dateFrom === dateTo) {
                    const singleDate = new Date(dateFrom);
                    const today = new Date();
                    const yesterday = new Date(today);
                    yesterday.setDate(yesterday.getDate() - 1);
                    
                    if (dateFrom === formatDate(today)) {
                        status = `Showing: Today's logs${type ? ` (${type.charAt(0).toUpperCase() + type.slice(1)} only)` : ''}`;
                    } else if (dateFrom === formatDate(yesterday)) {
                        status = `Showing: Yesterday's logs${type ? ` (${type.charAt(0).toUpperCase() + type.slice(1)} only)` : ''}`;
                    } else {
                        status = `Showing: ${singleDate.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })} logs${type ? ` (${type.charAt(0).toUpperCase() + type.slice(1)} only)` : ''}`;
                    }
                } else {
                    status = `Filtered by: ${filters.join(', ')}`;
                }
            }
            
            if (statusEl) statusEl.textContent = status;
            if (clearBtn) {
                clearBtn.style.display = hasFilters ? 'block' : 'none';
            }
        }

        // Validate date range
        function validateDateRange() {
            const dateFrom = document.getElementById('dateFromFilter').value;
            const dateTo = document.getElementById('dateToFilter').value;
            
            if (dateFrom && dateTo && new Date(dateFrom) > new Date(dateTo)) {
                alert('From date cannot be later than To date');
                return false;
            }
            return true;
        }

        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // Only enable on larger screens
            if (window.innerWidth <= 768) return;
            
            if (e.ctrlKey || e.metaKey) {
                switch(e.key) {
                    case 'r':
                        e.preventDefault();
                        refreshLogs();
                        break;
                    case 'f':
                        e.preventDefault();
                        document.getElementById('dateFromFilter').focus();
                        break;
                    case '1':
                        e.preventDefault();
                        setToday();
                        break;
                    case '2':
                        e.preventDefault();
                        setYesterday();
                        break;
                }
            }
            
            // Clear filters with Escape key
            if (e.key === 'Escape') {
                clearFilters();
            }
        });

        // Auto-apply filters on input change with validation
        document.getElementById('dateFromFilter').addEventListener('change', function() {
            if (validateDateRange()) {
                updateFilterStatus();
                autoApplyFilters();
            }
        });

        document.getElementById('dateToFilter').addEventListener('change', function() {
            if (validateDateRange()) {
                updateFilterStatus();
                autoApplyFilters();
            }
        });

        document.getElementById('typeFilter').addEventListener('change', function() {
            updateFilterStatus();
            autoApplyFilters();
        });

        // Initialize page
        document.addEventListener('DOMContentLoaded', function() {
            updateFilterStatus();
            
            // Remove loading state after page load
            setTimeout(() => setLoading(false), 100);
            
            // Set max date for inputs to current date
            const today = formatDate(new Date());
            document.getElementById('dateFromFilter').max = today;
            document.getElementById('dateToFilter').max = today;
            
            // Auto-refresh setup (optional - uncomment if needed)
            // setInterval(refreshLogs, 300000); // Refresh every 5 minutes
        });

        // Handle browser back/forward
        window.addEventListener('popstate', function() {
            window.location.reload();
        });

        // Performance optimizations for mobile
        if (window.innerWidth <= 768) {
            // Disable hover animations on mobile
            const style = document.createElement('style');
            style.textContent = `
                .log-item:hover { transform: none !important; }
                .animate-slide-up, .animate-fade-in { animation: none !important; }
            `;
            document.head.appendChild(style);
        }
    </script>
</x-app-layout>