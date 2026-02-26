<?php

namespace App\Pipeline\Handlers;

use App\Pipeline\Handler;
use App\Pipeline\HandlerResult;
use App\Pipeline\PipelineContext;
use Illuminate\Support\Facades\Cache;

class DeduplicationHandler extends Handler
{
    public function handle(PipelineContext $context): HandlerResult
    {
        $hash = sha1(json_encode($context->payload));
        $cacheKey = "webhook-dedupe:{$context->project->id}:{$hash}";

        if (!Cache::add($cacheKey, true, 300)) {
            return HandlerResult::QUIT;
        }

        return HandlerResult::CONTINUE;
    }
}
