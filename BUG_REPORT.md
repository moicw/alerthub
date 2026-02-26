# AlertHub Bug Report

## AH-101 — Metrics dashboard shows wrong numbers

| Field | Description |
| --- | --- |
| **Ticket** | AH-101 |
| **Root Cause** | Metrics queries and cache keys were global by date only, so counts merged notifications from all projects and cached the mixed result. Files: `/Users/skypie/Documents/Projects/alerthub/packages/alert-metrics/src/MetricsAggregator.php`. |
| **Fix Applied** | Scoped metrics queries by `project_id` and added `project_id` to cache keys; updated method signatures and call sites to pass project id. |
| **Regression Test** | `tests/Unit/MetricsAggregatorTest.php` verifies daily counts are correct per project. |
| **Prevention** | Standardize tenant scoping in metrics services and include tenant identifiers in cache keys; add unit tests for multi-tenant metrics. |

## AH-102 — Webhooks create duplicate subscribers or fail silently

| Field | Description |
| --- | --- |
| **Ticket** | AH-102 |
| **Root Cause** | Subscriber lookup and lock key only used email. Monitoring payloads often have no email, so lookups missed by `external_id`; the lock key collapsed to a shared key and `resolve()` could return null on lock contention. File: `/Users/skypie/Documents/Projects/alerthub/packages/alert-metrics/src/SubscriberResolver.php`. |
| **Fix Applied** | Lookup now checks email **or** external_id; lock key includes project and identifier; lock acquisition uses blocking to avoid returning null; fallback lookup after lock failure. |
| **Regression Test** | `tests/Unit/SubscriberResolverTest.php` ensures resolving by external_id returns the same subscriber. |
| **Prevention** | Treat external identifiers as first-class keys; add concurrency tests and enforce unique constraints that handle null email cases. |

## AH-103 — Digests never scheduled with delivery window

| Field | Description |
| --- | --- |
| **Ticket** | AH-103 |
| **Root Cause** | Listener order was incorrect: `CalculateDigestWindow` ran before `GenerateDigestId`, so `referenceId` was null and window calculation skipped. File: `/Users/skypie/Documents/Projects/alerthub/packages/alert-metrics/src/MetricsServiceProvider.php`. |
| **Fix Applied** | Reordered listeners to run `GenerateDigestId` before `CalculateDigestWindow`. |
| **Regression Test** | `tests/Unit/DigestScheduledListenerTest.php` asserts `scheduledWindow` is set after firing the event. |
| **Prevention** | Document listener dependencies and add explicit tests for event mutation order. |

## AH-104 — Only first digest job runs per subscriber

| Field | Description |
| --- | --- |
| **Ticket** | AH-104 |
| **Root Cause** | `ProcessAlertDigest::uniqueId()` only used `subscriberId` + `date`, so multiple digests within the `uniqueFor` window were deduplicated. File: `/Users/skypie/Documents/Projects/alerthub/packages/alert-metrics/src/ProcessAlertDigest.php`. |
| **Fix Applied** | Added alert ID hash (and digest type) to unique ID so distinct digests do not collide. |
| **Regression Test** | `tests/Unit/ProcessAlertDigestTest.php` asserts unique ID changes for different alert sets. |
| **Prevention** | Include payload identifiers in unique keys for high-frequency jobs and document uniqueness semantics. |

## AH-105 — Engagement scores inconsistent between API and digest

| Field | Description |
| --- | --- |
| **Ticket** | AH-105 |
| **Root Cause** | Engagement cache key used only subscriber ID, so digest scoring overwrote realtime scoring. File: `/Users/skypie/Documents/Projects/alerthub/packages/alert-metrics/src/EngagementScorer.php`. |
| **Fix Applied** | Cache key now includes `context` (realtime vs digest). |
| **Regression Test** | `tests/Unit/EngagementScorerTest.php` verifies both context-specific keys are cached. |
| **Prevention** | Add context to cache keys when business logic depends on request mode; add tests for cache separation. |
