<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessWebhookEvent;
use App\Models\Project;
use App\Models\WebhookSource;
use Illuminate\Http\Request;

class WebhookController extends Controller
{
    public function handle(Request $request, string $projectUuid, string $sourceKey)
    {
        $project = Project::where('uuid', $projectUuid)->firstOrFail();

        $source = WebhookSource::where('project_id', $project->id)
            ->where('source_key', $sourceKey)
            ->where('is_active', true)
            ->firstOrFail();

        if ($source->signing_secret) {
            $signature = (string) $request->header('X-Signature');
            $expected = hash_hmac('sha256', $request->getContent(), $source->signing_secret);

            if (!$signature || !hash_equals($expected, $signature)) {
                return response()->json(['message' => 'Invalid signature'], 401);
            }
        }

        ProcessWebhookEvent::dispatch($project->id, $source->id, $request->all());

        return response()->json(['status' => 'accepted'], 202);
    }
}
