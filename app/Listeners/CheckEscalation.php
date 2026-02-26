<?php

namespace App\Listeners;

use App\Events\NotificationCreated;

class CheckEscalation
{
    public function handle(NotificationCreated $event): void
    {
        $notification = $event->notification;
        $subscriber = $notification->subscriber;

        if (!$subscriber) {
            return;
        }

        $threshold = 10;
        $windowMinutes = 60;

        $recentCount = $subscriber->notifications()
            ->where('created_at', '>=', now()->subMinutes($windowMinutes))
            ->count();

        if ($recentCount > $threshold) {
            $notification->update([
                'status' => 'escalated',
            ]);
        }
    }
}
