<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HandlesPagination;
use App\Http\Resources\WebhookSourceResource;
use App\Models\Project;
use App\Models\WebhookSource;
use Illuminate\Http\Request;

class WebhookSourceController extends Controller
{
    use HandlesPagination;

    public function index(Project $project, Request $request)
    {
        $org = app('tenant');

        if ($project->organization_id !== $org->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $query = WebhookSource::where('project_id', $project->id)->orderBy('id');
        $sources = $this->paginate($query, $request);

        return WebhookSourceResource::collection($sources);
    }

    public function store(Project $project, Request $request)
    {
        $org = app('tenant');

        if ($project->organization_id !== $org->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'source_key' => 'required|string|max:255',
            'source_type' => 'required|string|in:github,stripe,monitoring,custom',
            'name' => 'required|string|max:255',
            'signing_secret' => 'nullable|string|max:255',
            'event_mappings' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        $existing = WebhookSource::where('project_id', $project->id)
            ->where('source_key', $validated['source_key'])
            ->first();

        if ($existing) {
            return new WebhookSourceResource($existing);
        }

        $webhookSource = WebhookSource::create([
            'project_id' => $project->id,
            'source_key' => $validated['source_key'],
            'source_type' => $validated['source_type'],
            'name' => $validated['name'],
            'signing_secret' => $validated['signing_secret'] ?? null,
            'event_mappings' => $validated['event_mappings'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return (new WebhookSourceResource($webhookSource))
            ->response()
            ->setStatusCode(201);
    }
}
