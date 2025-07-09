<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Credential;
use App\Models\CredentialAccessLog;
use App\Models\User;
use App\Models\SystemLog;
use App\Services\CredentialService;
use Carbon\Carbon;

class ReportsController extends Controller
{
    protected $credentialService;

    public function __construct(CredentialService $credentialService)
    {
        $this->middleware(['auth', 'user.active', 'admin', 'track.activity']);
        $this->credentialService = $credentialService;
    }

    public function index()
    {
        return view('admin.reports.index');
    }

    public function security(Request $request)
    {
        $period = $request->input('period', '30'); // days
        $startDate = now()->subDays($period);

        $data = [
            'expiringCredentials' => $this->credentialService->getExpiringCredentials($period),
            'expiredCredentials' => $this->credentialService->getExpiredCredentials(),
            'weakPasswords' => Credential::where('password_strength', '<=', 2)->active()->get(),
            'unusedCredentials' => Credential::whereNull('last_accessed_at')
                ->orWhere('last_accessed_at', '<', now()->subDays(90))
                ->active()
                ->get(),
            'failedLogins' => SystemLog::where('action', 'failed_login')
                ->where('created_at', '>=', $startDate)
                ->count(),
            'unauthorizedAccess' => SystemLog::where('level', 'warning')
                ->where('created_at', '>=', $startDate)
                ->count(),
        ];

        return view('admin.reports.security', compact('data', 'period'));
    }

    public function usage(Request $request)
    {
        $period = $request->input('period', '30'); // days
        $startDate = now()->subDays($period);

        $data = [
            'totalAccess' => CredentialAccessLog::where('created_at', '>=', $startDate)->count(),
            'userActivity' => User::withCount(['credentialAccessLogs' => function ($query) use ($startDate) {
                    $query->where('created_at', '>=', $startDate);
                }])
                ->orderBy('credential_access_logs_count', 'desc')
                ->get(),
            'topCredentials' => Credential::withCount(['accessLogs' => function ($query) use ($startDate) {
                    $query->where('created_at', '>=', $startDate);
                }])
                ->having('access_logs_count', '>', 0)
                ->orderBy('access_logs_count', 'desc')
                ->limit(10)
                ->get(),
            'dailyActivity' => $this->getDailyActivity($startDate),
        ];

        return view('admin.reports.usage', compact('data', 'period'));
    }

    private function getDailyActivity($startDate)
    {
        $dailyStats = [];
        
        for ($date = $startDate->copy(); $date <= now(); $date->addDay()) {
            $dayStart = $date->copy()->startOfDay();
            $dayEnd = $date->copy()->endOfDay();
            
            $dailyStats[] = [
                'date' => $date->format('Y-m-d'),
                'access_count' => CredentialAccessLog::whereBetween('created_at', [$dayStart, $dayEnd])->count(),
                'unique_users' => CredentialAccessLog::whereBetween('created_at', [$dayStart, $dayEnd])
                    ->distinct('user_id')->count('user_id'),
            ];
        }

        return collect($dailyStats);
    }
}
