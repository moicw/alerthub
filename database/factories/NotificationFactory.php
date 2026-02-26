<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Notification>
 */
class NotificationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'uuid' => (string) Str::uuid(),
            'project_id' => \App\Models\Project::factory(),
            'subscriber_id' => \App\Models\Subscriber::factory(),
            'alert_rule_id' => null,
            'channel' => $this->faker->randomElement(['email', 'webhook']),
            'subject' => $this->faker->sentence(),
            'body' => $this->faker->paragraph(),
            'payload' => [
                'source' => $this->faker->randomElement(['github', 'stripe', 'monitoring']),
                'event_type' => $this->faker->word(),
            ],
            'status' => $this->faker->randomElement(['pending', 'sent', 'failed', 'escalated']),
            'sent_at' => $this->faker->boolean(70) ? $this->faker->dateTimeBetween('-7 days') : null,
        ];
    }
}
