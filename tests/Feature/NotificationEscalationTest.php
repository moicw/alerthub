<?php

namespace Tests\Feature;

use App\Models\Notification;
use App\Models\Project;
use App\Models\Subscriber;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationEscalationTest extends TestCase
{
    use RefreshDatabase;

    public function test_escalation_runs_after_stats_update(): void
    {
        $project = Project::factory()->create();
        $subscriber = Subscriber::factory()->create([
            'project_id' => $project->id,
            'notification_count' => 10,
        ]);

        Notification::factory()->count(10)->create([
            'project_id' => $project->id,
            'subscriber_id' => $subscriber->id,
            'created_at' => now()->subMinutes(30),
        ]);

        $notification = Notification::factory()->create([
            'project_id' => $project->id,
            'subscriber_id' => $subscriber->id,
            'status' => 'pending',
        ]);

        $notification->refresh();

        $this->assertSame('escalated', $notification->status);
        $subscriber->refresh();
        $this->assertSame(11, $subscriber->notification_count);
    }
}
