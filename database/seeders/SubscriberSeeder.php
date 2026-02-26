<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Subscriber;
use Illuminate\Database\Seeder;

class SubscriberSeeder extends Seeder
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
            Subscriber::factory()
                ->count(5)
                ->create([
                    'project_id' => $project->id,
                ]);
        }
    }
}
