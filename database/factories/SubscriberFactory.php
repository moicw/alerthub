<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Subscriber>
 */
class SubscriberFactory extends Factory
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
            'email' => $this->faker->unique()->safeEmail(),
            'external_id' => $this->faker->uuid(),
            'name' => $this->faker->name(),
            'notification_count' => 0,
            'last_notified_at' => null,
            'metadata' => [
                'created_via' => 'seed',
            ],
        ];
    }
}
