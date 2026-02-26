<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Organization;
use App\Models\Project;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $organizations = Organization::query()->get();

        if ($organizations->isEmpty()) {
            $organizations = Organization::factory()->count(2)->create();
        }

        foreach ($organizations as $organization) {
            Project::factory()
                ->count(2)
                ->create([
                    'organization_id' => $organization->id,
                ]);
        }
    }
}
