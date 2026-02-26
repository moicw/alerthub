@extends('layouts.tester')

@section('content')
<div class="grid" style="margin-top: 20px;">
    <div class="panel">
        <h2>List Projects</h2>
        <button onclick="listProjects()">List Projects</button>
    </div>
    <div class="panel">
        <h2>Create Project</h2>
        <label>Name</label>
        <input id="projectName" placeholder="Acme Alerts" />
        <label>Description</label>
        <input id="projectDesc" placeholder="Payments monitoring" />
        <button class="secondary" onclick="createProject()">Create Project</button>
    </div>
</div>
@endsection

@section('scripts')
<script>
    async function listProjects() {
        const res = await fetch(`${baseUrl()}/api/projects`, { headers: authHeaders() });
        render(await res.json(), 'projects');
    }

    async function createProject() {
        const payload = {
            name: document.getElementById('projectName').value,
            description: document.getElementById('projectDesc').value,
        };
        const res = await fetch(`${baseUrl()}/api/projects`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', ...authHeaders() },
            body: JSON.stringify(payload),
        });
        render(await res.json(), 'create project');
    }
</script>
@endsection
