<?php
/**
 * Pricing Service
 * Handles pricing calculations based on activity tiers
 * Omniwallet CMS
 */

namespace App\Services;

class PricingService
{
    private array $config;

    public function __construct()
    {
        $this->config = require __DIR__ . '/../../config/pricing.php';
    }

    /**
     * Get all plans
     */
    public function getPlans(): array
    {
        return $this->config['plans'] ?? [];
    }

    /**
     * Get activity tiers
     */
    public function getActivityTiers(): array
    {
        return $this->config['activity_tiers'] ?? [];
    }

    /**
     * Get full config
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * Calculate pricing based on sales (number of transactions) and plan
     *
     * @param int $monthlySales Number of sales/transactions (not euros)
     * @param string $planId Plan identifier (starter, plus, advanced)
     * @return array Pricing calculation result
     */
    public function calculate(int $monthlySales, string $planId = 'advanced'): array
    {
        $plans = $this->getPlans();
        $tiers = $this->getActivityTiers();

        // Find the plan
        $plan = $plans[$planId] ?? $plans['advanced'] ?? ['price' => 249, 'name' => 'Advanced'];

        // Calculate activities (sales × 1.3)
        $activityMultiplier = $this->config['activity_multiplier'] ?? 1.3;
        $activities = (int)round($monthlySales * $activityMultiplier);

        $freemiumLimit = $this->config['freemium_limit'] ?? 100;
        $maxCalculator = $this->config['max_calculator'] ?? 150000;

        // Check if freemium
        if ($activities < $freemiumLimit) {
            return [
                'is_freemium' => true,
                'is_high_volume' => false,
                'plan' => $plan,
                'monthly_sales' => $monthlySales,
                'activities' => $activities,
                'base_price' => 0,
                'activity_cost' => 0,
                'total_cost' => 0,
                'breakdown' => []
            ];
        }

        // Check if high volume
        if ($activities > $maxCalculator) {
            return [
                'is_freemium' => false,
                'is_high_volume' => true,
                'plan' => $plan,
                'monthly_sales' => $monthlySales,
                'activities' => $activities,
                'base_price' => $plan['price'] ?? 0,
                'activity_cost' => null,
                'total_cost' => null,
                'breakdown' => []
            ];
        }

        // Calculate activity cost based on tiers
        $activityResult = $this->calculateActivityCost($activities, $tiers);

        // Total cost = plan base price + activity cost
        $basePrice = $plan['price'] ?? 0;
        $totalCost = $basePrice + $activityResult['total'];

        return [
            'is_freemium' => false,
            'is_high_volume' => false,
            'plan' => $plan,
            'monthly_sales' => $monthlySales,
            'activities' => $activities,
            'base_price' => $basePrice,
            'activity_cost' => round($activityResult['total'], 2),
            'total_cost' => round($totalCost, 2),
            'breakdown' => $activityResult['breakdown']
        ];
    }

    /**
     * Calculate activity cost based on tiered pricing (cumulative tiers)
     */
    private function calculateActivityCost(int $activities, array $tiers): array
    {
        $totalCost = 0;
        $remaining = $activities;
        $previousMax = 0;
        $breakdown = [];

        foreach ($tiers as $tier) {
            if ($remaining <= 0) break;

            $tierSize = $tier['max'] - $previousMax;
            $activitiesInTier = min($remaining, $tierSize);
            $tierCost = $activitiesInTier * $tier['price'];

            if ($activitiesInTier > 0) {
                $breakdown[] = [
                    'range' => $tier['label'] ?? (($previousMax + 1) . ' - ' . $tier['max']),
                    'activities' => $activitiesInTier,
                    'price_per_activity' => $tier['price'],
                    'subtotal' => round($tierCost, 2)
                ];
            }

            $totalCost += $tierCost;
            $remaining -= $activitiesInTier;
            $previousMax = $tier['max'];
        }

        return [
            'total' => $totalCost,
            'breakdown' => $breakdown
        ];
    }

    /**
     * Get recommended plan based on activity volume
     */
    public function getRecommendedPlan(int $monthlySales): array
    {
        $plans = $this->getPlans();
        $activityMultiplier = $this->config['activity_multiplier'] ?? 1.3;
        $activities = (int)round($monthlySales * $activityMultiplier);

        // Simple recommendation based on activity thresholds
        if ($activities < 500) {
            return $plans['starter'] ?? [];
        } elseif ($activities < 2000) {
            return $plans['plus'] ?? [];
        } else {
            return $plans['advanced'] ?? [];
        }
    }

    /**
     * Compare all plans for given sales
     */
    public function comparePlans(int $monthlySales): array
    {
        $plans = $this->getPlans();
        $comparisons = [];

        foreach ($plans as $planId => $plan) {
            $comparisons[$planId] = $this->calculate($monthlySales, $planId);
        }

        return $comparisons;
    }
}
