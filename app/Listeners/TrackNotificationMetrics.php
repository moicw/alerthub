<?php

namespace App\Listeners;

use AlertMetrics\MetricsAggregator;
use App\Events\NotificationCreated;

class TrackNotificationMetrics
{
    protected MetricsAggregator $aggregator;

    public function __construct(MetricsAggregator $aggregator)
    {
        $this->aggregator = $aggregator;
    }

    public function handle(NotificationCreated $event): void
    {
        $this->aggregator->recordAlert($event->notification->id);
    }
}
