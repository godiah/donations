<?php

namespace App\Services;

use App\Models\Contribution;
use App\Models\PayoutMethod;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Models\WithdrawalRequest;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class WalletService
{
    /**
     * Create wallet for user
     */
    public function createWallet(User $user, string $currency = 'KES'): Wallet
    {
        return DB::transaction(function () use ($user, $currency) {
            // Check if wallet already exists
            $existingWallet = $user->wallet()->forCurrency($currency)->first();

            if ($existingWallet) {
                return $existingWallet;
            }

            return $user->wallet()->create([
                'currency' => $currency,
                'balance' => 0,
                'status' => Wallet::STATUS_ACTIVE,
                'total_received' => 0,
                'total_withdrawn' => 0,
                'pending_withdrawals' => 0,
                'last_activity_at' => now(),
            ]);
        });
    }

    /**
     * Credit wallet from donation
     */
    public function creditFromDonation(Contribution $contribution): WalletTransaction
    {
        return DB::transaction(function () use ($contribution) {
            // CRITICAL: Check if wallet has already been credited (double-credit protection)
            if ($contribution->wallet_credited) {
                Log::info('Wallet already credited for contribution - preventing double credit', [
                    'contribution_id' => $contribution->id,
                    'wallet_credited_at' => $contribution->wallet_credited_at,
                ]);

                // Return existing wallet transaction if it exists
                if ($contribution->wallet_transaction_id) {
                    return WalletTransaction::find($contribution->wallet_transaction_id);
                }

                throw new Exception('Contribution already credited but wallet transaction not found');
            }

            // Calculate platform fee if not already calculated
            if (!$contribution->hasPlatformFeeCalculated()) {
                $contribution->refresh();
                $contribution->calculatePlatformFee();
                $contribution->save();

                Log::info('Platform fee calculated for contribution', [
                    'contribution_id' => $contribution->id,
                    'amount' => $contribution->amount,
                    'platform_fee' => $contribution->platform_fee,
                    'net_amount' => $contribution->net_amount,
                ]);
            }

            // Get the applicant (Individual or Company)
            $applicant = $contribution->donationLink->application->applicant ?? null;

            if (!$applicant) {
                throw new Exception('Applicant not found for donation');
            }

            // Get the User who owns the applicant
            $user = $applicant->user ?? null;

            if (!$user) {
                throw new Exception('User not found for applicant');
            }

            // Get or create wallet for the user
            $wallet = $this->getOrCreateWallet($user, $contribution->currency);

            // Create transaction reference
            $transactionRef = $this->generateTransactionReference('CR');

            // Use net amount for wallet credit (amount - platform fee)
            $creditAmount = $contribution->getNetAmount();

            // Calculate running balance
            $runningBalance = $wallet->balance + $creditAmount;

            // Create wallet transaction
            $walletTransaction = $wallet->transactions()->create([
                'transaction_reference' => $transactionRef,
                'type' => WalletTransaction::TYPE_CREDIT,
                'amount' => $creditAmount,
                'running_balance' => $runningBalance,
                'status' => WalletTransaction::STATUS_COMPLETED,
                'source_type' => WalletTransaction::SOURCE_TYPE_DONATION,
                'source_id' => $contribution->id,
                'gateway' => $contribution->payment_method === 'card' ?
                    WalletTransaction::GATEWAY_CYBERSOURCE :
                    WalletTransaction::GATEWAY_MPESA,
                'gateway_reference' => $contribution->cybersource_transaction_id ?? null,
                'description' => "Donation credit - Contribution #{$contribution->id}",
                'metadata' => [
                    'contribution_id' => $contribution->id,
                    'donation_link_code' => $contribution->donationLink->code,
                    'donor_email' => $contribution->email,
                    'donor_phone' => $contribution->phone,
                    'gross_amount' => $contribution->amount,
                    'platform_fee' => $contribution->platform_fee,
                    'net_amount' => $contribution->net_amount,
                    'platform_fee_percentage' => $contribution->platform_fee_percentage,
                ],
                'fee_amount' => $contribution->platform_fee,
                'processed_at' => now(),
            ]);

            // Update wallet balance and totals
            $wallet->update([
                'balance' => $runningBalance,
                'total_received' => $wallet->total_received + $creditAmount,
                'last_activity_at' => now(),
            ]);

            // Link contribution to wallet transaction
            $contribution->update([
                'wallet_transaction_id' => $walletTransaction->id,
                'wallet_credited' => true,
                'wallet_credited_at' => now(),
            ]);

            Log::info('Wallet credited from donation with platform fee', [
                'wallet_id' => $wallet->id,
                'contribution_id' => $contribution->id,
                'gross_amount' => $contribution->amount,
                'platform_fee' => $contribution->platform_fee,
                'net_amount' => $creditAmount,
                'new_balance' => $runningBalance,
                'transaction_ref' => $transactionRef,
            ]);

            return $walletTransaction;
        });
    }

    /**
     * Process withdrawal request
     */
    public function processWithdrawalRequest(
        User $user,
        float $amount,
        PayoutMethod $payoutMethod
    ): WithdrawalRequest {
        return DB::transaction(function () use ($user, $amount, $payoutMethod) {
            // Get user wallet
            $wallet = $user->wallet()->active()->first();

            if (!$wallet) {
                throw new Exception('No active wallet found for user');
            }

            // Validate withdrawal amount
            if ($amount <= 0) {
                throw new Exception('Withdrawal amount must be greater than zero');
            }

            // Validate payout method belongs to user
            if ($payoutMethod->user_id !== $user->id) {
                throw new Exception('Invalid payout method');
            }

            // Get withdrawal method from payout method
            $withdrawalMethod = WithdrawalRequest::getWithdrawalMethodFromPayoutType($payoutMethod->type);

            // Calculate fees based on withdrawal method
            $feeAmount = $this->calculateWithdrawalFee($amount, $withdrawalMethod);
            $netAmount = $amount - $feeAmount;

            // Check if wallet has sufficient balance
            if (!$wallet->hasSufficientBalance($amount)) {
                throw new Exception('Insufficient wallet balance');
            }

            // Check available balance (considering pending withdrawals)
            if ($wallet->available_balance < $amount) {
                throw new Exception('Insufficient available balance. You have pending withdrawals');
            }

            // Generate request reference
            $requestRef = $this->generateWithdrawalReference();

            // Create withdrawal details from payout method
            $withdrawalDetails = WithdrawalRequest::createWithdrawalDetailsFromPayoutMethod($payoutMethod);

            // Create withdrawal request
            $withdrawalRequest = $wallet->withdrawalRequests()->create([
                'user_id' => $user->id,
                'payout_method_id' => $payoutMethod->id,
                'request_reference' => $requestRef,
                'amount' => $amount,
                'fee_amount' => $feeAmount,
                'net_amount' => $netAmount,
                'withdrawal_method' => $withdrawalMethod,
                'withdrawal_details' => $withdrawalDetails,
                'status' => WithdrawalRequest::STATUS_PENDING,
            ]);

            // Update wallet pending withdrawals
            $wallet->update([
                'pending_withdrawals' => $wallet->pending_withdrawals + $amount,
                'last_activity_at' => now(),
            ]);

            Log::info('Withdrawal request created', [
                'withdrawal_id' => $withdrawalRequest->id,
                'user_id' => $user->id,
                'amount' => $amount,
                'method' => $withdrawalMethod,
                'payout_method_id' => $payoutMethod->id,
                'reference' => $requestRef,
            ]);

            return $withdrawalRequest;
        });
    }

    /**
     * Complete withdrawal (called after successful payout)
     */
    public function completeWithdrawal(WithdrawalRequest $withdrawalRequest, string $gatewayReference = null): WalletTransaction
    {
        return DB::transaction(function () use ($withdrawalRequest, $gatewayReference) {
            $wallet = $withdrawalRequest->wallet;

            // Create debit transaction
            $transactionRef = $this->generateTransactionReference('DR');
            $runningBalance = $wallet->balance - $withdrawalRequest->amount;

            $walletTransaction = $wallet->transactions()->create([
                'transaction_reference' => $transactionRef,
                'type' => WalletTransaction::TYPE_DEBIT,
                'amount' => $withdrawalRequest->amount,
                'running_balance' => $runningBalance,
                'status' => WalletTransaction::STATUS_COMPLETED,
                'source_type' => WalletTransaction::SOURCE_TYPE_WITHDRAWAL,
                'source_id' => $withdrawalRequest->id,
                'gateway' => $withdrawalRequest->withdrawal_method === 'mpesa' ?
                    WalletTransaction::GATEWAY_MPESA :
                    WalletTransaction::GATEWAY_BANK_TRANSFER,
                'gateway_reference' => $gatewayReference,
                'description' => "Withdrawal - Request #{$withdrawalRequest->request_reference}",
                'metadata' => [
                    'withdrawal_request_id' => $withdrawalRequest->id,
                    'withdrawal_method' => $withdrawalRequest->withdrawal_method,
                    'withdrawal_details' => $withdrawalRequest->withdrawal_details,
                ],
                'fee_amount' => $withdrawalRequest->fee_amount,
                'processed_at' => now(),
            ]);

            // Update wallet balances
            $wallet->update([
                'balance' => $runningBalance,
                'total_withdrawn' => $wallet->total_withdrawn + $withdrawalRequest->amount,
                'pending_withdrawals' => $wallet->pending_withdrawals - $withdrawalRequest->amount,
                'last_activity_at' => now(),
            ]);

            // Update withdrawal request status
            $withdrawalRequest->update([
                'status' => WithdrawalRequest::STATUS_COMPLETED,
                'gateway_reference' => $gatewayReference,
                'processed_at' => now(),
            ]);

            Log::info('Withdrawal completed', [
                'withdrawal_id' => $withdrawalRequest->id,
                'transaction_id' => $walletTransaction->id,
                'amount' => $withdrawalRequest->amount,
                'new_balance' => $runningBalance,
            ]);

            return $walletTransaction;
        });
    }

    /**
     * Cancel withdrawal request
     */
    public function cancelWithdrawal(WithdrawalRequest $withdrawalRequest): void
    {
        DB::transaction(function () use ($withdrawalRequest) {
            if (!$withdrawalRequest->canBeCancelled()) {
                throw new Exception('Withdrawal request cannot be cancelled');
            }

            $wallet = $withdrawalRequest->wallet;

            // Update wallet pending withdrawals
            $wallet->update([
                'pending_withdrawals' => $wallet->pending_withdrawals - $withdrawalRequest->amount,
                'last_activity_at' => now(),
            ]);

            // Update withdrawal request status
            $withdrawalRequest->update([
                'status' => WithdrawalRequest::STATUS_CANCELLED,
            ]);

            Log::info('Withdrawal cancelled', [
                'withdrawal_id' => $withdrawalRequest->id,
                'amount' => $withdrawalRequest->amount,
            ]);
        });
    }

    /**
     * Get wallet balance
     */
    public function getBalance(User $user, string $currency = 'KES'): float
    {
        $wallet = $user->wallet()->forCurrency($currency)->first();
        return $wallet ? $wallet->balance : 0;
    }

    /**
     * Get available balance (balance - pending withdrawals)
     */
    public function getAvailableBalance(User $user, string $currency = 'KES'): float
    {
        $wallet = $user->wallet()->forCurrency($currency)->first();
        return $wallet ? $wallet->available_balance : 0;
    }

    /**
     * Get or create wallet for user
     */
    protected function getOrCreateWallet(User $user, string $currency): Wallet
    {
        $wallet = $user->wallet()->forCurrency($currency)->first();

        if (!$wallet) {
            $wallet = $this->createWallet($user, $currency);
        }

        return $wallet;
    }

    /**
     * Generate transaction reference
     */
    protected function generateTransactionReference(string $prefix = 'TXN'): string
    {
        return $prefix . '-' . date('YmdHis') . '-' . strtoupper(Str::random(6));
    }

    /**
     * Generate withdrawal reference
     */
    protected function generateWithdrawalReference(): string
    {
        return 'WDR-' . date('YmdHis') . '-' . strtoupper(Str::random(6));
    }

    /**
     * Calculate withdrawal fee based on method
     */
    protected function calculateWithdrawalFee(float $amount, string $method): float
    {
        // Define fee structure
        $feeStructure = [
            'mpesa' => [
                'percentage' => 0.02, // 2%
                'minimum' => 10,      // KES 10
                'maximum' => 100,     // KES 100
            ],
            'bank_transfer' => [
                'percentage' => 0.015, // 1.5%
                'minimum' => 50,       // KES 50
                'maximum' => 500,      // KES 500
            ],
            'paybill' => [
                'percentage' => 0.025, // 2.5%
                'minimum' => 15,       // KES 15
                'maximum' => 150,      // KES 150
            ],
        ];

        if (!isset($feeStructure[$method])) {
            return 0;
        }

        $config = $feeStructure[$method];
        $percentageFee = $amount * $config['percentage'];

        // Apply minimum and maximum constraints
        $fee = max($config['minimum'], min($percentageFee, $config['maximum']));

        return round($fee, 2);
    }

    /**
     * Get wallet transaction history
     */
    public function getTransactionHistory(User $user, int $limit = 50, string $currency = 'KES'): array
    {
        $wallet = $user->wallet()->forCurrency($currency)->first();

        if (!$wallet) {
            return [];
        }

        $transactions = $wallet->transactions()
            ->with(['wallet.user'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();

        return $transactions->map(function ($transaction) {
            return [
                'id' => $transaction->id,
                'reference' => $transaction->transaction_reference,
                'type' => $transaction->type,
                'amount' => $transaction->amount,
                'running_balance' => $transaction->running_balance,
                'status' => $transaction->status,
                'description' => $transaction->description,
                'gateway' => $transaction->gateway,
                'fee_amount' => $transaction->fee_amount,
                'created_at' => $transaction->created_at,
                'processed_at' => $transaction->processed_at,
            ];
        })->toArray();
    }

    public function getTransactionCount(User $user, string $currency = 'KES'): int
    {
        $wallet = $user->wallet()->forCurrency($currency)->first();

        if (!$wallet) {
            return 0;
        }

        return $wallet->transactions()->count();
    }
    /**
     * Validate wallet transaction integrity
     */
    public function validateWalletIntegrity(Wallet $wallet): array
    {
        $issues = [];

        // Get all completed transactions ordered by creation
        $transactions = $wallet->transactions()
            ->completed()
            ->orderBy('created_at')
            ->get();

        $calculatedBalance = 0;

        foreach ($transactions as $transaction) {
            if ($transaction->isCredit()) {
                $calculatedBalance += $transaction->amount;
            } else {
                $calculatedBalance -= $transaction->amount;
            }

            // Check if running balance matches calculated balance
            if (abs($transaction->running_balance - $calculatedBalance) > 0.01) {
                $issues[] = [
                    'type' => 'balance_mismatch',
                    'transaction_id' => $transaction->id,
                    'expected_balance' => $calculatedBalance,
                    'recorded_balance' => $transaction->running_balance,
                ];
            }
        }

        // Check if final wallet balance matches calculated balance
        if (abs($wallet->balance - $calculatedBalance) > 0.01) {
            $issues[] = [
                'type' => 'wallet_balance_mismatch',
                'expected_balance' => $calculatedBalance,
                'wallet_balance' => $wallet->balance,
            ];
        }

        return $issues;
    }

    /**
     * Reconcile wallet balances (admin function)
     */
    public function reconcileWallet(Wallet $wallet): void
    {
        DB::transaction(function () use ($wallet) {
            // Recalculate balance from all completed transactions
            $credits = $wallet->transactions()
                ->completed()
                ->credits()
                ->sum('amount');

            $debits = $wallet->transactions()
                ->completed()
                ->debits()
                ->sum('amount');

            $correctBalance = $credits - $debits;

            // Update wallet balance
            $wallet->update([
                'balance' => $correctBalance,
                'last_activity_at' => now(),
            ]);

            Log::info('Wallet reconciled', [
                'wallet_id' => $wallet->id,
                'old_balance' => $wallet->getOriginal('balance'),
                'new_balance' => $correctBalance,
                'credits_total' => $credits,
                'debits_total' => $debits,
            ]);
        });
    }

    /**
     * Check if user has any payout method
     */
    public function userHasPayoutMethod(User $user): bool
    {
        return $user->payoutMethods()->exists();
    }

    /**
     * Get user's primary or latest payout method
     */
    public function getUserPayoutMethod(User $user): ?PayoutMethod
    {
        // First, try to get primary payout method
        $primaryMethod = $user->primaryPayoutMethod;

        if ($primaryMethod) {
            return $primaryMethod;
        }

        // If no primary method, get the latest verified payout method
        $latestMethod = $user->verifiedPayoutMethods()
            ->latest()
            ->first();

        if ($latestMethod) {
            return $latestMethod;
        }

        // If no verified methods, get the latest payout method
        return $user->payoutMethods()
            ->latest()
            ->first();
    }

    /**
     * Get withdrawal fee preview
     */
    public function getWithdrawalFeePreview(float $amount, PayoutMethod $payoutMethod): array
    {
        $withdrawalMethod = WithdrawalRequest::getWithdrawalMethodFromPayoutType($payoutMethod->type);
        $feeAmount = $this->calculateWithdrawalFee($amount, $withdrawalMethod);
        $netAmount = $amount - $feeAmount;

        return [
            'gross_amount' => $amount,
            'fee_amount' => $feeAmount,
            'net_amount' => $netAmount,
            'withdrawal_method' => $withdrawalMethod,
            'fee_percentage' => $this->getFeePercentage($withdrawalMethod),
        ];
    }

    /**
     * Get fee percentage for display
     */
    private function getFeePercentage(string $method): float
    {
        $feeStructure = [
            'mpesa' => 2.0,
            'bank_transfer' => 1.5,
            'paybill' => 2.5,
        ];

        return $feeStructure[$method] ?? 0;
    }
}
