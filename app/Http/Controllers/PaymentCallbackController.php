<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\WithdrawalRequest;
use App\Services\WalletService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentCallbackController extends Controller
{
    protected $walletService;

    public function __construct(WalletService $walletService)
    {
        $this->walletService = $walletService;
    }

    /**
     * Handle M-Pesa callback
     */
    public function handleMpesaCallback(Request $request): JsonResponse
    {
        try {
            $data = $request->all();

            Log::info('M-Pesa callback received', [
                'data' => $data,
                'ip' => $request->ip(),
            ]);

            // Validate callback data
            if (!isset($data['transaction_id'])) {
                Log::warning('M-Pesa callback missing transaction_id', $data);
                return response()->json(['success' => false, 'message' => 'Missing transaction_id'], 400);
            }

            // Find withdrawal by reference
            $withdrawal = WithdrawalRequest::where('gateway_reference', $data['transaction_id'])->first();

            if (!$withdrawal) {
                Log::warning('M-Pesa callback for unknown transaction', [
                    'transaction_id' => $data['transaction_id'],
                    'data' => $data,
                ]);
                return response()->json(['success' => false, 'message' => 'Transaction not found'], 404);
            }

            // Check if withdrawal is in processing state
            if ($withdrawal->status !== WithdrawalRequest::STATUS_PROCESSING) {
                Log::warning('M-Pesa callback for non-processing withdrawal', [
                    'withdrawal_id' => $withdrawal->id,
                    'current_status' => $withdrawal->status,
                    'transaction_id' => $data['transaction_id'],
                ]);
                return response()->json(['success' => false, 'message' => 'Invalid withdrawal status'], 400);
            }

            // Process the callback based on result
            if (isset($data['result_code']) && $data['result_code'] === '0') {
                // Success - complete the withdrawal
                $this->walletService->completeWithdrawal($withdrawal, $data['transaction_id']);

                Log::info('M-Pesa payment completed successfully', [
                    'withdrawal_id' => $withdrawal->id,
                    'transaction_id' => $data['transaction_id'],
                    'amount' => $withdrawal->net_amount,
                ]);
            } else {
                // Failed - mark as failed and restore balance
                $errorMessage = $data['result_desc'] ?? 'Payment failed';

                $withdrawal->update([
                    'status' => WithdrawalRequest::STATUS_FAILED,
                    'rejection_reason' => 'M-Pesa payment failed: ' . $errorMessage,
                ]);

                $this->walletService->cancelWithdrawal($withdrawal);

                Log::error('M-Pesa payment failed', [
                    'withdrawal_id' => $withdrawal->id,
                    'transaction_id' => $data['transaction_id'],
                    'error' => $errorMessage,
                    'result_code' => $data['result_code'] ?? 'unknown',
                ]);
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('M-Pesa callback processing error', [
                'error' => $e->getMessage(),
                'data' => $request->all(),
            ]);

            return response()->json(['success' => false, 'message' => 'Processing error'], 500);
        }
    }

    /**
     * Handle bank transfer callback
     */
    public function handleBankCallback(Request $request): JsonResponse
    {
        try {
            $data = $request->all();

            Log::info('Bank transfer callback received', [
                'data' => $data,
                'ip' => $request->ip(),
            ]);

            // Validate callback data
            if (!isset($data['reference'])) {
                Log::warning('Bank callback missing reference', $data);
                return response()->json(['success' => false, 'message' => 'Missing reference'], 400);
            }

            $withdrawal = WithdrawalRequest::where('gateway_reference', $data['reference'])->first();

            if (!$withdrawal) {
                Log::warning('Bank callback for unknown transaction', [
                    'reference' => $data['reference'],
                    'data' => $data,
                ]);
                return response()->json(['success' => false, 'message' => 'Transaction not found'], 404);
            }

            // Check if withdrawal is in processing state
            if ($withdrawal->status !== WithdrawalRequest::STATUS_PROCESSING) {
                Log::warning('Bank callback for non-processing withdrawal', [
                    'withdrawal_id' => $withdrawal->id,
                    'current_status' => $withdrawal->status,
                    'reference' => $data['reference'],
                ]);
                return response()->json(['success' => false, 'message' => 'Invalid withdrawal status'], 400);
            }

            if (isset($data['status']) && $data['status'] === 'successful') {
                $this->walletService->completeWithdrawal($withdrawal, $data['reference']);

                Log::info('Bank transfer completed successfully', [
                    'withdrawal_id' => $withdrawal->id,
                    'reference' => $data['reference'],
                    'amount' => $withdrawal->net_amount,
                ]);
            } else {
                $errorMessage = $data['error_message'] ?? 'Transfer failed';

                $withdrawal->update([
                    'status' => WithdrawalRequest::STATUS_FAILED,
                    'rejection_reason' => 'Bank transfer failed: ' . $errorMessage,
                ]);

                $this->walletService->cancelWithdrawal($withdrawal);

                Log::error('Bank transfer failed', [
                    'withdrawal_id' => $withdrawal->id,
                    'reference' => $data['reference'],
                    'error' => $errorMessage,
                ]);
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Bank callback processing error', [
                'error' => $e->getMessage(),
                'data' => $request->all(),
            ]);

            return response()->json(['success' => false, 'message' => 'Processing error'], 500);
        }
    }

    /**
     * Handle paybill callback
     */
    public function handlePaybillCallback(Request $request): JsonResponse
    {
        try {
            $data = $request->all();

            Log::info('Paybill callback received', [
                'data' => $data,
                'ip' => $request->ip(),
            ]);

            // Validate callback data
            if (!isset($data['transaction_id'])) {
                Log::warning('Paybill callback missing transaction_id', $data);
                return response()->json(['success' => false, 'message' => 'Missing transaction_id'], 400);
            }

            $withdrawal = WithdrawalRequest::where('gateway_reference', $data['transaction_id'])->first();

            if (!$withdrawal) {
                Log::warning('Paybill callback for unknown transaction', [
                    'transaction_id' => $data['transaction_id'],
                    'data' => $data,
                ]);
                return response()->json(['success' => false, 'message' => 'Transaction not found'], 404);
            }

            // Check if withdrawal is in processing state
            if ($withdrawal->status !== WithdrawalRequest::STATUS_PROCESSING) {
                Log::warning('Paybill callback for non-processing withdrawal', [
                    'withdrawal_id' => $withdrawal->id,
                    'current_status' => $withdrawal->status,
                    'transaction_id' => $data['transaction_id'],
                ]);
                return response()->json(['success' => false, 'message' => 'Invalid withdrawal status'], 400);
            }

            if (isset($data['status']) && $data['status'] === 'completed') {
                $this->walletService->completeWithdrawal($withdrawal, $data['transaction_id']);

                Log::info('Paybill payment completed successfully', [
                    'withdrawal_id' => $withdrawal->id,
                    'transaction_id' => $data['transaction_id'],
                    'amount' => $withdrawal->net_amount,
                ]);
            } else {
                $errorMessage = $data['error_message'] ?? 'Payment failed';

                $withdrawal->update([
                    'status' => WithdrawalRequest::STATUS_FAILED,
                    'rejection_reason' => 'Paybill payment failed: ' . $errorMessage,
                ]);

                $this->walletService->cancelWithdrawal($withdrawal);

                Log::error('Paybill payment failed', [
                    'withdrawal_id' => $withdrawal->id,
                    'transaction_id' => $data['transaction_id'],
                    'error' => $errorMessage,
                ]);
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Paybill callback processing error', [
                'error' => $e->getMessage(),
                'data' => $request->all(),
            ]);

            return response()->json(['success' => false, 'message' => 'Processing error'], 500);
        }
    }
}
