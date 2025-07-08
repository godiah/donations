<?php

namespace App\Services;

use App\Models\DonationLink;
use App\Models\Contribution;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class DonationStatisticsService
{
    /**
     * Cache duration for statistics (in minutes)
     */
    const STATS_CACHE_DURATION = 15;

    /**
     * Cache duration for exchange rates (in hours)
     */
    const EXCHANGE_RATE_CACHE_DURATION = 6;

    /**
     * Get comprehensive statistics for a donation link
     */
    public function getStatistics(DonationLink $donationLink, bool $useCache = true): array
    {
        $cacheKey = "donation_stats_{$donationLink->id}";

        if ($useCache && Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        try {
            $stats = $this->calculateStatistics($donationLink);

            // Cache the results
            if ($useCache) {
                Cache::put($cacheKey, $stats, now()->addMinutes(self::STATS_CACHE_DURATION));
            }

            return $stats;
        } catch (\Exception $e) {
            Log::error('Failed to calculate donation statistics', [
                'donation_link_id' => $donationLink->id,
                'donation_link_code' => $donationLink->code,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return $this->getDefaultStats($donationLink);
        }
    }

    /**
     * Calculate statistics for a donation link
     */
    private function calculateStatistics(DonationLink $donationLink): array
    {
        // Get all completed contributions for this donation link
        $completedContributions = $donationLink->contributions()
            ->where('payment_status', Contribution::STATUS_COMPLETED)
            ->get();

        $totalRaisedKes = 0;
        $totalContributors = $completedContributions->count();
        $currencyBreakdown = [];

        // Get current exchange rate
        $usdToKesRate = $this->getUsdToKesExchangeRate();

        // Process each contribution
        foreach ($completedContributions as $contribution) {
            $currency = strtoupper($contribution->currency);
            $amount = $contribution->amount;

            // Track currency breakdown
            if (!isset($currencyBreakdown[$currency])) {
                $currencyBreakdown[$currency] = [
                    'count' => 0,
                    'total_amount' => 0,
                    'total_kes_equivalent' => 0,
                ];
            }

            $currencyBreakdown[$currency]['count']++;
            $currencyBreakdown[$currency]['total_amount'] += $amount;

            // Convert to KES
            $kesAmount = $this->convertToKes($amount, $currency, $usdToKesRate);
            $currencyBreakdown[$currency]['total_kes_equivalent'] += $kesAmount;
            $totalRaisedKes += $kesAmount;
        }

        // Get target amount
        $targetAmount = $this->getTargetAmount($donationLink);

        // Calculate progress
        $progressData = $this->calculateProgress($totalRaisedKes, $targetAmount, $totalContributors);

        // Calculate additional statistics
        $averageContributionKes = $totalContributors > 0 ? $totalRaisedKes / $totalContributors : 0;

        $stats = [
            // Core statistics
            'total_raised_kes' => round($totalRaisedKes, 2),
            'total_raised_formatted' => number_format($totalRaisedKes, 2),
            'total_contributors' => $totalContributors,
            'average_contribution_kes' => round($averageContributionKes, 2),
            'average_contribution_formatted' => number_format($averageContributionKes, 2),

            // Progress data
            'target_amount' => $targetAmount,
            'target_amount_formatted' => number_format($targetAmount, 2),
            'progress_percentage' => $progressData['percentage'],
            'progress_type' => $progressData['type'],
            'remaining_to_target' => $progressData['remaining'],
            'remaining_to_target_formatted' => number_format($progressData['remaining'], 2),

            // Currency breakdown
            'currency_breakdown' => $currencyBreakdown,
            'has_multiple_currencies' => count($currencyBreakdown) > 1,

            // Meta information
            'exchange_rate_used' => $usdToKesRate,
            'last_updated' => now(),
            'cache_duration' => self::STATS_CACHE_DURATION,

            // Status flags
            'has_contributions' => $totalContributors > 0,
            'has_target' => $targetAmount > 0,
            'target_reached' => $targetAmount > 0 && $totalRaisedKes >= $targetAmount,
        ];

        Log::info('Donation statistics calculated', [
            'donation_link_id' => $donationLink->id,
            'donation_link_code' => $donationLink->code,
            'total_raised_kes' => $stats['total_raised_kes'],
            'total_contributors' => $stats['total_contributors'],
            'progress_percentage' => $stats['progress_percentage'],
            'progress_type' => $stats['progress_type'],
            'target_amount' => $stats['target_amount'],
        ]);

        return $stats;
    }

    /**
     * Get target amount for the donation link
     */
    private function getTargetAmount(DonationLink $donationLink): float
    {
        // Try to get target from application's applicant
        $targetAmount = $donationLink->application->applicant->target_amount ?? 0;

        // Fallback to donation link target if available
        if ($targetAmount <= 0) {
            $targetAmount = $donationLink->target_amount ?? 0;
        }

        return (float) $targetAmount;
    }

    /**
     * Calculate progress based on target amount and contributions
     */
    private function calculateProgress(float $totalRaisedKes, float $targetAmount, int $totalContributors): array
    {
        if ($targetAmount > 0) {
            // Target-based progress
            $percentage = min(($totalRaisedKes / $targetAmount) * 100, 100);
            $remaining = max($targetAmount - $totalRaisedKes, 0);

            return [
                'percentage' => round($percentage, 2),
                'type' => 'target_based',
                'remaining' => $remaining,
            ];
        } else {
            // Activity-based progress (when no target is set)
            // Show progress based on number of contributions and amount raised
            // This is more of a "momentum" indicator

            if ($totalContributors === 0) {
                $percentage = 0;
            } elseif ($totalContributors >= 100) {
                $percentage = 100; // Cap at 100% for 100+ contributors
            } else {
                // Scale based on contributors and amount
                $contributorScore = min($totalContributors * 2, 50); // Max 50% from contributors
                $amountScore = min($totalRaisedKes / 1000, 50); // Max 50% from amount (1K KES = 1%)
                $percentage = min($contributorScore + $amountScore, 100);
            }

            return [
                'percentage' => round($percentage, 2),
                'type' => 'activity_based',
                'remaining' => 0,
            ];
        }
    }

    /**
     * Convert amount to KES
     */
    private function convertToKes(float $amount, string $currency, float $usdToKesRate): float
    {
        switch (strtoupper($currency)) {
            case 'KES':
                return $amount;
            case 'USD':
                return $amount * $usdToKesRate;
            default:
                Log::warning('Unknown currency encountered, treating as KES', [
                    'currency' => $currency,
                    'amount' => $amount,
                ]);
                return $amount;
        }
    }

    /**
     * Get USD to KES exchange rate with caching
     */
    public function getUsdToKesExchangeRate(bool $useCache = true): float
    {
        $cacheKey = 'usd_to_kes_exchange_rate';

        if ($useCache && Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        try {
            // Try to get from config first
            $configRate = config('currency.exchange_rates.USD.to_kes');
            if ($configRate && $configRate > 0) {
                if ($useCache) {
                    Cache::put($cacheKey, $configRate, now()->addHours(self::EXCHANGE_RATE_CACHE_DURATION));
                }
                return $configRate;
            }

            // Try to fetch from API if enabled
            if (config('currency.api.enabled')) {
                $apiRate = $this->fetchExchangeRateFromApi();
                if ($apiRate && $apiRate > 0) {
                    if ($useCache) {
                        Cache::put($cacheKey, $apiRate, now()->addHours(self::EXCHANGE_RATE_CACHE_DURATION));
                    }
                    return $apiRate;
                }
            }

            // Fallback to default rate
            $defaultRate = 135.0;

            Log::info('Using default USD to KES exchange rate', [
                'rate' => $defaultRate,
                'reason' => 'No config or API rate available',
            ]);

            if ($useCache) {
                Cache::put($cacheKey, $defaultRate, now()->addHours(self::EXCHANGE_RATE_CACHE_DURATION));
            }

            return $defaultRate;
        } catch (\Exception $e) {
            Log::error('Failed to get USD to KES exchange rate', [
                'error' => $e->getMessage(),
            ]);

            return 135.0; // Fallback rate
        }
    }

    /**
     * Fetch exchange rate from external API
     */
    private function fetchExchangeRateFromApi(): ?float
    {
        try {
            $apiConfig = config('currency.api');
            $timeout = $apiConfig['timeout'] ?? 10;

            // Example with exchangerate-api.com (free tier)
            $response = Http::timeout($timeout)->get($apiConfig['base_url'] . 'USD');

            if ($response->successful()) {
                $data = $response->json();
                $kesRate = $data['rates']['KES'] ?? null;

                if ($kesRate && $kesRate > 0) {
                    Log::info('Exchange rate fetched from API', [
                        'rate' => $kesRate,
                        'provider' => $apiConfig['provider'],
                    ]);

                    return (float) $kesRate;
                }
            }

            Log::warning('Failed to get valid exchange rate from API', [
                'response_status' => $response->status(),
                'response_body' => $response->body(),
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Exception while fetching exchange rate from API', [
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Get default statistics (fallback when calculation fails)
     */
    private function getDefaultStats(DonationLink $donationLink): array
    {
        $targetAmount = $this->getTargetAmount($donationLink);

        return [
            'total_raised_kes' => 0,
            'total_raised_formatted' => '0.00',
            'total_contributors' => 0,
            'average_contribution_kes' => 0,
            'average_contribution_formatted' => '0.00',
            'target_amount' => $targetAmount,
            'target_amount_formatted' => number_format($targetAmount, 2),
            'progress_percentage' => 0,
            'progress_type' => $targetAmount > 0 ? 'target_based' : 'activity_based',
            'remaining_to_target' => $targetAmount,
            'remaining_to_target_formatted' => number_format($targetAmount, 2),
            'currency_breakdown' => [],
            'has_multiple_currencies' => false,
            'exchange_rate_used' => $this->getUsdToKesExchangeRate(),
            'last_updated' => now(),
            'cache_duration' => self::STATS_CACHE_DURATION,
            'has_contributions' => false,
            'has_target' => $targetAmount > 0,
            'target_reached' => false,
            'error' => true,
        ];
    }

    /**
     * Clear statistics cache for a donation link
     */
    public function clearCache(DonationLink $donationLink): void
    {
        $cacheKey = "donation_stats_{$donationLink->id}";
        Cache::forget($cacheKey);

        Log::info('Donation statistics cache cleared', [
            'donation_link_id' => $donationLink->id,
            'donation_link_code' => $donationLink->code,
        ]);
    }

    /**
     * Clear exchange rate cache
     */
    public function clearExchangeRateCache(): void
    {
        Cache::forget('usd_to_kes_exchange_rate');
        Log::info('Exchange rate cache cleared');
    }

    /**
     * Refresh statistics for a donation link
     */
    public function refreshStatistics(DonationLink $donationLink): array
    {
        $this->clearCache($donationLink);
        return $this->getStatistics($donationLink, false);
    }
}
