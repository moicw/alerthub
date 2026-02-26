<?php

namespace App\Jobs;

use AlertMetrics\SubscriberResolver;
use App\Models\Project;
use App\Models\WebhookSource;
use App\Pipeline\AlertPipeline;
use App\Pipeline\Handlers\DeduplicationHandler;
use App\Pipeline\Handlers\NotificationDispatchHandler;
use App\Pipeline\Handlers\RuleEvaluationHandler;
use App\Pipeline\Handlers\SubscriberMatchHandler;
use App\Pipeline\Handlers\ValidationHandler;
use App\Pipeline\PipelineContext;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessWebhookEvent implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $backoff = [5, 15, 30];
    public $uniqueFor = 300;

    protected int $projectId;
    protected int $webhookSourceId;
    protected array $payload;

    public function __construct(int $projectId, int $webhookSourceId, array $payload)
    {
        $this->projectId = $projectId;
        $this->webhookSourceId = $webhookSourceId;
        $this->payload = $payload;
    }

    public function uniqueId(): string
    {
        return $this->projectId . ':' . $this->webhookSourceId . ':' . sha1(json_encode($this->payload));
    }

    public function handle(SubscriberResolver $resolver): void
    {
        $project = Project::find($this->projectId);
        $webhookSource = WebhookSource::find($this->webhookSourceId);

        if (!$project || !$webhookSource) {
            return;
        }

        $context = new PipelineContext($project, $webhookSource, $this->payload);

        $pipeline = new AlertPipeline([
            new DeduplicationHandler(),
            new ValidationHandler(),
            new SubscriberMatchHandler($resolver),
            new RuleEvaluationHandler(),
            new NotificationDispatchHandler(),
        ]);

        $pipeline->run($context);
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('ProcessWebhookEvent: Job failed', [
            'project_id' => $this->projectId,
            'webhook_source_id' => $this->webhookSourceId,
            'error' => $exception->getMessage(),
        ]);
    }
}
