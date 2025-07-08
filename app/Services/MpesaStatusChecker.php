<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\Contribution;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class MpesaStatusChecker
{
    protected $mpesaService;
    protected $walletService;

    public function __construct(MpesaService $mpesaService, WalletService $walletService)
    {
        $this->mpesaService = $mpesaService;
        $this->walletService = $walletService;
    }

    /**
     * Check status of pending transactions
     */
    public function checkPendingTransactions()
    {
        // Get transactions that are still pending after 2 minutes
        $pendingTransactions = Transaction::where('status', Transaction::STATUS_PENDING)
            ->where('mpesa_payment_type', 'stk_push')
            ->where('created_at', '<', now()->subMinutes(2))
            ->where('created_at', '>', now()->subHours(3)) // Don't check very old transactions
            ->get();

        Log::info('Checking pending M-Pesa transactions', [
            'count' => $pendingTransactions->count()
        ]);

        foreach ($pendingTransactions as $transaction) {
            $this->checkTransactionStatus($transaction);

            // Add delay between checks to avoid rate limiting
            sleep(1);
        }
    }

    /**
     * Check individual transaction status
     */
    public function checkTransactionStatus(Transaction $transaction)
    {
        try {
            $result = $this->mpesaService->queryStkPushStatus($transaction->mpesa_checkout_request_id);

            Log::info('Transaction status check', [
                'transaction_id' => $transaction->id,
                'checkout_request_id' => $transaction->mpesa_checkout_request_id,
                'result' => $result
            ]);

            if ($result['success'] && isset($result['data'])) {
                $this->processStatusResponse($transaction, $result['data']);
            } else {
                Log::warning('Failed to check transaction status', [
                    'transaction_id' => $transaction->id,
                    'error' => $result['error'] ?? 'Unknown error'
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Transaction status check failed', [
                'transaction_id' => $transaction->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Process the status response
     */
    protected function processStatusResponse(Transaction $transaction, array $statusData)
    {
        $resultCode = $statusData['ResultCode'] ?? null;
        $resultDesc = $statusData['ResultDesc'] ?? '';

        if ($resultCode === '0') {
            // Transaction successful
            $this->processSuccessfulTransaction($transaction, $statusData);
        } elseif ($resultCode === '1032') {
            // Transaction cancelled
            $this->processCancelledTransaction($transaction, $statusData);
        } elseif ($resultCode === '1001') {
            // Transaction failed - insufficient funds
            $this->processFailedTransaction($transaction, $statusData, 'Insufficient funds');
        } elseif ($resultCode === '2001') {
            // Transaction failed - wrong pin
            $this->processFailedTransaction($transaction, $statusData, 'Wrong PIN');
        } elseif (in_array($resultCode, ['1', '1001', '1019', '1025', '1026', '1997', '2001'])) {
            // Various failure codes
            $this->processFailedTransaction($transaction, $statusData, $resultDesc);
        } else {
            // Transaction still pending or unknown status
            Log::info('Transaction still pending', [
                'transaction_id' => $transaction->id,
                'result_code' => $resultCode,
                'result_desc' => $resultDesc
            ]);

            // If transaction is older than 10 minutes, mark as failed
            if ($transaction->created_at < now()->subMinutes(10)) {
                $this->processFailedTransaction($transaction, $statusData, 'Transaction timeout');
            }
        }
    }

    /**
     * Process successful transaction
     */
    protected function processSuccessfulTransaction(Transaction $transaction, array $statusData)
    {
        try {
            // Extract payment details if available
            $paymentData = $this->extractPaymentData($statusData);

            $transaction->update([
                'status' => Transaction::STATUS_COMPLETED,
                'mpesa_receipt_number' => $paymentData['receipt_number'] ?? null,
                'mpesa_amount' => $paymentData['amount'] ?? $transaction->amount,
                'mpesa_transaction_date' => $paymentData['transaction_date'] ?? now(),
                'gateway_response' => array_merge($transaction->gateway_response ?? [], $statusData),
                'notes' => 'Transaction completed - Updated via status query',
                'processed_at' => now(),
            ]);

            $contribution = $transaction->contribution;
            $contribution->update([
                'payment_status' => Contribution::STATUS_COMPLETED,
                'processed_at' => now(),
            ]);

            // Credit wallet
            $this->walletService->creditFromDonation($contribution);

            Log::info('Transaction marked as successful via status check', [
                'transaction_id' => $transaction->id,
                'contribution_id' => $contribution->id,
                'amount' => $transaction->amount
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to process successful transaction', [
                'transaction_id' => $transaction->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Process cancelled transaction
     */
    protected function processCancelledTransaction(Transaction $transaction, array $statusData)
    {
        $transaction->update([
            'status' => Transaction::STATUS_CANCELLED,
            'gateway_response' => array_merge($transaction->gateway_response ?? [], $statusData),
            'notes' => 'Transaction cancelled by user',
            'processed_at' => now(),
        ]);

        $transaction->contribution->update([
            'payment_status' => Contribution::STATUS_CANCELLED,
            'processed_at' => now(),
        ]);

        Log::info('Transaction marked as cancelled via status check', [
            'transaction_id' => $transaction->id
        ]);
    }

    /**
     * Process failed transaction
     */
    protected function processFailedTransaction(Transaction $transaction, array $statusData, string $reason)
    {
        $transaction->update([
            'status' => Transaction::STATUS_FAILED,
            'gateway_response' => array_merge($transaction->gateway_response ?? [], $statusData),
            'notes' => $reason,
            'processed_at' => now(),
        ]);

        $transaction->contribution->update([
            'payment_status' => Contribution::STATUS_FAILED,
            'processed_at' => now(),
        ]);

        Log::info('Transaction marked as failed via status check', [
            'transaction_id' => $transaction->id,
            'reason' => $reason
        ]);
    }

    /**
     * Extract payment data from status response
     */
    protected function extractPaymentData(array $statusData): array
    {
        $paymentData = [
            'receipt_number' => null,
            'amount' => null,
            'transaction_date' => null,
            'phone_number' => null,
        ];

        // The status response might not always have detailed payment data
        // This is different from the callback which has CallbackMetadata
        if (isset($statusData['CallbackMetadata']['Item'])) {
            foreach ($statusData['CallbackMetadata']['Item'] as $item) {
                switch ($item['Name']) {
                    case 'MpesaReceiptNumber':
                        $paymentData['receipt_number'] = $item['Value'];
                        break;
                    case 'Amount':
                        $paymentData['amount'] = $item['Value'];
                        break;
                    case 'TransactionDate':
                        $paymentData['transaction_date'] = Carbon::createFromFormat('YmdHis', $item['Value']);
                        break;
                    case 'PhoneNumber':
                        $paymentData['phone_number'] = $item['Value'];
                        break;
                }
            }
        }

        return $paymentData;
    }
}
