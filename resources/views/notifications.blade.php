@extends('layouts.tester')

@section('content')
<div class="grid" style="margin-top: 20px;">
    <div class="panel">
        <h2>List Notifications</h2>
        <button onclick="listNotifications()">List Notifications</button>
        <div class="note">Use filters by editing the function if needed.</div>
    </div>
    <div class="panel">
        <h2>Quick Filters</h2>
        <label>Status</label>
        <input id="notifStatus" placeholder="pending/sent/failed/escalated" />
        <label>Channel</label>
        <input id="notifChannel" placeholder="email/webhook" />
        <button class="secondary" onclick="listNotificationsWithFilters()">List With Filters</button>
        <hr style="border-color: var(--border); margin: 12px 0;">
        <button class="ghost" id="autoPollBtn" onclick="toggleAutoPoll()">Auto Poll: Off</button>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let autoPollTimer = null;

    async function listNotifications(params = {}) {
        const projectUuid = document.getElementById('projectUuid').value.trim();
        const query = new URLSearchParams(params).toString();
        const url = `${baseUrl()}/api/projects/${projectUuid}/notifications` + (query ? `?${query}` : '');
        const res = await fetch(url, { headers: authHeaders() });
        render(await res.json(), 'notifications');
    }

    async function listNotificationsWithFilters() {
        const status = document.getElementById('notifStatus').value.trim();
        const channel = document.getElementById('notifChannel').value.trim();
        const params = {};
        if (status) params.status = status;
        if (channel) params.channel = channel;
        return listNotifications(params);
    }

    function toggleAutoPoll() {
        const button = document.getElementById('autoPollBtn');
        if (autoPollTimer) {
            clearInterval(autoPollTimer);
            autoPollTimer = null;
            button.textContent = 'Auto Poll: Off';
            return;
        }

        autoPollTimer = setInterval(() => {
            listNotificationsWithFilters();
        }, 4000);

        button.textContent = 'Auto Poll: On';
    }
</script>
@endsection
