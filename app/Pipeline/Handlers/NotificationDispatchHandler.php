<?php

namespace App\Pipeline\Handlers;

use App\Jobs\SendNotification;
use App\Models\Notification;
use App\Pipeline\Handler;
use App\Pipeline\HandlerResult;
use App\Pipeline\PipelineContext;

class NotificationDispatchHandler extends Handler
{
    public function handle(PipelineContext $context): HandlerResult
    {
        foreach ($context->matchedRules as $rule) {
            $notification = Notification::create([
                'project_id' => $context->project->id,
                'subscriber_id' => $context->subscriber?->id,
                'alert_rule_id' => $rule->id,
                'channel' => 'email',
                'subject' => sprintf('Alert: %s', $context->eventType),
                'body' => json_encode([
                    'event_type' => $context->eventType,
                    'source' => $context->sourceType,
                    'payload' => $context->payload['payload'] ?? [],
                ]),
                'payload' => $context->payload,
                'status' => 'pending',
            ]);

            SendNotification::dispatch($notification->id);
        }

        return HandlerResult::CONTINUE;
    }
}
