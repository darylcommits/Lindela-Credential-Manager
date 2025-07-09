<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\OtpRequest;
use App\Services\OtpService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class OtpManagementController extends Controller
{
    protected $otpService;

    public function __construct(OtpService $otpService)
    {
        $this->middleware(['auth', 'user.active', 'admin', 'track.activity']);
        $this->otpService = $otpService;
    }

    public function index(Request $request)
    {
        $status = $request->input('status', 'pending');
        
        $otpRequests = OtpRequest::with(['user', 'approver'])
            ->when($status === 'pending', function ($query) {
                return $query->pending()->where('expires_at', '>', now());
            })
            ->when($status !== 'pending', function ($query) use ($status) {
                return $query->where('status', $status);
            })
            ->orderBy('created_at', $status === 'pending' ? 'asc' : 'desc')
            ->paginate(15);

        $pendingCount = OtpRequest::pending()->where('expires_at', '>', now())->count();

        return view('admin.otp.index', compact('otpRequests', 'status', 'pendingCount'));
    }

    public function show(OtpRequest $otpRequest)
    {
        $otpRequest->load(['user', 'approver', 'credentialAccessLogs.credential']);
        
        return view('admin.otp.show', compact('otpRequest'));
    }

    public function approve(Request $request, OtpRequest $otpRequest)
    {
        $validator = Validator::make($request->all(), [
            'admin_notes' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        if (!$user) {
            return back()->with('error', 'User not authenticated.');
        }

        try {
            $this->otpService->approveOtp(
                $otpRequest, 
                $user, 
                $request->admin_notes
            );

            return back()->with('success', 'OTP request approved successfully.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function deny(Request $request, OtpRequest $otpRequest)
    {
        $validator = Validator::make($request->all(), [
            'admin_notes' => 'required|string|max:500',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        if (!$user) {
            return back()->with('error', 'User not authenticated.');
        }

        try {
            $this->otpService->denyOtp(
                $otpRequest, 
                $user, 
                $request->admin_notes
            );

            return back()->with('success', 'OTP request denied.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function bulkApprove(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'otp_request_ids' => 'required|array',
            'otp_request_ids.*' => 'exists:otp_requests,id',
            'admin_notes' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        if (!$user) {
            return back()->with('error', 'User not authenticated.');
        }

        $approved = 0;
        $errors = [];

        foreach ($request->otp_request_ids as $id) {
            try {
                $otpRequest = OtpRequest::findOrFail($id);
                $this->otpService->approveOtp(
                    $otpRequest, 
                    $user, 
                    $request->admin_notes
                );
                $approved++;
            } catch (\Exception $e) {
                $errors[] = "Request ID {$id}: " . $e->getMessage();
            }
        }

        $message = "Approved {$approved} OTP requests.";
        if (!empty($errors)) {
            $message .= " Errors: " . implode(', ', $errors);
        }

        return back()->with($approved > 0 ? 'success' : 'error', $message);
    }
}