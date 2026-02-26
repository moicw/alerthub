<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HandlesPagination;
use App\Http\Resources\NotificationResource;
use App\Models\Notification;
use App\Models\Project;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    use HandlesPagination;

    public function index(Project $project, Request $request)
    {
        $org = app('tenant');

        if ($project->organization_id !== $org->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $query = Notification::where('project_id', $project->id);

        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        if ($request->filled('channel')) {
            $query->where('channel', $request->query('channel'));
        }

        if ($request->filled('subscriber_id')) {
            $query->where('subscriber_id', (int) $request->query('subscriber_id'));
        }

        if ($request->filled('alert_rule_id')) {
            $query->where('alert_rule_id', (int) $request->query('alert_rule_id'));
        }

        $notifications = $this->paginate($query->orderByDesc('id'), $request);

        return NotificationResource::collection($notifications);
    }
}
