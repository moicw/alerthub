<?php

namespace App\Pipeline\Handlers;

use AlertMetrics\SubscriberResolver;
use App\Pipeline\Handler;
use App\Pipeline\HandlerResult;
use App\Pipeline\PipelineContext;

class SubscriberMatchHandler extends Handler
{
    protected SubscriberResolver $resolver;

    public function __construct(SubscriberResolver $resolver)
    {
        $this->resolver = $resolver;
    }

    public function handle(PipelineContext $context): HandlerResult
    {
        $subscriber = $this->resolver->resolve($context->project->id, $context->payload);

        if (!$subscriber) {
            return HandlerResult::QUIT;
        }

        $context->subscriber = $subscriber;

        return HandlerResult::CONTINUE;
    }
}
