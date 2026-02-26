@extends('layouts.tester')

@section('content')
<div class="grid" style="margin-top: 20px;">
    <div class="panel">
        <h2>List Subscribers</h2>
        <button onclick="listSubscribers()">List Subscribers</button>
    </div>
    <div class="panel">
        <h2>Create Subscriber</h2>
        <label>Email</label>
        <input id="subscriberEmail" placeholder="ops@example.com" />
        <label>External ID</label>
        <input id="subscriberExternal" placeholder="ops-team" />
        <label>Name</label>
        <input id="subscriberName" placeholder="Ops Team" />
        <button class="secondary" onclick="createSubscriber()">Create Subscriber</button>
    </div>
</div>
@endsection

@section('scripts')
<script>
    async function listSubscribers() {
        const projectUuid = document.getElementById('projectUuid').value.trim();
        const res = await fetch(`${baseUrl()}/api/projects/${projectUuid}/subscribers`, {
            headers: authHeaders(),
        });
        render(await res.json(), 'subscribers');
    }

    async function createSubscriber() {
        const projectUuid = document.getElementById('projectUuid').value.trim();
        const payload = {
            email: document.getElementById('subscriberEmail').value || null,
            external_id: document.getElementById('subscriberExternal').value || null,
            name: document.getElementById('subscriberName').value || null,
        };
        const res = await fetch(`${baseUrl()}/api/projects/${projectUuid}/subscribers`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', ...authHeaders() },
            body: JSON.stringify(payload),
        });
        render(await res.json(), 'create subscriber');
    }
</script>
@endsection
