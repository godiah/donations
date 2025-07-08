<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WithdrawalRequest;
use App\Services\WalletService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AdminWithdrawalController extends Controller
{
    protected $walletService;

    public function __construct(WalletService $walletService)
    {
        $this->walletService = $walletService;
        //$this->middleware(['auth', 'admin']);
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
                'admin_id' => auth()->id(),
            ]);

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
}
