<?php

namespace Database\Seeders;

use App\Models\AlertRule;
use App\Models\Notification;
use App\Models\Project;
use App\Models\Subscriber;
use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
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
            $subscribers = Subscriber::where('project_id', $project->id)->get();
            $alertRules = AlertRule::where('project_id', $project->id)->get();

            if ($subscribers->isEmpty()) {
                $subscribers = Subscriber::factory()->count(3)->create([
                    'project_id' => $project->id,
                ]);
            }

            if ($alertRules->isEmpty()) {
                $alertRules = AlertRule::factory()->count(2)->create([
                    'project_id' => $project->id,
                ]);
            }

            for ($i = 0; $i < 10; $i++) {
                $subscriber = $subscribers->random();
                $alertRule = $alertRules->random();

                Notification::factory()->create([
                    'project_id' => $project->id,
                    'subscriber_id' => $subscriber->id,
                    'alert_rule_id' => $alertRule->id,
                    'status' => 'sent',
                    'sent_at' => now()->subMinutes(rand(1, 120)),
                ]);
            }
        }
    }
}
