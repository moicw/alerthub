<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AlertRule>
 */
class AlertRuleFactory extends Factory
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
            'name' => $this->faker->words(2, true),
            'source_type' => $this->faker->randomElement(['github', 'stripe', 'monitoring', 'custom']),
            'event_type' => $this->faker->word(),
            'conditions' => [
                'field' => 'severity',
                'operator' => '>=',
                'value' => 'high',
            ],
            'action' => $this->faker->randomElement(['notify', 'escalate', 'digest']),
            'priority' => $this->faker->randomElement(['low', 'medium', 'high', 'critical']),
            'is_active' => $this->faker->boolean(85),
        ];
    }
}
