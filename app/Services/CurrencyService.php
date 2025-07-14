<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class CurrencyService
{
    /**
     * Convert amount from one currency to another
     */
    public function convertCurrency(float $amount, string $fromCurrency, string $toCurrency): float
    {
        // If same currency, no conversion needed
        if ($fromCurrency === $toCurrency) {
            return $amount;
        }

        // Get exchange rates from config
        $exchangeRates = config('currency.exchange_rates');

        // Convert to KES (base currency)
        if ($toCurrency === 'KES') {
            return $this->convertToKes($amount, $fromCurrency, $exchangeRates);
        }

        // Convert from KES to other currency
        if ($fromCurrency === 'KES') {
            return $this->convertFromKes($amount, $toCurrency, $exchangeRates);
        }

        // Convert between two non-KES currencies (via KES)
        $amountInKes = $this->convertToKes($amount, $fromCurrency, $exchangeRates);
        return $this->convertFromKes($amountInKes, $toCurrency, $exchangeRates);
    }

    /**
     * Convert amount to KES from another currency
     */
    private function convertToKes(float $amount, string $fromCurrency, array $exchangeRates): float
    {
        if ($fromCurrency === 'KES') {
            return $amount;
        }

        if (!isset($exchangeRates[$fromCurrency]['to_kes'])) {
            Log::error('Exchange rate not found for currency conversion', [
                'from_currency' => $fromCurrency,
                'to_currency' => 'KES',
                'amount' => $amount
            ]);
            throw new \Exception("Exchange rate not found for {$fromCurrency} to KES");
        }

        $rate = $exchangeRates[$fromCurrency]['to_kes'];
        $convertedAmount = $amount * $rate;

        Log::info('Currency conversion to KES', [
            'from_currency' => $fromCurrency,
            'original_amount' => $amount,
            'exchange_rate' => $rate,
            'converted_amount' => $convertedAmount
        ]);

        return round($convertedAmount, 2);
    }

    /**
     * Convert amount from KES to another currency
     */
    private function convertFromKes(float $amount, string $toCurrency, array $exchangeRates): float
    {
        if ($toCurrency === 'KES') {
            return $amount;
        }

        if (!isset($exchangeRates[$toCurrency]['from_kes'])) {
            Log::error('Exchange rate not found for currency conversion', [
                'from_currency' => 'KES',
                'to_currency' => $toCurrency,
                'amount' => $amount
            ]);
            throw new \Exception("Exchange rate not found for KES to {$toCurrency}");
        }

        $rate = $exchangeRates[$toCurrency]['from_kes'];
        $convertedAmount = $amount * $rate;

        Log::info('Currency conversion from KES', [
            'to_currency' => $toCurrency,
            'original_amount' => $amount,
            'exchange_rate' => $rate,
            'converted_amount' => $convertedAmount
        ]);

        return round($convertedAmount, 2);
    }

    /**
     * Get supported currencies
     */
    public function getSupportedCurrencies(): array
    {
        return config('currency.supported_currencies');
    }

    /**
     * Get default currency
     */
    public function getDefaultCurrency(): string
    {
        return config('currency.default_currency');
    }

    /**
     * Check if currency is supported
     */
    public function isCurrencySupported(string $currency): bool
    {
        return array_key_exists($currency, $this->getSupportedCurrencies());
    }

    /**
     * Format amount with currency symbol
     */
    public function formatAmount(float $amount, string $currency): string
    {
        $currencies = $this->getSupportedCurrencies();

        if (!isset($currencies[$currency])) {
            return "{$currency} " . number_format($amount, 2);
        }

        $symbol = $currencies[$currency]['symbol'];
        $decimalPlaces = $currencies[$currency]['decimal_places'];

        return $symbol . ' ' . number_format($amount, $decimalPlaces);
    }
}
