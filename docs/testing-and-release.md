# Simple Shop testing and release guide

Use an isolated environment to establish behavior before upgrading or migrating this production store. The new container/test setup provides isolation; the legacy application `.env` must still be treated as potentially live. The release-baseline example documents the baseline preparation; it was not executed during the initial analysis. The later publication status is recorded below.

## What was verified during analysis

On October 6, 2026, America/Phoenix:

- PHP syntax: 149 files passed, covering application, routes, configuration, migrations/factories/seeders, bootstrap source, and tests.
- Composer manifest validation: passed; warned about the cart wildcard. Composer 2.7.7 also emitted deprecations under PHP 8.4.22.
- PHPUnit: the isolated `tests/Unit/ExampleTest.php` passed with one assertion. This test uses PHPUnit directly and does not boot Laravel.
- Frontend: the current Vite build passed in a temporary source copy using existing installed dependencies and no application `.env`.
- Dependency advisory checks: attempted, but Composer/npm could not reach registries. No clean-audit claim is justified.

No feature tests, migrations, seeding, live database queries, payment requests, uploads, emails, backups, deployment, or Git release operations were performed.

## Environment implementation checks

Run the new safety tests with:

```bash
scripts/dev exec --user www-data app vendor/bin/phpunit --filter 'NavigationSearchTest|NonProductionSafetyTest'
scripts/dev exec --user www-data app php artisan schedule:list
scripts/dev exec --user www-data app php scripts/inspect-copy.php
```

The first command forces a SQLite in-memory database independently of `.env.docker` and container process variables. The second must show no scheduled tasks while sandbox mode is active. The third is a read-only restored-copy inventory, restricted to the two named sandbox databases; it reports counts and catalog paths, never customer records or passwords.

The targeted safety suite passes (six tests, 13 assertions). The navigation-search regression suite adds ten cases; running both passes 16 tests with 54 assertions. Search verification must assert matching product IDs/cards for Cyrillic and Latin queries; an HTTP 200 alone missed the original environment-driver regression. The [search verification record](environments.md#cyrillic-and-latin-navigation-search) includes the exact query pairs, snapshot counts, browser button/Enter checks, and driver limitations. Composer installation from the existing lockfile and the Node 24 frontend build pass. The npm install reported 13 advisories (two moderate, ten high, one critical); dependencies were deliberately not changed during environment setup. Do not interpret successful builds as security clearance. See [Environments](environments.md) for the restore, deployment, and smoke-check record. Full commerce/auth regression coverage and payment-provider acceptance remain pending.

## Current acceptance checks and remaining work

The staging storefront is public at the owner's request. Verify `/` and `/admin/login` return 200 without an HTTP authentication challenge, and `/admin/product` redirects an anonymous visitor to `/admin/login`. The management route is singular `product`. `/update`, `/admin/register`, and payment callbacks remain blocked in sandbox mode; `/bank/ok` was checked with POST and returned 404. The HTTP-authentication removal did not enable real payment submission or scheduled tasks.

Existing evidence covers restored-copy row counts/media hashes, the locked dependency install and asset build, navigation search results, environment safety, and manual guest/admin smoke checks. It does not establish a passing full legacy suite, correct bank callback verification, or complete staff authorization.

The [next implementation checkpoint](modernization-plan.md#next-implementation-checkpoint) defines the work to add synthetic catalog/cart/guest-order fixtures and CI before the first framework upgrade. Preserve each verified behavior with assertions on product IDs, cart contents, order data, redirects, or payment suppression as applicable; a successful page response alone is insufficient. Documentation-only updates do not require rerunning unchanged application tests; the recorded test counts describe the last executed checks.

## Safe local and CI environment

1. Use a disposable checkout/container with its own environment file and empty bootstrap caches. Do not copy production `.env`, cached configuration, storage logs, or credentials into it.
2. Choose a database with credentials restricted to that disposable database. SQLite may support fast tests, but use the same MariaDB/MySQL major as production for migrations, JSON, indexing, money backfills, and locking/concurrency tests.
3. Configure testing explicitly: application environment, separate application key, database connection/name, array/test mail, fake/local storage, test queue behavior, and disabled/fake Scout. Force these settings so inherited shell variables cannot redirect tests.
4. Add a bootstrap guard before any `RefreshDatabase`/migration operation. Assert effective environment, allowed connection/host/database, and absence of cached production configuration. Fail closed for unknown databases. `APP_ENV=testing` alone is not a safety check.
5. Block outbound production integration access. Fake the bank adapter and mail/search/storage clients; disable scheduled jobs and external queue consumers. The existing bank form URL and credentials are hardcoded, so environment overrides alone cannot make today's payment path safe to click.
6. Create dedicated synthetic fixtures/factories for products, categories, media, pages, carts, orders, and staff. Current product/order factories are empty. Do not reuse `DatabaseSeeder` or `OCSeeder` for routine testing.
7. Install dependencies from lockfiles, build assets, and run the agreed suite. Add CI with the same isolation, runtime versions, lint/build steps, commerce tests, and advisory checks. Record tool/package versions with results.

The initial `phpunit.xml` omitted DB isolation. This has now been replaced by forced test settings and an effective-database guard in `CreatesApplication`; keep both when changing test infrastructure. Legacy authentication tests still use routes that do not match the `/admin` prefix, so the full suite is not an established passing baseline. The parent `repair.sh` also clears queues and caches; it is not a setup script.

## Regression coverage

### Catalog and content

- Browse home, category, product, page, and search using Macedonian names and existing slugs.
- Verify current stock/visibility behavior before intentionally changing it; hidden/unpublished products must not be purchasable later.
- Missing/deleted records return controlled responses. Admin lists include out-of-stock products.
- Preserve primary/gallery images, nested paths, orientation, transparency, and representative imported HTML.
- Test allowed editor markup and reject script, event-handler, unsafe URL, and textarea-breakout payloads.

### Cart and checkout

- Guest add/remove, empty cart, deleted/inactive/out-of-stock product, expiration, and session continuity.
- One-unit behavior during the upgrade; multiple quantities only after the product rule is decided.
- Zero, varied, and invalid discounts; rounding boundaries; matching display/cart/order/provider totals.
- Address correction and same-total item changes update the correct pending checkout.
- Paid orders are immutable and a subsequent purchase creates a new order.
- New items added during a bank visit survive completion of the previous checkout.

### Payment and stock

- Outbound fields/checksum against bank-approved fixtures, including Cyrillic values, optional fields, character/byte length rules, exact amount conversion, and callback URLs.
- Valid paid/failure/cancel cases; invalid signature, wrong merchant/currency/amount, missing order, malformed payload, and unknown attempt.
- Duplicate and concurrent callbacks, delayed success after failure, failure after success, and old-attempt callbacks.
- Two buyers competing for the last unit; multiple line items; partial-processing exceptions roll back atomically.
- Reservation expiration and late successful payment require explicit reconciliation.
- Missing visitor session on provider POST must not corrupt payment handling or expose order details.
- Network/provider failure permits a safe retry without an accidental second paid order.
- Logs contain correlation identifiers and sanitized status, without secrets or full customer payloads.

These are requirements for the new behavior. The current callback code is expected to fail several cases; passing syntax/build checks does not satisfy them.

### Admin and media

- Guests/customers cannot manage products, stock, pages, uploads, orders, or other users; staff can perform permitted actions.
- CSRF and request validation cover mutations. Public staff registration/import are unavailable.
- Upload content/type/size/dimensions are checked; filenames and paths cannot escape storage or execute code.
- Select/reuse/reorder a cover/gallery and insert media into the editor. Referenced media deletion is blocked or explicitly resolved.
- Video selection/playback and unsupported-file errors work within agreed limits.
- Fresh upload, replacement, missing source, and derivative regeneration behave consistently.

### Operations and migration

- Fresh install with dedicated synthetic fixtures and upgrade of a restored production copy both succeed.
- Search rebuild/update, sitemap generation, queue processing, backup scheduling/monitoring, and restore work independently of OpenCart.
- Apply schema changes to representative existing records, not just empty tables.
- Compare record counts, mappings, orphan/duplicate reports, and media checksums. Re-run migration to prove idempotency and recovery from interruption.
- After cutover rehearsal, remove access to old OpenCart DB/files and run browsing, authoring, checkout, and backup checks again.

## Later production-copy intake

Obtain a current Laravel schema/data dump and media snapshot, plus the OpenCart snapshot needed to reconcile source content. Record capture time, source versions, migration history, and snapshot consistency. Store them outside Git and the web root with restricted access; agree retention and deletion.

Sanitize customer names, addresses, phones, emails, comments, credentials, tokens, and provider identifiers before general development use. Preserve referential relationships and meaningful payment states while replacing values. Restrict access to any unsanitized copy needed for final reconciliation. Disable real integrations before restoring/booting.

Inventory differences before changing data:

- Tables, columns, indexes, constraints, collation, engine, migration status, and source prefixes/languages.
- Counts, duplicate slugs/relationship pairs, orphans, nulls, negative stock, active/deleted-at values, and invalid JSON.
- Products/covers/gallery/HTML links with missing or mismatched files.
- Order/payment totals and references, including incomplete orders whose actual payment outcome is unknown.
- Stock and native fields that differ from OpenCart. Document the winner for each conflicting field.

Build a reconciliation report with explicit exceptions and owner decisions. Never rerun the old importer to make differences disappear.

## Version 1.0 baseline and branch

**Publication status:** after the owner requested a new branch and push, remote `develop` was verified at the inspected commit; the proposed branch and tag names were absent remotely. Annotated tag `1.0` and branch `codex/refactor-simple-store` were created from that source baseline. Production deployment comparison remains pending and is explicitly excluded from the tag's assurance. The following procedure remains a historical reference; do not recreate or move the tag.

The intended tag name is exactly `1.0`, matching the request. The inspected candidate is:

```text
93d6e04d9efb3a905097c1973edad74813549e34
develop: Feature/add media and backup (#4)
```

Before creating it, refresh/check remote refs, inspect any existing local/remote `1.0`, compare deployed code, and preserve all uncommitted documentation/user work. If `develop` advanced or production differs, explicitly identify the baseline instead of tagging an arbitrary latest commit. Do not overwrite an existing tag.

Once that exact baseline is confirmed, the proposed operations are:

```bash
git tag -a 1.0 93d6e04d9efb3a905097c1973edad74813549e34 -m 'Version 1.0 before modernization'
git switch -c codex/refactor-simple-store 1.0
```

Commit this documentation on the new branch after the baseline, using explicit file selection. Verify the tag's peeled commit and new branch base. Publish tag/branch as part of the authorized implementation/release work, checking for conflicts first. A tag records source only: capture matching dependencies, built assets, runtime/configuration inventory, and separately protected database/media backups for a reproducible release.

The historical tag includes known legacy risks. Keep it immutable as history; use a verified safe prior deployment for routine rollback, retaining containment for known exposed endpoints and rotated credentials.

## Deployment gates

Before a production upgrade:

1. Resolve or explicitly disposition the critical findings in the analysis. Preserve guest checkout and complete bank sandbox acceptance for payment-related changes.
2. Restore a recent backup in staging and rehearse forward migrations there. Verify data/media reports and the intended fallback.
3. Build a versioned release from locked dependencies on the selected runtimes. Verify production PHP extensions, web PHP/CLI consistency, writable disks, server document root, asset manifest, and worker/scheduler configuration.
4. Back up production DB, original media, and configuration securely. Confirm restore access and define the maintenance/ordering window. Handle in-flight payments and provider retries throughout the switch; do not casually block callback delivery.
5. Apply only reviewed forward migrations, activate release/configuration/assets, and restart long-running workers as required. Rebuild search/sitemap when the change requires it.
6. Smoke-test public pages, admin login/roles, image delivery, guest cart, and the permitted payment verification process. Never make an unapproved real payment as a deployment check.
7. Monitor application errors, payment outcomes/retries, stock anomalies, missing media, queue failures, and backups. Record who owns response and the agreed observation period.

## Rollback and recovery

Prefer additive schema changes and compatibility reads/writes during transition so the previous verified application can run. Keep originals and URL mappings until cutover acceptance. Do not combine dropping legacy columns/files with initial migration.

Rollback the application/configuration/assets to the prior safe release only after checking schema/session compatibility and credential changes. Maintain provider callback processing and reconcile in-flight attempts; a bank-confirmed charge does not disappear when code is rolled back.

After new orders or payments have been accepted, restoring an old database would lose business events. Freeze affected writes, preserve the current database and provider event evidence, reconcile transactions/stock, and use a forward repair or carefully reconciled restore. Do not use a blind database rollback. Never assume a migration `down` method is safe recovery for business data.

For OpenCart cutover recovery, retain the old file tree and mappings, reverse only the tested read-path switch if needed, and preserve all Laravel orders/new authoring. Resume OpenCart writes only under an explicit reconciliation plan to avoid two competing sources of truth.
