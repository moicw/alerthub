<?php

namespace Tests\Unit;

use AlertMetrics\EngagementScorer;
use App\Models\Subscriber;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class EngagementScorerTest extends TestCase
{
    use RefreshDatabase;

    public function test_scores_are_cached_per_context(): void
    {
        Cache::flush();

        $subscriber = Subscriber::factory()->create();
        $scorer = app(EngagementScorer::class);

        $scorer->calculateScore($subscriber->id, 'realtime');
        $scorer->calculateScore($subscriber->id, 'digest');

        $this->assertTrue(Cache::has("engagement::{$subscriber->id}::realtime"));
        $this->assertTrue(Cache::has("engagement::{$subscriber->id}::digest"));
    }
}
