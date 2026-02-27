<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>AlertHub Test Console</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:wght@400;600;700&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #0e0f12;
            --bg-2: #151820;
            --panel: #1c2130;
            --panel-2: #23283a;
            --accent: #ff9f1c;
            --accent-2: #5bc0eb;
            --text: #f6f7fb;
            --muted: #b3b9c6;
            --border: #2e3448;
            --shadow: 0 10px 30px rgba(0, 0, 0, 0.35);
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: "Space Grotesk", system-ui, -apple-system, sans-serif;
            color: var(--text);
            background: radial-gradient(1200px 800px at 10% -10%, #1b1f2a 0%, transparent 60%),
                        radial-gradient(1200px 800px at 110% 10%, #1a1226 0%, transparent 50%),
                        var(--bg);
            min-height: 100vh;
        }

        header {
            padding: 28px 24px 16px;
            border-bottom: 1px solid var(--border);
            background: linear-gradient(135deg, rgba(255, 159, 28, 0.12), rgba(91, 192, 235, 0.08));
        }

        h1 {
            font-family: "Fraunces", serif;
            font-weight: 700;
            font-size: 30px;
            margin: 0 0 6px;
        }

        p { margin: 0; color: var(--muted); }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 24px;
        }

        nav {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 16px;
        }

        nav a {
            text-decoration: none;
            color: var(--text);
            border: 1px solid var(--border);
            padding: 8px 12px;
            border-radius: 999px;
            font-size: 13px;
            background: rgba(15, 18, 26, 0.6);
        }

        nav a.active {
            border-color: var(--accent);
            color: var(--accent);
        }

        .panel {
            background: linear-gradient(180deg, var(--panel), var(--panel-2));
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 18px;
            box-shadow: var(--shadow);
        }

        .grid {
            display: grid;
            gap: 20px;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        }

        label {
            display: block;
            font-size: 12px;
            color: var(--muted);
            margin-bottom: 6px;
            letter-spacing: 0.4px;
        }

        input, textarea, select {
            width: 100%;
            background: #0f121a;
            color: var(--text);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 10px 12px;
            margin-bottom: 10px;
            font-family: inherit;
            font-size: 14px;
        }

        textarea { min-height: 120px; resize: vertical; }

        button {
            background: var(--accent);
            color: #121212;
            border: none;
            border-radius: 10px;
            padding: 10px 14px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.15s ease, opacity 0.15s ease;
        }

        button.secondary { background: var(--accent-2); }

        button.ghost {
            background: transparent;
            color: var(--text);
            border: 1px solid var(--border);
        }

        button:hover { transform: translateY(-1px); opacity: 0.92; }

        .row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        .output {
            background: #0b0d13;
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 14px;
            min-height: 160px;
            overflow: auto;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", monospace;
            font-size: 12px;
            line-height: 1.5;
        }

        .tag {
            display: inline-block;
            font-size: 11px;
            padding: 4px 8px;
            border-radius: 999px;
            background: rgba(255, 159, 28, 0.15);
            color: var(--accent);
            border: 1px solid rgba(255, 159, 28, 0.4);
            margin-bottom: 8px;
        }

        .note {
            font-size: 12px;
            color: var(--muted);
            margin-top: 8px;
        }

        .card {
            border: 1px dashed var(--border);
            border-radius: 12px;
            padding: 12px;
            background: rgba(15, 18, 26, 0.4);
        }

        @media (max-width: 720px) {
            .row { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <h1>AlertHub Test Console</h1>
           
            <nav>
                <a href="/" class="{{ request()->is('/') ? 'active' : '' }}">Flow</a>
                <a href="/projects" class="{{ request()->is('projects') ? 'active' : '' }}">Projects</a>
                <a href="/subscribers" class="{{ request()->is('subscribers') ? 'active' : '' }}">Subscribers</a>
                <a href="/alert-rules" class="{{ request()->is('alert-rules') ? 'active' : '' }}">Alert Rules</a>
                <a href="/webhook-sources" class="{{ request()->is('webhook-sources') ? 'active' : '' }}">Webhook Sources</a>
                <a href="/notifications" class="{{ request()->is('notifications') ? 'active' : '' }}">Notifications</a>
                <a href="/webhooks" class="{{ request()->is('webhooks') ? 'active' : '' }}">Webhook Test</a>
            </nav>
        </div>
    </header>

    <main class="container">
        <div class="panel">
            <div class="tag">Global Settings</div>
            <div class="row">
                <div>
                    <label for="baseUrl">Base URL</label>
                    <input id="baseUrl" value="http://localhost:8000" />
                </div>
                <div>
                    <label for="projectUuid">Project UUID</label>
                    <input id="projectUuid" placeholder="Project UUID" />
                </div>
                <div>
                    <label>Bearer Token</label>
                    <input value="EdInnPSjj1PymwHyTcgTSHusKiOqF7hE5P0sC5An" readonly />
                </div>
            </div>
        </div>

        @yield('content')

        @unless (View::hasSection('hideGlobalOutput'))
            <div class="panel" style="margin-top: 20px;">
                <div class="tag">Output</div>
                <div id="output" class="output"></div>
            </div>
        @endunless
    </main>

    <script>
        const output = document.getElementById('output');
        const AUTH_TOKEN = 'EdInnPSjj1PymwHyTcgTSHusKiOqF7hE5P0sC5An';

        function render(data, label = 'response') {
            const timestamp = new Date().toISOString();
            output.textContent = `[${timestamp}] ${label}\n` + JSON.stringify(data, null, 2);
        }

        function baseUrl() {
            return document.getElementById('baseUrl').value.replace(/\/$/, '');
        }

        function authHeaders() {
            return AUTH_TOKEN ? { Authorization: `Bearer ${AUTH_TOKEN}` } : {};
        }

        async function hmacSha256(secret, message) {
            const enc = new TextEncoder();
            const key = await crypto.subtle.importKey(
                'raw',
                enc.encode(secret),
                { name: 'HMAC', hash: 'SHA-256' },
                false,
                ['sign']
            );
            const signature = await crypto.subtle.sign('HMAC', key, enc.encode(message));
            return Array.from(new Uint8Array(signature))
                .map(b => b.toString(16).padStart(2, '0'))
                .join('');
        }
    </script>
    @yield('scripts')
</body>
</html>
