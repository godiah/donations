<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WithdrawalRequest;
use App\Services\WalletService;
use App\Services\WithdrawalProcessingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AdminWithdrawalController extends Controller
{
    protected $walletService;
    protected $withdrawalProcessingService;

    public function __construct(
        WalletService $walletService,
        WithdrawalProcessingService $withdrawalProcessingService
    ) {
        $this->walletService = $walletService;
        $this->withdrawalProcessingService = $withdrawalProcessingService;
    }

    /**
     * List all withdrawal requests
     */
    public function index(Request $request)
    {
        $query = WithdrawalRequest::with(['user', 'wallet'])
            ->orderBy('created_at', 'desc');

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $withdrawals = $query->paginate(20);

        return view('admin.withdrawals.index', compact('withdrawals'));
    }

    /**
     * Show withdrawal details
     */
    public function show(WithdrawalRequest $withdrawal)
    {
        $withdrawal->load(['user', 'wallet', 'wallet.transactions']);

        return view('admin.withdrawals.show', compact('withdrawal'));
    }

    /**
     * Approve withdrawal request
     */
    public function approve(WithdrawalRequest $withdrawal)
    {
        try {
            if (!$withdrawal->isPending()) {
                return redirect()->back()
                    ->with('error', 'Only pending withdrawals can be approved');
            }

            $withdrawal->update([
                'status' => WithdrawalRequest::STATUS_APPROVED,
                'approved_at' => now(),
            ]);

            Log::info('Withdrawal approved by admin', [
                'withdrawal_id' => $withdrawal->id,
                'admin_id' => Auth::id(),
            ]);

            // Automatically start processing the payment
            $this->withdrawalProcessingService->initiatePaymentProcessing($withdrawal);


            return redirect()->back()
                ->with('success', 'Withdrawal approved successfully');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to approve withdrawal: ' . $e->getMessage());
        }
    }

    /**
     * Reject withdrawal request
     */
    public function reject(Request $request, WithdrawalRequest $withdrawal)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        try {
            if (!$withdrawal->isPending()) {
                return redirect()->back()
                    ->with('error', 'Only pending withdrawals can be rejected');
            }

            $this->walletService->cancelWithdrawal($withdrawal);

            $withdrawal->update([
                'status' => WithdrawalRequest::STATUS_CANCELLED,
                'rejection_reason' => $request->rejection_reason,
            ]);

            return redirect()->back()
                ->with('success', 'Withdrawal rejected successfully');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to reject withdrawal: ' . $e->getMessage());
        }
    }

    /**
     * Manual process withdrawal (if auto-processing fails)
     */
    public function process(WithdrawalRequest $withdrawal)
    {
        try {
            if ($withdrawal->status !== WithdrawalRequest::STATUS_APPROVED) {
                return redirect()->back()
                    ->with('error', 'Only approved withdrawals can be processed');
            }

            // Start processing
            $this->withdrawalProcessingService->initiatePaymentProcessing($withdrawal);

            return redirect()->back()
                ->with('success', 'Withdrawal processing initiated successfully');
        } catch (\Exception $e) {
            Log::error('Manual withdrawal processing failed', [
                'withdrawal_id' => $withdrawal->id,
                'admin_id' => Auth::id(),
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()
                ->with('error', 'Failed to process withdrawal: ' . $e->getMessage());
        }
    }

    /**
     * Mark withdrawal as completed manually (for reconciliation)
     */
    public function markCompleted(WithdrawalRequest $withdrawal, Request $request)
    {
        $request->validate([
            'gateway_reference' => 'required|string|max:255',
        ]);

        try {
            if (!in_array($withdrawal->status, [WithdrawalRequest::STATUS_PROCESSING, WithdrawalRequest::STATUS_APPROVED])) {
                return redirect()->back()
                    ->with('error', 'Only processing or approved withdrawals can be marked as completed');
            }

            $this->walletService->completeWithdrawal($withdrawal, $request->gateway_reference);

            Log::info('Withdrawal manually marked as completed', [
                'withdrawal_id' => $withdrawal->id,
                'admin_id' => Auth::id(),
                'gateway_reference' => $request->gateway_reference,
            ]);

            return redirect()->back()
                ->with('success', 'Withdrawal marked as completed successfully');
        } catch (\Exception $e) {
            Log::error('Manual withdrawal completion failed', [
                'withdrawal_id' => $withdrawal->id,
                'admin_id' => Auth::id(),
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()
                ->with('error', 'Failed to complete withdrawal: ' . $e->getMessage());
        }
    }
}
