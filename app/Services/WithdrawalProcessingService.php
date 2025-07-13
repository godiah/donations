<?php

namespace App\Services;

use App\Models\WithdrawalRequest;
use App\Services\WalletService;
use App\Services\MpesaService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Exception;

class WithdrawalProcessingService
{
    protected $walletService;
    protected $mpesaService;
    protected $bankTransferService;
    protected $paybillService;

    public function __construct(
        WalletService $walletService,
        MpesaService $mpesaService,
        BankTransferService $bankTransferService,
        PaybillService $paybillService
    ) {
        $this->walletService = $walletService;
        $this->mpesaService = $mpesaService;
        $this->bankTransferService = $bankTransferService;
        $this->paybillService = $paybillService;
    }

    /**
     * Initiate payment processing based on withdrawal method
     */
    public function initiatePaymentProcessing(WithdrawalRequest $withdrawal)
    {
        return DB::transaction(function () use ($withdrawal) {
            try {
                // Mark as processing before attempting transfer
                $withdrawal->update([
                    'status' => WithdrawalRequest::STATUS_PROCESSING,
                ]);

                Log::info('Starting payment processing', [
                    'withdrawal_id' => $withdrawal->id,
                    'method' => $withdrawal->withdrawal_method,
                    'amount' => $withdrawal->net_amount,
                ]);

                // Route to appropriate payment service
                switch ($withdrawal->withdrawal_method) {
                    case 'mpesa':
                        $this->processMpesaPayment($withdrawal);
                        break;

                    case 'bank_transfer':
                        $this->processBankTransfer($withdrawal);
                        break;

                    case 'paybill':
                        $this->processPaybillPayment($withdrawal);
                        break;

                    default:
                        throw new Exception('Unsupported withdrawal method: ' . $withdrawal->withdrawal_method);
                }

                Log::info('Payment processing initiated successfully', [
                    'withdrawal_id' => $withdrawal->id,
                    'method' => $withdrawal->withdrawal_method,
                ]);
            } catch (\Exception $e) {
                // Mark as failed if processing initiation fails
                $withdrawal->update([
                    'status' => WithdrawalRequest::STATUS_FAILED,
                    'rejection_reason' => 'Processing failed: ' . $e->getMessage(),
                ]);

                // Restore user's balance
                $this->walletService->cancelWithdrawal($withdrawal);

                Log::error('Payment processing failed', [
                    'withdrawal_id' => $withdrawal->id,
                    'method' => $withdrawal->withdrawal_method,
                    'error' => $e->getMessage(),
                ]);

                throw $e;
            }
        });
    }

    /**
     * Process M-Pesa payment
     */
    protected function processMpesaPayment(WithdrawalRequest $withdrawal)
    {
        $details = $withdrawal->withdrawal_details;
        $phoneNumber = $details['phone_number'] ?? null;

        if (!$phoneNumber) {
            throw new Exception('Phone number not found in withdrawal details');
        }

        // Call M-Pesa API
        $response = $this->mpesaService->initiateB2CPayment([
            'amount' => $withdrawal->net_amount,
            'phone_number' => $phoneNumber,
            'withdrawal_id' => $withdrawal->id,
            'callback_url' => route('api.mpesa.callback'),
            'description' => "Withdrawal #{$withdrawal->request_reference}",
        ]);

        if ($response['success']) {
            // Update with gateway reference for tracking
            $withdrawal->update([
                'gateway_reference' => $response['transaction_id'],
            ]);

            Log::info('M-Pesa payment initiated', [
                'withdrawal_id' => $withdrawal->id,
                'transaction_id' => $response['transaction_id'],
                'phone_number' => $phoneNumber,
                'amount' => $withdrawal->net_amount,
            ]);
        } else {
            throw new Exception('M-Pesa payment initiation failed: ' . ($response['error'] ?? 'Unknown error'));
        }
    }

    /**
     * Process bank transfer
     */
    protected function processBankTransfer(WithdrawalRequest $withdrawal)
    {
        $details = $withdrawal->withdrawal_details;

        // Validate required fields
        $requiredFields = ['bank_name', 'account_number', 'account_name'];
        foreach ($requiredFields as $field) {
            if (empty($details[$field])) {
                throw new Exception("Missing required field: {$field}");
            }
        }

        // Call bank transfer API
        $response = $this->bankTransferService->initiateTransfer([
            'amount' => $withdrawal->net_amount,
            'bank_name' => $details['bank_name'],
            'bank_code' => $details['bank_code'] ?? null,
            'account_number' => $details['account_number'],
            'account_name' => $details['account_name'],
            'withdrawal_id' => $withdrawal->id,
            'callback_url' => route('api.bank.callback'),
            'description' => "Withdrawal #{$withdrawal->request_reference}",
        ]);

        if ($response['success']) {
            $withdrawal->update([
                'gateway_reference' => $response['reference'],
            ]);

            Log::info('Bank transfer initiated', [
                'withdrawal_id' => $withdrawal->id,
                'reference' => $response['reference'],
                'bank_name' => $details['bank_name'],
                'account_number' => $details['account_number'],
                'amount' => $withdrawal->net_amount,
            ]);
        } else {
            throw new Exception('Bank transfer initiation failed: ' . ($response['error'] ?? 'Unknown error'));
        }
    }

    /**
     * Process paybill payment
     */
    protected function processPaybillPayment(WithdrawalRequest $withdrawal)
    {
        $details = $withdrawal->withdrawal_details;

        // Validate required fields
        $requiredFields = ['paybill_number', 'account_number'];
        foreach ($requiredFields as $field) {
            if (empty($details[$field])) {
                throw new Exception("Missing required field: {$field}");
            }
        }

        // Call paybill API
        $response = $this->paybillService->initiatePayment([
            'amount' => $withdrawal->net_amount,
            'paybill_number' => $details['paybill_number'],
            'account_number' => $details['account_number'],
            'account_name' => $details['account_name'] ?? null,
            'withdrawal_id' => $withdrawal->id,
            'callback_url' => route('api.paybill.callback'),
            'description' => "Withdrawal #{$withdrawal->request_reference}",
        ]);

        if ($response['success']) {
            $withdrawal->update([
                'gateway_reference' => $response['transaction_id'],
            ]);

            Log::info('Paybill payment initiated', [
                'withdrawal_id' => $withdrawal->id,
                'transaction_id' => $response['transaction_id'],
                'paybill_number' => $details['paybill_number'],
                'account_number' => $details['account_number'],
                'amount' => $withdrawal->net_amount,
            ]);
        } else {
            throw new Exception('Paybill payment initiation failed: ' . ($response['error'] ?? 'Unknown error'));
        }
    }

    /**
     * Retry failed payment processing
     */
    public function retryPayment(WithdrawalRequest $withdrawal)
    {
        if ($withdrawal->status !== WithdrawalRequest::STATUS_FAILED) {
            throw new Exception('Only failed withdrawals can be retried');
        }

        // Reset status to approved and retry processing
        $withdrawal->update([
            'status' => WithdrawalRequest::STATUS_APPROVED,
            'rejection_reason' => null,
            'gateway_reference' => null,
        ]);

        return $this->initiatePaymentProcessing($withdrawal);
    }
}
