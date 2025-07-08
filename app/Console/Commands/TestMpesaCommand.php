<?php

namespace App\Console\Commands;

use App\Services\MpesaService;
use Illuminate\Console\Command;

class TestMpesaCommand extends Command
{
    protected $signature = 'mpesa:test';
    protected $description = 'Test M-Pesa connection';

    public function handle(MpesaService $mpesaService): int
    {
        $this->info('Testing M-Pesa connection...');

        $result = $mpesaService->testConnection();

        if ($result['success']) {
            $this->info("✅ {$result['message']}");
            $this->line("Environment: {$result['environment']}");
            return Command::SUCCESS;
        } else {
            $this->error("❌ {$result['message']}");
            return Command::FAILURE;
        }
    }
}
