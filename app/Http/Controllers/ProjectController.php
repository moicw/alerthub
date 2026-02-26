<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Http\Controllers\Concerns\HandlesIncludes;
use App\Http\Controllers\Concerns\HandlesPagination;
use App\Http\Resources\ProjectResource;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProjectController extends Controller
{
   use HandlesIncludes;
   use HandlesPagination;

   public function index()
    {
        $org = app('tenant');

        $query = Project::where('organization_id', $org->id);

        $includes = $this->parseIncludes(request(), [
            'subscribers' => 'subscribers',
            'alert_rules' => 'alertRules',
            'notifications' => 'notifications',
            'webhook_sources' => 'webhookSources',
        ]);

        if (!empty($includes)) {
            $query->with($includes);
        }

        $projects = $this->paginate($query->orderBy('id'), request(), 10);

        return ProjectResource::collection($projects);
    }

    public function store(Request $request)
    {
        $org = app('tenant');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $project = Project::create([
            'organization_id' => $org->id,
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
        ]);

        return (new ProjectResource($project))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Project $project)
    {
      $org = app('tenant');

      if ($project->organization_id !== $org->id) {
          return response()->json(['message' => 'Unauthorized'], 403);
      }

      $includes = $this->parseIncludes(request(), [
          'subscribers' => 'subscribers',
          'alert_rules' => 'alertRules',
          'notifications' => 'notifications',
          'webhook_sources' => 'webhookSources',
      ]);

      if (!empty($includes)) {
          $project->load($includes);
      }

      return new ProjectResource($project);
    }
    
    public function update(Request $request, Project $project)
    {
        $org = app('tenant');

        if ($project->organization_id !== $org->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $project->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
        ]);

        return new ProjectResource($project);
        
    }
    public function destroy(Project $project)
    {
        $org = app('tenant');

        if ($project->organization_id !== $org->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $project->delete();

        return response()->json(['message' => 'Project deleted successfully']);
    }
}
