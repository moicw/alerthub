@extends('layouts.tester')

@section('content')
<div class="grid" style="margin-top: 20px;">
    <div class="panel">
        <h2>Create Webhook Source</h2>
        <label>Source Key</label>
        <input id="webhookSourceKey" value="github-main" />
        <label>Source Type</label>
        <select id="webhookSourceType">
            <option value="github">github</option>
            <option value="stripe">stripe</option>
            <option value="monitoring">monitoring</option>
            <option value="custom">custom</option>
        </select>
        <label>Name</label>
        <input id="webhookSourceName" value="GitHub Main" />
        <label>Signing Secret (optional)</label>
        <input id="webhookSigningSecretLocal" placeholder="secret" />
        <div class="note">Use the same Source Key on the Webhook Test page.</div>
        <button class="secondary" onclick="createWebhookSource()">Create Webhook Source</button>
    </div>
    <div class="panel">
        <h2>Notes</h2>
        <p class="note">Use the same source key in the Webhook Test page.</p>
    </div>
</div>
@endsection

@section('scripts')
<script>
    async function createWebhookSource() {
        const projectUuid = document.getElementById('projectUuid').value.trim();
        const payload = {
            source_key: document.getElementById('webhookSourceKey').value,
            source_type: document.getElementById('webhookSourceType').value,
            name: document.getElementById('webhookSourceName').value,
            signing_secret: document.getElementById('webhookSigningSecretLocal').value || null,
            is_active: true,
        };
        const res = await fetch(`${baseUrl()}/api/projects/${projectUuid}/webhook-sources`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', ...authHeaders() },
            body: JSON.stringify(payload),
        });
        render(await res.json(), 'create webhook source');
    }
</script>
@endsection
