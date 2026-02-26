<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\WebhookSource>
 */
class WebhookSourceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'project_id' => \App\Models\Project::factory(),
            'source_key' => Str::random(16),
            'source_type' => $this->faker->randomElement(['github', 'stripe', 'monitoring', 'custom']),
            'name' => $this->faker->words(2, true),
            'signing_secret' => $this->faker->boolean(50) ? Str::random(32) : null,
            'event_mappings' => null,
            'is_active' => $this->faker->boolean(90),
        ];
    }
}
