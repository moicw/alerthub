<?php

namespace Tests\Unit;

use AlertMetrics\SubscriberResolver;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubscriberResolverTest extends TestCase
{
    use RefreshDatabase;

    public function test_resolves_by_external_id_when_email_missing(): void
    {
        $project = Project::factory()->create();
        $resolver = app(SubscriberResolver::class);

        $payload = [
            'source' => 'monitoring',
            'event_type' => 'alert.triggered',
            'payload' => [
                'contact' => [
                    'external_id' => 'ops-team',
                ],
            ],
        ];

        $first = $resolver->resolve($project->id, $payload);
        $second = $resolver->resolve($project->id, $payload);

        $this->assertNotNull($first);
        $this->assertNotNull($second);
        $this->assertSame($first->id, $second->id);
    }
}
