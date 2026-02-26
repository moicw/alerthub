<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HandlesPagination;
use App\Http\Resources\AlertRuleResource;
use App\Models\AlertRule;
use App\Models\Project;
use Illuminate\Http\Request;

class AlertRuleController extends Controller
{
    use HandlesPagination;

    public function index(Project $project, Request $request)
    {
        $org = app('tenant');

        if ($project->organization_id !== $org->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $query = AlertRule::where('project_id', $project->id)->orderBy('id');

        $alertRules = $this->paginate($query, $request);

        return AlertRuleResource::collection($alertRules);
    }

    public function store(Project $project, Request $request)
    {
        $org = app('tenant');

        if ($project->organization_id !== $org->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'source_type' => 'required|string|in:github,stripe,monitoring,custom',
            'event_type' => 'required|string|max:255',
            'conditions' => 'nullable|array',
            'action' => 'required|string|in:notify,escalate,digest',
            'priority' => 'required|string|in:low,medium,high,critical',
            'is_active' => 'boolean',
        ]);

        $alertRule = AlertRule::create([
            'project_id' => $project->id,
            'name' => $validated['name'],
            'source_type' => $validated['source_type'],
            'event_type' => $validated['event_type'],
            'conditions' => $validated['conditions'] ?? null,
            'action' => $validated['action'],
            'priority' => $validated['priority'],
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return (new AlertRuleResource($alertRule))
            ->response()
            ->setStatusCode(201);
    }
}
