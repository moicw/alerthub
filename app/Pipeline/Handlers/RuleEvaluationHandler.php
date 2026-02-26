<?php

namespace App\Pipeline\Handlers;

use App\Models\AlertRule;
use App\Pipeline\Handler;
use App\Pipeline\HandlerResult;
use App\Pipeline\PipelineContext;
use Illuminate\Support\Arr;

class RuleEvaluationHandler extends Handler
{
    public function handle(PipelineContext $context): HandlerResult
    {
        $rules = AlertRule::where('project_id', $context->project->id)
            ->where('is_active', true)
            ->where('source_type', $context->sourceType)
            ->where('event_type', $context->eventType)
            ->get();

        if ($rules->isEmpty()) {
            return HandlerResult::QUIT;
        }

        $matched = [];
        foreach ($rules as $rule) {
            if ($this->matchesConditions($rule->conditions ?? [], $context->payload)) {
                $matched[] = $rule;
            }
        }

        if (empty($matched)) {
            return HandlerResult::QUIT;
        }

        $context->matchedRules = $matched;

        return HandlerResult::CONTINUE;
    }

    protected function matchesConditions(array $conditions, array $payload): bool
    {
        if (empty($conditions)) {
            return true;
        }

        $isAssoc = Arr::isAssoc($conditions);

        $rules = $isAssoc ? [$conditions] : $conditions;

        foreach ($rules as $rule) {
            $field = $rule['field'] ?? null;
            $operator = $rule['operator'] ?? '==';
            $value = $rule['value'] ?? null;

            if (!$field) {
                continue;
            }

            $actual = data_get($payload, $field);

            if (!$this->compare($actual, $operator, $value)) {
                return false;
            }
        }

        return true;
    }

    protected function compare(mixed $actual, string $operator, mixed $expected): bool
    {
        return match ($operator) {
            '=', '==' => $actual == $expected,
            '===', 'strict' => $actual === $expected,
            '!=' => $actual != $expected,
            '!==' => $actual !== $expected,
            '>' => $actual > $expected,
            '>=' => $actual >= $expected,
            '<' => $actual < $expected,
            '<=' => $actual <= $expected,
            'contains' => is_string($actual) && str_contains($actual, (string) $expected),
            default => $actual == $expected,
        };
    }
}
