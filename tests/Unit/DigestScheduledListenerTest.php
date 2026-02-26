<?php

namespace Tests\Unit;

use AlertMetrics\Events\DigestScheduled;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DigestScheduledListenerTest extends TestCase
{
    use RefreshDatabase;

    public function test_digest_window_is_calculated_after_reference_id(): void
    {
        $event = new DigestScheduled(
            subscriberId: 1,
            projectId: 1,
            date: now()->toDateString(),
            alertIds: range(1, 6),
            digestType: 'daily'
        );

        event($event);

        $this->assertNotNull($event->referenceId);
        $this->assertNotNull($event->scheduledWindow);
    }
}
