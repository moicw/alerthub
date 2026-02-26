<?php

namespace Tests\Feature;

use App\Jobs\ProcessWebhookEvent;
use App\Models\AlertRule;
use App\Models\Notification;
use App\Models\Project;
use App\Models\WebhookSource;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProcessWebhookPipelineTest extends TestCase
{
    use RefreshDatabase;

    public function test_pipeline_creates_notification_for_matching_rule(): void
    {
        $project = Project::factory()->create();
        $source = WebhookSource::factory()->create([
            'project_id' => $project->id,
            'source_key' => 'github-main',
            'source_type' => 'github',
            'is_active' => true,
        ]);

        AlertRule::factory()->create([
            'project_id' => $project->id,
            'source_type' => 'github',
            'event_type' => 'push',
            'conditions' => [
                'field' => 'payload.sender.email',
                'operator' => '==',
                'value' => 'jane@example.com',
            ],
            'action' => 'notify',
            'priority' => 'high',
            'is_active' => true,
        ]);

        $payload = [
            'event_type' => 'push',
            'source' => 'github',
            'payload' => [
                'sender' => ['email' => 'jane@example.com'],
                'repository' => ['full_name' => 'acme/payment-service'],
            ],
        ];

        $job = new ProcessWebhookEvent($project->id, $source->id, $payload);
        $job->handle(app(\AlertMetrics\SubscriberResolver::class));

        $this->assertSame(1, Notification::count());
    }
}
