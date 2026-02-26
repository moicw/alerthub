<?php

namespace Tests\Unit;

use AlertMetrics\ProcessAlertDigest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProcessAlertDigestTest extends TestCase
{
    use RefreshDatabase;

    public function test_unique_id_includes_alert_ids(): void
    {
        $jobA = new ProcessAlertDigest(1, '2026-02-24', [1, 2, 3], 'daily');
        $jobB = new ProcessAlertDigest(1, '2026-02-24', [4, 5, 6], 'daily');

        $this->assertNotSame($jobA->uniqueId(), $jobB->uniqueId());
    }
}
