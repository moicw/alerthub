<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use AlertMetrics\DigestScheduler;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('alerts:schedule-digests {projectId} {date?} {type?}', function () {
    $projectId = (int) $this->argument('projectId');
    $date = $this->argument('date') ?? now()->toDateString();
    $type = $this->argument('type') ?? 'daily';

    $scheduler = app(DigestScheduler::class);
    $count = $scheduler->scheduleDigests($projectId, $date, $type);

    $this->info("Scheduled {$count} digests for project {$projectId}.");
})->purpose('Schedule alert digests for a project');
