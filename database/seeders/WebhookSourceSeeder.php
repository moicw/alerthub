<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Project;
use App\Models\WebhookSource;

class WebhookSourceSeeder extends Seeder
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
            WebhookSource::factory()
                ->count(2)
                ->create([
                    'project_id' => $project->id,
                ]);
        }
    }
}
