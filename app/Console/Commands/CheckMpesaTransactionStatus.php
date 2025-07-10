<?php

namespace App\Console\Commands;

use App\Services\MpesaStatusChecker;
use App\Models\Transaction;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckMpesaTransactionStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mpesa:check-status 
                            {--transaction-id= : Check specific transaction by ID}
                            {--hours=3 : Check transactions from last X hours (default: 3)}
                            {--dry-run : Show what would be checked without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check the status of pending M-Pesa transactions';

    protected $mpesaStatusChecker;

    /**
     * Create a new command instance.
     */
    public function __construct(MpesaStatusChecker $mpesaStatusChecker)
    {
        parent::__construct();
        $this->mpesaStatusChecker = $mpesaStatusChecker;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting M-Pesa transaction status check...');
        
        $transactionId = $this->option('transaction-id');
        $hours = $this->option('hours');
        $dryRun = $this->option('dry-run');

        if ($transactionId) {
            $this->checkSpecificTransaction($transactionId, $dryRun);
        } else {
            $this->checkPendingTransactions($hours, $dryRun);
        }

        $this->info('M-Pesa transaction status check completed.');
    }

    /**
     * Check a specific transaction by ID
     */
    protected function checkSpecificTransaction(int $transactionId, bool $dryRun)
    {
        $transaction = Transaction::find($transactionId);
        
        if (!$transaction) {
            $this->error("Transaction with ID {$transactionId} not found.");
            return;
        }

        $this->info("Checking transaction ID: {$transactionId}");
        $this->table(
            ['ID', 'Status', 'Amount', 'Phone', 'Created At', 'Checkout Request ID'],
            [[
                $transaction->id,
                $transaction->status,
                $transaction->amount,
                $transaction->phone_number,
                $transaction->created_at->format('Y-m-d H:i:s'),
                $transaction->mpesa_checkout_request_id ?? 'N/A'
            ]]
        );

        if ($transaction->status !== Transaction::STATUS_PENDING) {
            $this->warn("Transaction is not pending (Status: {$transaction->status})");
            return;
        }

        if (!$transaction->mpesa_checkout_request_id) {
            $this->error("Transaction has no M-Pesa checkout request ID");
            return;
        }

        if ($dryRun) {
            $this->info("DRY RUN: Would check status for transaction {$transactionId}");
            return;
        }

        $this->info("Checking transaction status...");
        $this->mpesaStatusChecker->checkTransactionStatus($transaction);
        
        // Refresh transaction to show updated status
        $transaction->refresh();
        $this->info("Updated status: {$transaction->status}");
    }

    /**
     * Check all pending transactions
     */
    protected function checkPendingTransactions(int $hours, bool $dryRun)
    {
        // Get transactions that are still pending
        $pendingTransactions = Transaction::where('status', Transaction::STATUS_PENDING)
            ->where('mpesa_payment_type', 'stk_push')
            ->where('created_at', '<', now()->subMinutes(1))
            ->where('created_at', '>', now()->subHours($hours))
            ->get();

        $count = $pendingTransactions->count();
        $this->info("Found {$count} pending M-Pesa transactions from the last {$hours} hours");

        if ($count === 0) {
            $this->info("No pending transactions to check.");
            return;
        }

        // Display transactions in a table
        $this->table(
            ['ID', 'Amount', 'Phone', 'Created At', 'Age (minutes)'],
            $pendingTransactions->map(function ($transaction) {
                return [
                    $transaction->id,
                    $transaction->amount,
                    $transaction->phone_number,
                    $transaction->created_at->format('Y-m-d H:i:s'),
                    $transaction->created_at->diffInMinutes(now())
                ];
            })->toArray()
        );

        if ($dryRun) {
            $this->info("DRY RUN: Would check status for {$count} transactions");
            return;
        }

        $this->info("Checking transaction statuses...");
        $progressBar = $this->output->createProgressBar($count);
        $progressBar->start();

        $statusCounts = [
            'completed' => 0,
            'failed' => 0,
            'cancelled' => 0,
            'still_pending' => 0
        ];

        foreach ($pendingTransactions as $transaction) {
            $originalStatus = $transaction->status;
            
            $this->mpesaStatusChecker->checkTransactionStatus($transaction);
            
            // Refresh to get updated status
            $transaction->refresh();
            
            // Track status changes
            if ($transaction->status !== $originalStatus) {
                switch ($transaction->status) {
                    case Transaction::STATUS_COMPLETED:
                        $statusCounts['completed']++;
                        break;
                    case Transaction::STATUS_FAILED:
                        $statusCounts['failed']++;
                        break;
                    case Transaction::STATUS_CANCELLED:
                        $statusCounts['cancelled']++;
                        break;
                }
            } else {
                $statusCounts['still_pending']++;
            }

            $progressBar->advance();
            
            // Add delay between checks to avoid rate limiting
            sleep(1);
        }

        $progressBar->finish();
        $this->newLine(2);

        // Show summary
        $this->info("Status Check Summary:");
        $this->table(
            ['Status', 'Count'],
            [
                ['Completed', $statusCounts['completed']],
                ['Failed', $statusCounts['failed']],
                ['Cancelled', $statusCounts['cancelled']],
                ['Still Pending', $statusCounts['still_pending']]
            ]
        );

        if ($statusCounts['completed'] > 0) {
            $this->info("✅ {$statusCounts['completed']} transactions marked as completed");
        }
        if ($statusCounts['failed'] > 0) {
            $this->warn("❌ {$statusCounts['failed']} transactions marked as failed");
        }
        if ($statusCounts['cancelled'] > 0) {
            $this->warn("🚫 {$statusCounts['cancelled']} transactions marked as cancelled");
        }
        if ($statusCounts['still_pending'] > 0) {
            $this->comment("⏳ {$statusCounts['still_pending']} transactions still pending");
        }
    }
}