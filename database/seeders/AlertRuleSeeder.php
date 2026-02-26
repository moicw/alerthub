<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\AlertRule;
use App\Models\Project;

class AlertRuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $projects = Project::query()->get();

        if ($projects->isEmpty()) {
            $projects = Project::factory()->count(2)->create();
        }

        foreach ($projects as $project) {
            AlertRule::factory()
                ->count(3)
                ->create([
                    'project_id' => $project->id,
                ]);
        }
    }
}
