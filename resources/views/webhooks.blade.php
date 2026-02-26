@extends('layouts.tester')

@section('content')
<div class="grid" style="margin-top: 20px;">
    <div class="panel">
        <h2>Webhook Payload</h2>
        <label>Webhook Source Key</label>
        <input id="sourceKey" placeholder="e.g. github-main" />
        <label>Webhook Signing Secret (optional)</label>
        <input id="webhookSigningSecret" placeholder="secret" />
        <label>Payload (JSON)</label>
        <textarea id="webhookPayload">{
  "event_type": "push",
  "source": "github",
  "payload": {
    "ref": "refs/heads/main",
    "commits": [
      {
        "id": "abc123",
        "message": "Deploy hotfix for payment processing",
        "author": { "name": "Jane Dev", "email": "jane@example.com" },
        "timestamp": "2024-01-15T10:30:00Z"
      }
    ],
    "repository": { "full_name": "acme/payment-service" },
    "sender": { "login": "janedev", "email": "jane@example.com" }
  }
}</textarea>
        <button class="secondary" onclick="sendWebhook()">Send Webhook</button>
        <button class="ghost" onclick="randomizePayload()">Randomize Payload</button>
        <div class="note">If the source has a signing secret, the UI will compute `X-Signature` automatically.</div>
    </div>
    <div class="panel">
        <h2>Stripe / Monitoring Samples</h2>
        <button class="ghost" onclick="loadStripe()">Load Stripe Sample</button>
        <button class="ghost" onclick="loadMonitoring()">Load Monitoring Sample</button>
    </div>
</div>
@endsection

@section('scripts')
<script>
    async function sendWebhook() {
        const projectUuid = document.getElementById('projectUuid').value.trim();
        const sourceKey = document.getElementById('sourceKey').value.trim();
        const rawPayload = document.getElementById('webhookPayload').value;
        let payloadObj = null;

        try {
            payloadObj = JSON.parse(rawPayload);
        } catch (err) {
            return render({ error: 'Invalid JSON payload.' }, 'error');
        }

        const headers = { 'Content-Type': 'application/json' };
        const signingSecret = document.getElementById('webhookSigningSecret').value.trim();

        if (signingSecret) {
            const signature = await hmacSha256(signingSecret, rawPayload);
            headers['X-Signature'] = signature;
        }

        const res = await fetch(`${baseUrl()}/api/webhooks/${projectUuid}/${sourceKey}`, {
            method: 'POST',
            headers,
            body: JSON.stringify(payloadObj),
        });

        render(await res.json(), 'webhook');
    }

    function loadStripe() {
        document.getElementById('webhookPayload').value = JSON.stringify({
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
        }, null, 2);
    }

    function loadMonitoring() {
        document.getElementById('webhookPayload').value = JSON.stringify({
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
        }, null, 2);
    }

    function randomizePayload() {
        try {
            const raw = document.getElementById('webhookPayload').value;
            const payload = JSON.parse(raw);
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
            document.getElementById('webhookPayload').value = JSON.stringify(payload, null, 2);
        } catch (err) {
            render({ error: 'Invalid JSON payload.' }, 'error');
        }
    }
</script>
@endsection
