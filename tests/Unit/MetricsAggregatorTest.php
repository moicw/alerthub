<?php

namespace Tests\Unit;

use AlertMetrics\MetricsAggregator;
use App\Models\Notification;
use App\Models\Project;
use App\Models\Subscriber;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MetricsAggregatorTest extends TestCase
{
    use RefreshDatabase;

    public function test_daily_count_is_scoped_to_project(): void
    {
        $projectA = Project::factory()->create();
        $projectB = Project::factory()->create();

        $subscriberA = Subscriber::factory()->create(['project_id' => $projectA->id]);
        $subscriberB = Subscriber::factory()->create(['project_id' => $projectB->id]);

        Notification::factory()->count(2)->create([
            'project_id' => $projectA->id,
            'subscriber_id' => $subscriberA->id,
            'created_at' => now(),
        ]);

        Notification::factory()->count(3)->create([
            'project_id' => $projectB->id,
            'subscriber_id' => $subscriberB->id,
            'created_at' => now(),
        ]);

        $aggregator = app(MetricsAggregator::class);
        $date = now()->toDateString();

        $this->assertSame(2, $aggregator->getDailyAlertCount($projectA->id, $date));
        $this->assertSame(3, $aggregator->getDailyAlertCount($projectB->id, $date));
    }
}
