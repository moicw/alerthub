@extends('layouts.tester')

@section('content')
<div class="grid" style="margin-top: 20px;">
    <div class="panel">
        <h2>Step 1 — Projects</h2>
        <p class="note">List and create projects for the org.</p>
        <a class="ghost" href="/projects">Go to Projects</a>
    </div>
    <div class="panel">
        <h2>Step 2 — Subscribers</h2>
        <p class="note">Create subscribers and verify pagination.</p>
        <a class="ghost" href="/subscribers">Go to Subscribers</a>
    </div>
    <div class="panel">
        <h2>Step 3 — Alert Rules</h2>
        <p class="note">Create rules to match webhook events.</p>
        <a class="ghost" href="/alert-rules">Go to Alert Rules</a>
    </div>
    <div class="panel">
        <h2>Step 4 — Webhook Sources</h2>
        <p class="note">Register webhook sources for a project.</p>
        <a class="ghost" href="/webhook-sources">Go to Webhook Sources</a>
    </div>
    <div class="panel">
        <h2>Step 5 — Webhook Test</h2>
        <p class="note">Send a webhook to trigger pipeline.</p>
        <a class="ghost" href="/webhooks">Go to Webhook Test</a>
    </div>
    <div class="panel">
        <h2>Step 6 — Notifications</h2>
        <p class="note">Confirm notifications created by rules.</p>
        <a class="ghost" href="/notifications">Go to Notifications</a>
    </div>
</div>
@endsection
