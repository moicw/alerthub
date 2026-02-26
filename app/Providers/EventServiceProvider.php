<?php

namespace App\Providers;

use App\Events\NotificationCreated;
use App\Listeners\CheckEscalation;
use App\Listeners\TrackNotificationMetrics;
use App\Listeners\UpdateSubscriberStats;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        NotificationCreated::class => [
            UpdateSubscriberStats::class,
            CheckEscalation::class,
            TrackNotificationMetrics::class,
        ],
    ];
}
