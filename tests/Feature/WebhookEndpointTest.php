<?php

namespace Tests\Feature;

use App\Jobs\ProcessWebhookEvent;
use App\Models\Project;
use App\Models\WebhookSource;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class WebhookEndpointTest extends TestCase
{
    use RefreshDatabase;

    public function test_webhook_endpoint_dispatches_job(): void
    {
        Queue::fake();

        $project = Project::factory()->create();
        $source = WebhookSource::factory()->create([
            'project_id' => $project->id,
            'source_key' => 'github-main',
            'source_type' => 'github',
            'is_active' => true,
            'signing_secret' => null,
        ]);

        $payload = [
            'event_type' => 'push',
            'source' => 'github',
            'payload' => [
                'sender' => ['email' => 'jane@example.com'],
                'repository' => ['full_name' => 'acme/payment-service'],
            ],
        ];

        $response = $this->postJson("/api/webhooks/{$project->uuid}/{$source->source_key}", $payload);

        $response->assertStatus(202);
        Queue::assertPushed(ProcessWebhookEvent::class);
    }
}
