<?php

namespace App\Pipeline\Handlers;

use App\Pipeline\Handler;
use App\Pipeline\HandlerResult;
use App\Pipeline\PipelineContext;
use Illuminate\Support\Facades\Log;

class ValidationHandler extends Handler
{
    public function handle(PipelineContext $context): HandlerResult
    {
        if (empty($context->payload['event_type']) || empty($context->payload['source'])) {
            Log::warning('ValidationHandler: missing event_type or source', [
                'project_id' => $context->project->id,
            ]);

            return HandlerResult::QUIT;
        }

        if (!isset($context->payload['payload']) || !is_array($context->payload['payload'])) {
            Log::warning('ValidationHandler: missing payload object', [
                'project_id' => $context->project->id,
            ]);

            return HandlerResult::QUIT;
        }

        $payload = $context->payload['payload'];

        switch ($context->sourceType) {
            case 'github':
                if (empty($payload['sender']) || empty($payload['repository'])) {
                    Log::warning('ValidationHandler: invalid github payload', [
                        'project_id' => $context->project->id,
                    ]);
                    return HandlerResult::QUIT;
                }
                break;
            case 'stripe':
                if (empty($payload['id'])) {
                    Log::warning('ValidationHandler: invalid stripe payload', [
                        'project_id' => $context->project->id,
                    ]);
                    return HandlerResult::QUIT;
                }
                break;
            case 'monitoring':
                if (empty($payload['alert_id'])) {
                    Log::warning('ValidationHandler: invalid monitoring payload', [
                        'project_id' => $context->project->id,
                    ]);
                    return HandlerResult::QUIT;
                }
                break;
            default:
                // Custom sources are accepted as-is
                break;
        }

        return HandlerResult::CONTINUE;
    }
}
