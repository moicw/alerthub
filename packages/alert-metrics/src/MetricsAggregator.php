<?php

namespace AlertMetrics;

use Illuminate\Support\Facades\Cache;

class MetricsAggregator
{
    /**
     * Get the count of alerts for a given date.
     *
     * Used by the dashboard and digest scheduler to track alert volume.
     * Results are cached for 1 hour to reduce database load.
     *
     * @param  int     $projectId
     * @param  string  $date  Date in Y-m-d format
     * @return int
     */
    public function getDailyAlertCount(int $projectId, string $date): int
    {
        $cacheKey = "alert-metrics::{$projectId}::{$date}";

        return Cache::remember($cacheKey, 3600, function () use ($projectId, $date) {
            return \App\Models\Notification::where('project_id', $projectId)
                ->whereDate('created_at', $date)
                ->count();
        });
    }

    /**
     * Get hourly breakdown of alert counts for a date.
     *
     * @param  int     $projectId
     * @param  string  $date  Date in Y-m-d format
     * @return array
     */
    public function getHourlyBreakdown(int $projectId, string $date): array
    {
        $cacheKey = "alert-metrics::hourly::{$projectId}::{$date}";

        return Cache::remember($cacheKey, 1800, function () use ($projectId, $date) {
            return \App\Models\Notification::where('project_id', $projectId)
                ->whereDate('created_at', $date)
                ->selectRaw('HOUR(created_at) as hour, COUNT(*) as count')
                ->groupByRaw('HOUR(created_at)')
                ->pluck('count', 'hour')
                ->toArray();
        });
    }

    /**
     * Record that an alert was processed.
     *
     * Increments the daily counter and invalidates stale cache.
     *
     * @param  int  $notificationId
     * @return void
     */
    public function recordAlert(int $notificationId): void
    {
        $notification = \App\Models\Notification::find($notificationId);

        if (!$notification) {
            return;
        }

        $projectId = $notification->project_id;
        $date = $notification->created_at->toDateString();
        $counterKey = "alert-metrics::counter::{$projectId}::{$date}";

        Cache::increment($counterKey);

        // Invalidate the cached count so next read is fresh
        Cache::forget("alert-metrics::{$projectId}::{$date}");
        Cache::forget("alert-metrics::hourly::{$projectId}::{$date}");
    }

    /**
     * Get alert count for a specific time window.
     *
     * @param  int     $projectId
     * @param  string  $startDate
     * @param  string  $endDate
     * @return int
     */
    public function getAlertCountForWindow(int $projectId, string $startDate, string $endDate): int
    {
        return \App\Models\Notification::where('project_id', $projectId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();
    }
}
