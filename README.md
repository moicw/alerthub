# AlertHub

Multi-tenant alert management platform built on Laravel.

## Setup

1. Install PHP 8.2+ and Composer.
2. Install dependencies and set up env:

```bash
composer install
cp .env.example .env
php artisan key:generate
```

3. Run migrations and seed sample data:

```bash
php artisan migrate
php artisan db:seed
```

4. Run the queue worker (for webhook processing and notifications):

```bash
php artisan queue:work
```

## Architecture Overview

- **Multi-tenant isolation** via `ResolveOrganization` middleware (Bearer token).
- **Chain of Responsibility pipeline** for webhook processing:
  - Deduplication
  - Validation
  - Subscriber match
  - Rule evaluation
  - Notification dispatch
- **Event-driven side effects** on notification creation:
  - Update subscriber stats
  - Escalation check
  - Metrics tracking
- **Legacy integration**: AlertMetrics provider, subscriber resolver, engagement scoring, digest scheduling.

## API Authentication

All tenant endpoints require:

```
Authorization: Bearer {org_api_token}
```

## REST API

### Projects

- `GET /api/projects`
- `POST /api/projects`
- `GET /api/projects/{id}`
- `PUT /api/projects/{id}`

Supports includes:

```
GET /api/projects/{id}?includes=subscribers,alert_rules
```

### Subscribers

- `GET /api/projects/{id}/subscribers`
- `POST /api/projects/{id}/subscribers`

### Notifications

- `GET /api/projects/{id}/notifications`

Filters: `status`, `channel`, `subscriber_id`, `alert_rule_id`

### Alert Rules

- `GET /api/projects/{id}/alert-rules`
- `POST /api/projects/{id}/alert-rules`

### Webhook Sources

- `POST /api/projects/{id}/webhook-sources`

## Pagination

All list endpoints support offset or cursor pagination:

- Offset: `?page=2&per_page=20`
- Cursor: `?cursor=...&per_page=20`

## Webhooks

```
POST /api/webhooks/{project_uuid}/{source_key}
```

If `signing_secret` is set on the webhook source, include:

```
X-Signature: {HMAC_SHA256(payload, signing_secret)}
```

The endpoint returns `202 Accepted` and enqueues processing.

## Digest Scheduling (Legacy Module)

Schedule digests via Artisan:

```bash
php artisan alerts:schedule-digests {projectId} {date?} {type?}
```

## Demo Flow

1. Create organization and projects via seeders.
2. Create a webhook source.
3. Create an alert rule.
4. POST a webhook payload to `/api/webhooks/{project_uuid}/{source_key}`.
5. Check notifications list for created entries.

## Tests

```bash
php artisan test
```

## Try It Live

You can test at:
https://alerthub.moicw.com/
