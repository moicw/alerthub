<?php

namespace App\Pipeline;

use App\Models\Project;
use App\Models\Subscriber;
use App\Models\WebhookSource;

class PipelineContext
{
    public Project $project;
    public WebhookSource $webhookSource;
    public array $payload;
    public string $sourceType;
    public string $eventType;
    public ?Subscriber $subscriber = null;
    public array $matchedRules = [];

    public function __construct(Project $project, WebhookSource $webhookSource, array $payload)
    {
        $this->project = $project;
        $this->webhookSource = $webhookSource;
        $this->payload = $payload;
        $this->sourceType = (string) ($payload['source'] ?? $webhookSource->source_type);
        $this->eventType = (string) ($payload['event_type'] ?? 'unknown');
    }
}
