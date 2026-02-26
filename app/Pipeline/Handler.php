<?php

namespace App\Pipeline;

abstract class Handler
{
    abstract public function handle(PipelineContext $context): HandlerResult;
}
