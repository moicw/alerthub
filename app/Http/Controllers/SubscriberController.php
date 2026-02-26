<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HandlesIncludes;
use App\Http\Controllers\Concerns\HandlesPagination;
use App\Http\Resources\SubscriberResource;
use App\Models\Project;
use App\Models\Subscriber;
use Illuminate\Http\Request;

class SubscriberController extends Controller
{
    use HandlesIncludes;
    use HandlesPagination;

    public function index(Project $project, Request $request)
    {
        $org = app('tenant');

        if ($project->organization_id !== $org->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $query = Subscriber::where('project_id', $project->id);

        $includes = $this->parseIncludes($request, [
            'notifications' => 'notifications',
        ]);

        if (!empty($includes)) {
            $query->with($includes);
        }

        $subscribers = $this->paginate($query->orderBy('id'), $request);

        return SubscriberResource::collection($subscribers);
    }

    public function store(Project $project, Request $request)
    {
        $org = app('tenant');

        if ($project->organization_id !== $org->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'email' => 'nullable|email',
            'external_id' => 'nullable|string|max:255',
            'name' => 'nullable|string|max:255',
            'metadata' => 'nullable|array',
        ]);

        if (empty($validated['email']) && empty($validated['external_id'])) {
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => [
                    'email' => ['Email or external_id is required.'],
                ],
            ], 422);
        }

        $subscriber = Subscriber::create([
            'project_id' => $project->id,
            'email' => $validated['email'] ?? null,
            'external_id' => $validated['external_id'] ?? null,
            'name' => $validated['name'] ?? null,
            'metadata' => $validated['metadata'] ?? [],
        ]);

        return (new SubscriberResource($subscriber))
            ->response()
            ->setStatusCode(201);
    }
}
