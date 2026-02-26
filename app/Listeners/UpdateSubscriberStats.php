<?php

namespace App\Listeners;

use App\Events\NotificationCreated;

class UpdateSubscriberStats
{
    public function handle(NotificationCreated $event): void
    {
        $subscriber = $event->notification->subscriber;

        if (!$subscriber) {
            return;
        }

        $subscriber->increment('notification_count');
        $subscriber->forceFill([
            'last_notified_at' => now(),
        ])->save();
    }
}
