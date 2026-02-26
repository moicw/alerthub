@extends('layouts.tester')

@section('content')
<div class="grid" style="margin-top: 20px;">
    <div class="panel">
        <h2>List Alert Rules</h2>
        <button onclick="listAlertRules()">List Alert Rules</button>
    </div>
    <div class="panel">
        <h2>Create Alert Rule</h2>
        <label>Name</label>
        <input id="ruleName" value="GitHub Push Rule" />
        <div class="row">
            <div>
                <label>Source Type</label>
                <select id="ruleSource">
                    <option value="github">github</option>
                    <option value="stripe">stripe</option>
                    <option value="monitoring">monitoring</option>
                    <option value="custom">custom</option>
                </select>
            </div>
            <div>
                <label>Event Type</label>
                <input id="ruleEventType" value="push" />
            </div>
        </div>
        <label>Conditions (JSON)</label>
        <textarea id="ruleConditions">{"field":"payload.sender.email","operator":"==","value":"jane@example.com"}</textarea>
        <div class="row">
            <div>
                <label>Action</label>
                <select id="ruleAction">
                    <option value="notify">notify</option>
                    <option value="escalate">escalate</option>
                    <option value="digest">digest</option>
                </select>
            </div>
            <div>
                <label>Priority</label>
                <select id="rulePriority">
                    <option value="low">low</option>
                    <option value="medium">medium</option>
                    <option value="high" selected>high</option>
                    <option value="critical">critical</option>
                </select>
            </div>
        </div>
        <button class="secondary" onclick="createAlertRule()">Create Alert Rule</button>
    </div>
</div>
@endsection

@section('scripts')
<script>
    async function listAlertRules() {
        const projectUuid = document.getElementById('projectUuid').value.trim();
        const res = await fetch(`${baseUrl()}/api/projects/${projectUuid}/alert-rules`, {
            headers: authHeaders(),
        });
        render(await res.json(), 'alert rules');
    }

    async function createAlertRule() {
        const projectUuid = document.getElementById('projectUuid').value.trim();
        let conditions = null;
        try {
            conditions = JSON.parse(document.getElementById('ruleConditions').value || '{}');
        } catch (err) {
            return render({ error: 'Invalid JSON in conditions.' }, 'error');
        }
        const payload = {
            name: document.getElementById('ruleName').value,
            source_type: document.getElementById('ruleSource').value,
            event_type: document.getElementById('ruleEventType').value,
            conditions: conditions,
            action: document.getElementById('ruleAction').value,
            priority: document.getElementById('rulePriority').value,
            is_active: true,
        };
        const res = await fetch(`${baseUrl()}/api/projects/${projectUuid}/alert-rules`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', ...authHeaders() },
            body: JSON.stringify(payload),
        });
        render(await res.json(), 'create alert rule');
    }
</script>
@endsection
