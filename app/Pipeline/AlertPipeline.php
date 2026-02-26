<?php

namespace App\Pipeline;

use App\Pipeline\Handlers\NotificationDispatchHandler;

class AlertPipeline
{
    /**
     * @var array<int, Handler>
     */
    protected array $handlers;

    protected ?NotificationDispatchHandler $dispatchHandler = null;

    /**
     * @param  array<int, Handler>  $handlers
     */
    public function __construct(array $handlers)
    {
        $this->handlers = $handlers;

        foreach ($handlers as $handler) {
            if ($handler instanceof NotificationDispatchHandler) {
                $this->dispatchHandler = $handler;
            }
        }
    }

    public function run(PipelineContext $context): void
    {
        foreach ($this->handlers as $handler) {
            $result = $handler->handle($context);

            if ($result === HandlerResult::QUIT) {
                return;
            }

            if ($result === HandlerResult::SKIP_TO_DISPATCH) {
                if ($this->dispatchHandler) {
                    $this->dispatchHandler->handle($context);
                }

                return;
            }
        }
    }
}
