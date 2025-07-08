<?php

namespace App\Console\Commands;

use App\Models\MpesaConfiguration;
use Illuminate\Console\Command;

class SetupMpesaCommand extends Command
{
    protected $signature = 'mpesa:setup {environment=sandbox}';
    protected $description = 'Set up M-Pesa configuration';

    public function handle(): int
    {
        $environment = $this->argument('environment');

        if (!in_array($environment, ['sandbox', 'production'])) {
            $this->error('Environment must be either "sandbox" or "production"');
            return Command::FAILURE;
        }

        $this->info("Setting up M-Pesa configuration for {$environment} environment...");

        // Deactivate existing configurations
        MpesaConfiguration::where('is_active', true)->update(['is_active' => false]);

        // Create or update configuration
        $config = MpesaConfiguration::updateOrCreate(
            ['environment' => $environment],
            [
                'consumer_key' => config("mpesa.{$environment}.consumer_key"),
                'consumer_secret' => config("mpesa.{$environment}.consumer_secret"),
                'business_short_code' => config("mpesa.{$environment}.business_short_code"),
                'lipa_na_mpesa_passkey' => config("mpesa.{$environment}.lipa_na_mpesa_passkey"),
                'confirmation_url' => config('mpesa.callback_urls.confirmation_url'),
                'validation_url' => config('mpesa.callback_urls.validation_url'),
                'queue_timeout_url' => config('mpesa.callback_urls.queue_timeout_url'),
                'result_url' => config('mpesa.callback_urls.result_url'),
                'is_active' => true,
            ]
        );

        if ($config->wasRecentlyCreated) {
            $this->info("M-Pesa configuration created for {$environment} environment");
        } else {
            $this->info("M-Pesa configuration updated for {$environment} environment");
        }

        // Validate configuration
        $errors = $config->validateStkPushConfig();
        if (!empty($errors)) {
            $this->error('Configuration validation failed:');
            foreach ($errors as $error) {
                $this->line("  - {$error}");
            }
            return Command::FAILURE;
        }

        $this->info('M-Pesa configuration is valid and ready to use!');
        return Command::SUCCESS;
    }
}
