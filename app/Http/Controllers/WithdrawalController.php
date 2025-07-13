<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\WithdrawalRequest as RequestsWithdrawalRequest;
use App\Models\WithdrawalRequest;
use App\Services\WalletService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class WithdrawalController extends Controller
{
    use AuthorizesRequests;

    protected $walletService;

    public function __construct(WalletService $walletService)
    {
        $this->walletService = $walletService;
    }

    /**
     * Display wallet dashboard
     */
    public function dashboard()
    {
        $user = Auth::user();
        $wallet = $user->wallet()->with(['transactions' => function ($query) {
            $query->orderBy('created_at', 'desc')->limit(10);
        }])->first();

        $balance = $this->walletService->getBalance($user);
        $availableBalance = $this->walletService->getAvailableBalance($user);

        $activeWithdrawals = $user->withdrawalRequests()
            ->whereIn('status', ['pending', 'approved'])
            ->orderBy('created_at', 'desc')
            ->get();

        $recentTransactions = $this->walletService->getTransactionHistory($user, 10);

        return view('wallet.dashboard', compact(
            'wallet',
            'balance',
            'availableBalance',
            'activeWithdrawals',
            'recentTransactions'
        ));
    }

    /**
     * Show withdrawal form
     */
    public function create()
    {
        $user = Auth::user();
        $availableBalance = $this->walletService->getAvailableBalance($user);

        if ($availableBalance <= 0) {
            return redirect()->route('wallet.dashboard')
                ->with('error', 'No funds available for withdrawal');
        }

        // Check if user has any payout method
        if (!$this->walletService->userHasPayoutMethod($user)) {
            return redirect()->route('payout-methods.create')
                ->with('error', 'Please set up a payout method to proceed with withdrawal');
        }

        // Get user's primary or latest payout method
        $payoutMethod = $this->walletService->getUserPayoutMethod($user);

        if (!$payoutMethod) {
            return redirect()->route('payout-methods.create')
                ->with('error', 'Please set up a payout method to proceed with withdrawal');
        }

        // Get fee preview for display (using minimum withdrawal amount for preview)
        $previewAmount = 100; // Default preview amount
        $feePreview = $this->walletService->getWithdrawalFeePreview($previewAmount, $payoutMethod);

        return view('wallet.withdraw', compact(
            'availableBalance',
            'payoutMethod',
            'feePreview'
        ));
    }

    /**
     * Process withdrawal request
     */
    public function store(RequestsWithdrawalRequest $request)
    {
        try {
            $user = Auth::user();

            // Get the payout method to use for withdrawal
            $payoutMethod = $this->walletService->getUserPayoutMethod($user);

            if (!$payoutMethod) {
                return redirect()->route('payout-methods.create')
                    ->with('error', 'Please set up a payout method to proceed with withdrawal');
            }

            $withdrawalRequest = $this->walletService->processWithdrawalRequest(
                $user,
                $request->amount,
                $payoutMethod
            );

            Log::info('Withdrawal request submitted', [
                'user_id' => $user->id,
                'withdrawal_id' => $withdrawalRequest->id,
                'amount' => $request->amount,
                'payout_method_id' => $payoutMethod->id,
                'method' => $withdrawalRequest->withdrawal_method,
            ]);

            return redirect()->route('wallet.dashboard')
                ->with('success', 'Withdrawal request submitted successfully. Reference: ' . $withdrawalRequest->request_reference);
        } catch (\Exception $e) {
            Log::error('Withdrawal request failed', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'request_data' => $request->validated(),
            ]);

            return redirect()->back()
                ->withErrors(['error' => $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Show withdrawal details
     */
    public function show(WithdrawalRequest $withdrawal)
    {
        $this->authorize('view', $withdrawal);

        return view('wallet.withdrawal-details', compact('withdrawal'));
    }

    /**
     * Cancel withdrawal request
     */
    public function cancel(WithdrawalRequest $withdrawal)
    {
        $this->authorize('cancel', $withdrawal);

        try {
            $this->walletService->cancelWithdrawal($withdrawal);

            return redirect()->route('wallet.dashboard')
                ->with('success', 'Withdrawal request cancelled successfully');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Get wallet transaction history
     */
    public function transactionHistory(Request $request)
    {
        $user = Auth::user();
        $page = $request->get('page', 1);
        $limit = 25; // Load 25 transactions per page
        $offset = ($page - 1) * $limit;

        // Get total count of transactions
        $totalTransactions = $this->walletService->getTransactionCount($user);

        // Get transactions for current page
        $transactions = $this->walletService->getTransactionHistory($user, $limit, 'KES', $offset);

        // Calculate pagination info
        $hasMore = ($offset + count($transactions)) < $totalTransactions;
        $currentCount = $offset + count($transactions);

        // If it's an AJAX request (load more), return JSON
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'transactions' => $transactions,
                'hasMore' => $hasMore,
                'currentCount' => $currentCount,
                'totalCount' => $totalTransactions
            ]);
        }

        // Return view for initial page load
        return view('wallet.transactions', compact('transactions', 'hasMore', 'currentCount', 'totalTransactions'));
    }

    /**
     * Get withdrawal fee preview via AJAX
     */
    public function getFeePreview(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1'
        ]);

        try {
            $user = Auth::user();
            $payoutMethod = $this->walletService->getUserPayoutMethod($user);

            if (!$payoutMethod) {
                return response()->json([
                    'success' => false,
                    'message' => 'No payout method found'
                ], 400);
            }

            $feePreview = $this->walletService->getWithdrawalFeePreview(
                $request->amount,
                $payoutMethod
            );

            return response()->json([
                'success' => true,
                'data' => $feePreview
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
