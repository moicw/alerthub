@extends('layouts.tester')

@section('hideGlobalOutput', true)

@section('content')
<div class="row" style="margin-top: 20px;">
    <div class="panel">
        <h2>Steps 1–3</h2>
        <p class="note">Pick a project once, then run the steps in order.</p>
        <div class="card">
            <label>Project (auto‑loaded)</label>
            <select id="projectSelect"></select>
            <label>Webhook Source Key</label>
            <input id="flowSourceKey" value="github-main" />
            <label>Webhook Signing Secret (optional)</label>
            <input id="flowSigningSecret" placeholder="secret" />
            <label>Webhook Payload</label>
            <select id="payloadPreset">
                <option value="github">GitHub Push</option>
                <option value="stripe">Stripe Payment Failed</option>
                <option value="monitoring">Monitoring Alert</option>
            </select>
            <label>Auto-randomize payload (avoid dedupe)</label>
            <select id="flowRandomize">
                <option value="on" selected>On</option>
                <option value="off">Off</option>
            </select>
        </div>
        <div style="margin-top: 14px;">
            <button class="secondary" onclick="flowCreateWebhookSource()">Step 1: Create Webhook Source</button>
            <button class="secondary" onclick="flowCreateAlertRule()">Step 2: Create Alert Rule</button>
            <button class="secondary" onclick="flowSendWebhook()">Step 3: Send Webhook</button>
        </div>
        <div class="tag" style="margin-top: 12px;">Steps Output</div>
        <div id="stepsOutput" class="output"></div>
    </div>

    <div class="panel">
        <h2>Notifications</h2>
        <p class="note">Auto‑poll and inspect new notifications here.</p>
        <button class="ghost" id="flowPollBtn" onclick="flowTogglePoll()">Auto Poll: Off</button>
        <button class="secondary" onclick="flowListNotifications()">Refresh Now</button>
        <div class="tag" style="margin-top: 12px;">Notifications Output</div>
        <div id="notificationsOutput" class="output"></div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let flowPollTimer = null;

    function renderSteps(data, label = 'steps') {
        const timestamp = new Date().toISOString();
        document.getElementById('stepsOutput').textContent =
            `[${timestamp}] ${label}\n` + JSON.stringify(data, null, 2);
    }

    function renderNotifications(data, label = 'notifications') {
        const timestamp = new Date().toISOString();
        document.getElementById('notificationsOutput').textContent =
            `[${timestamp}] ${label}\n` + JSON.stringify(data, null, 2);
    }

    async function loadProjects() {
        const res = await fetch(`${baseUrl()}/api/projects`, { headers: authHeaders() });
        const data = await res.json();
        const select = document.getElementById('projectSelect');
        select.innerHTML = '';
        (data.data || data.projects || []).forEach(project => {
            const option = document.createElement('option');
            option.value = project.uuid;
            option.textContent = `${project.name} (${project.uuid})`;
            select.appendChild(option);
        });

        const saved = localStorage.getItem('alerthub.projectUuid');
        if (saved) {
            select.value = saved;
        }

        syncProjectUuid();
        select.addEventListener('change', syncProjectUuid);
    }

    function syncProjectUuid() {
        const projectUuid = document.getElementById('projectSelect').value;
        document.getElementById('projectUuid').value = projectUuid;
        localStorage.setItem('alerthub.projectUuid', projectUuid);
    }

    function currentProjectUuid() {
        return document.getElementById('projectSelect').value;
    }

    async function flowCreateWebhookSource() {
        const sourceKey = document.getElementById('flowSourceKey').value;
        const listRes = await fetch(`${baseUrl()}/api/projects/${currentProjectUuid()}/webhook-sources`, {
            headers: authHeaders(),
        });
        const listData = await listRes.json();
        const existing = (listData.data || []).find(item => item.source_key === sourceKey);
        if (existing) {
            return renderSteps(existing, 'webhook source exists');
        }

        const payload = {
            source_key: sourceKey,
            source_type: 'github',
            name: 'GitHub Main',
            signing_secret: document.getElementById('flowSigningSecret').value || null,
            is_active: true,
        };
        const res = await fetch(`${baseUrl()}/api/projects/${currentProjectUuid()}/webhook-sources`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', ...authHeaders() },
            body: JSON.stringify(payload),
        });
        renderSteps(await res.json(), 'create webhook source');
    }

    async function flowCreateAlertRule() {
        const payload = {
            name: 'GitHub Push Rule',
            source_type: 'github',
            event_type: 'push',
            conditions: {
                field: 'payload.sender.email',
                operator: '==',
                value: 'jane@example.com',
            },
            action: 'notify',
            priority: 'high',
            is_active: true,
        };

        const listRes = await fetch(`${baseUrl()}/api/projects/${currentProjectUuid()}/alert-rules`, {
            headers: authHeaders(),
        });
        const listData = await listRes.json();
        const existing = (listData.data || []).find(item =>
            item.source_type === payload.source_type &&
            item.event_type === payload.event_type &&
            JSON.stringify(item.conditions) === JSON.stringify(payload.conditions)
        );
        if (existing) {
            return renderSteps(existing, 'alert rule exists');
        }

        const res = await fetch(`${baseUrl()}/api/projects/${currentProjectUuid()}/alert-rules`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', ...authHeaders() },
            body: JSON.stringify(payload),
        });
        renderSteps(await res.json(), 'create alert rule');
    }

    async function flowSendWebhook() {
        const payload = buildPayload(document.getElementById('payloadPreset').value);
        if (document.getElementById('flowRandomize').value === 'on') {
            applyRandomizer(payload);
        }
        const rawPayload = JSON.stringify(payload);
        const headers = { 'Content-Type': 'application/json' };
        const signingSecret = document.getElementById('flowSigningSecret').value.trim();

        if (signingSecret) {
            headers['X-Signature'] = await hmacSha256(signingSecret, rawPayload);
        }

        const res = await fetch(`${baseUrl()}/api/webhooks/${currentProjectUuid()}/${document.getElementById('flowSourceKey').value}`, {
            method: 'POST',
            headers,
            body: rawPayload,
        });
        renderSteps(await res.json(), 'webhook');
    }

    function buildPayload(kind) {
        if (kind === 'stripe') {
            return {
                event_type: "payment_intent.payment_failed",
                source: "stripe",
                payload: {
                    id: "pi_3abc123",
                    amount: 5000,
                    currency: "usd",
                    customer: { id: "cus_xyz", email: "customer@example.com" },
                    last_payment_error: { code: "card_declined", message: "Your card was declined." },
                    metadata: { order_id: "ORD-12345" }
                }
            };
        }
        if (kind === 'monitoring') {
            return {
                event_type: "alert.triggered",
                source: "monitoring",
                payload: {
                    alert_id: "mon-789",
                    severity: "critical",
                    service: "api-gateway",
                    message: "Response time exceeded 5s threshold",
                    metric_value: 7.2,
                    threshold: 5.0,
                    triggered_at: "2024-01-15T10:30:00Z",
                    contact: { email: "ops-team@example.com", external_id: "ops-team" }
                }
            };
        }
        return {
            event_type: "push",
            source: "github",
            payload: {
                ref: "refs/heads/main",
                commits: [
                    {
                        id: "abc123",
                        message: "Deploy hotfix for payment processing",
                        author: { name: "Jane Dev", email: "jane@example.com" },
                        timestamp: "2024-01-15T10:30:00Z"
                    }
                ],
                repository: { full_name: "acme/payment-service" },
                sender: { login: "janedev", email: "jane@example.com" }
            }
        };
    }

    function applyRandomizer(payload) {
        const now = Date.now().toString(36);
        if (payload?.payload?.commits?.[0]) {
            payload.payload.commits[0].id = `rand-${now}`;
        }
        if (payload?.payload?.alert_id) {
            payload.payload.alert_id = `rand-${now}`;
        }
        if (payload?.payload?.id) {
            payload.payload.id = `rand-${now}`;
        }
    }

    async function flowListNotifications() {
        const res = await fetch(`${baseUrl()}/api/projects/${currentProjectUuid()}/notifications`, {
            headers: authHeaders(),
        });
        renderNotifications(await res.json(), 'notifications');
    }

    function flowTogglePoll() {
        const button = document.getElementById('flowPollBtn');
        if (flowPollTimer) {
            clearInterval(flowPollTimer);
            flowPollTimer = null;
            button.textContent = 'Auto Poll: Off';
            return;
        }

        flowListNotifications();
        flowPollTimer = setInterval(flowListNotifications, 4000);
        button.textContent = 'Auto Poll: On';
    }

    loadProjects();
</script>
@endsection
