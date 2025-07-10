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

        $pendingWithdrawals = $user->withdrawalRequests()
            ->pending()
            ->orderBy('created_at', 'desc')
            ->get();

        $recentTransactions = $this->walletService->getTransactionHistory($user, 10);

        return view('wallet.dashboard', compact(
            'wallet',
            'balance',
            'availableBalance',
            'pendingWithdrawals',
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

        return view('wallet.withdraw', compact('availableBalance'));
    }

    /**
     * Process withdrawal request
     */
    public function store(RequestsWithdrawalRequest $request)
    {
        try {
            $user = Auth::user();

            $withdrawalRequest = $this->walletService->processWithdrawalRequest(
                $user,
                $request->amount,
                $request->withdrawal_method,
                $request->getWithdrawalDetails()
            );

            Log::info('Withdrawal request submitted', [
                'user_id' => $user->id,
                'withdrawal_id' => $withdrawalRequest->id,
                'amount' => $request->amount,
                'method' => $request->withdrawal_method,
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
     * Get wallet transaction history (AJAX)
     */
    public function transactionHistory(Request $request)
    {
        $user = Auth::user();
        $limit = $request->get('limit', 50);

        $transactions = $this->walletService->getTransactionHistory($user, $limit);

        return response()->json([
            'success' => true,
            'transactions' => $transactions,
        ]);
    }
}
