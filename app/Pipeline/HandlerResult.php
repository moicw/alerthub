<?php

namespace App\Pipeline;

enum HandlerResult: string
{
    case CONTINUE = 'continue';
    case QUIT = 'quit';
    case SKIP_TO_DISPATCH = 'skip_to_dispatch';
}
