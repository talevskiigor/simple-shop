# Simple Shop modernization plan

> **October 7 modernization update:** Laravel 13, native administrator/content/media management, server-sanitized visual editing, resizer repair and OpenCart retirement are implemented. The earlier baseline/next-step sections below are historical. Current status, executed tests, cleanup results and deployment evidence are authoritative in [implementation-log.md](implementation-log.md) and [admin-and-media.md](admin-and-media.md). Payment-provider acceptance and production rollout remain separate.

Upgrade the existing Laravel application, complete native store administration, and retire OpenCart after a verified data/media cutover. Preserve guest checkout and the functioning payment integration throughout. This is the implementation sequence; current progress is recorded below.

**Current progress:** the environment and test/CI checkpoints, Laravel 10 → 11 → 12 → 13, administrator-only management, WYSIWYG/media authoring, resizer repair, and OpenCart retirement are implemented, committed/pushed and deployed to staging. Both media inventories are reconciled and privately recoverable. Tag `1.0` is unchanged. Current evidence is in [the implementation log](implementation-log.md).

## Next implementation checkpoint

The domain cutover and production promotion are complete under the owner's authorization. Current live behavior and release evidence belong in [the implementation log](implementation-log.md). No further test data or test payments may be created on live, including its old staging alias.

Future work, after the current production cleanup:

1. Provision an independent staging deployment with separate database, media and credentials before any remote acceptance testing; local remains available now. Confirm that staging cannot access live storage or payment/email configuration by accident.
2. Recover or replace the ten missing originals while preserving intended product imagery and URLs.
3. Keep merchant-portal reconciliation for real payments; coordinate credential rotation and any future authenticated provider status integration. Exercise payment/stock-concurrency scenarios only in isolation.
4. Configure independent off-host backup storage and rehearse restores away from live.
5. Plan longer-lived anonymous carts and optional customer/social features separately.

The phase design below is the historical roadmap. Read it with the latest production rules and implementation log rather than repeating completed steps or testing on live.

## Scope and architecture decisions

- Keep one Laravel application, Blade, and Bootstrap. Retain the existing public URLs and Macedonian content. Avoid a storefront rewrite or a general-purpose page builder.
- Treat Laravel as the final source of truth for products, categories, stock, pages, and media. During transition, explicitly assign ownership of each field; routine imports must not overwrite sales or native edits.
- Introduce focused application services for pricing, checkout, payment processing, media, and the one-time import. Controllers should validate/authorize and delegate; no broad repository abstraction is required.
- Keep catalog and content administration separate from customer identity. A small explicit staff role is sufficient initially; do not introduce a permissions platform without a need.
- Use existing media/page tables as a starting point. A new media package must justify its migration cost rather than creating two competing libraries.
- Preserve existing behavior with characterization tests, while separately identifying unsafe behavior that must change before release.

## Phase 0 Baseline and safe development

**Purpose:** preserve the current application and make further work testable without live effects.

1. Baseline source/backup comparison is recorded; current production deployment and runtime parity still need verification. The immutable baseline is `93d6e04d9efb3a905097c1973edad74813549e34`.
2. **Completed:** annotated tag `1.0` and branch `codex/refactor-simple-store` were created/published from that baseline. Do not recreate or move the tag, or discard the pending environment work.
3. **Local/staging recorded:** runtime/build versions, document root, databases, search configuration, disk mappings, and disabled integrations are in [Environments](environments.md). Current production workers, scheduler, backup operation, and callbacks still need confirmation.
4. **Partially completed:** Docker environments, sanitized restored copies, isolated SQLite tests, synthetic search fixtures, and the effective-database guard are implemented. Add the remaining commerce fixtures and correct applicable legacy test paths. Do not use the default seeder or OpenCart importer.
5. Add regression tests for browse, stock visibility, cart, guest details, price/discount calculation, provider request fields, and successful/failed callbacks. Add separate failing regression cases for F01–F08 rather than treating those defects as intended behavior.
6. Review immediate production containment for public import, public registration into management, diagnostics, and embedded credentials. Make any implemented containment a small tested change with coordinated deployment. Keep this distinct from redesigning the shop.

**Exit:** immutable baseline identity, isolated tests that cannot contact production, a repeatable frontend build, documented current behavior, and a concrete disposition for critical findings. A live dump is not required to begin this phase; it is required for later migration/release confidence.

## Phase 1 Laravel and dependency upgrade

**Target:** Laravel 13 with PHP 8.4, subject to production hosting and package verification. Laravel 13 supports PHP 8.3–8.5; PHP 8.4 aligns with the observed local toolchain. Move the frontend build to Node 24 LTS and update Composer. Pin runtime/container versions rather than relying on moving image tags. [Laravel support policy](https://laravel.com/framework/docs/13.x/releases), [Node releases](https://nodejs.org/en/about/previous-releases)

Upgrade through **10 → 11 → 12 → 13**, with an independently tested commit/PR for each major. Intermediate versions need not be deployed. Separate package/API compatibility changes from new product features. Keep the existing Laravel application structure during the upgrade.

### Resolve the cart blocker first

The project locks Cart 4.2.4 to Illuminate through 10. The published 4.2.6 release declares compatibility through 12, not 13. The repository's current development manifest includes 13 but uses a different package name; it is not evidence of a supported published replacement. Do not alias a development branch or ignore platform/dependency requirements to force installation. [Published package metadata](https://packagist.org/packages/darryldecode/cart), [Upstream development manifest](https://raw.githubusercontent.com/darryldecode/laravelshoppingcart/master/composer.json)

First wrap existing cart use behind a small application interface and capture item/discount/total/session behavior. Recheck stable upstream releases when implementing. If 13 remains unsupported, replace this limited cart usage with an application-owned cart implementation, including a tested legacy-session import/transition. Do not add durable carts or multiple quantities as an incidental upgrade change.

If a safe cart transition cannot be completed promptly, Laravel 12 is a supported temporary deployment target. Record the blocker, owner, and a Laravel 13 completion date before Laravel 12 security support expires; do not describe 12 as the final target.

### Package work

- **Laravel 11:** update framework and compatible first-party packages; move Sanctum to 4 and compare its migration/config requirements with the token migration already present. Review floating-column behavior and altered-column migrations. Preserve the Laravel 10 directory structure; it is supported by the upgrade path. Remove DBAL only after confirming no application/package requirement. [Laravel 11 guide](https://laravel.com/framework/docs/11.x/upgrade)
- **Laravel 12:** update the test dependencies and accommodate Carbon 3, image validation, filesystem defaults, and request-merging behavior where used. The app explicitly defines storage roots; do not silently replace those configurations. [Laravel 12 guide](https://laravel.com/framework/docs/12.x/upgrade)
- **Laravel 13:** follow Tinker/PHPUnit dependency guidance, review request-forgery handling for exact bank endpoints, storage-driver extension callbacks, session serialization, and cache prefixes. Preserve session-cookie identity and test old cart data; copying new JSON-session defaults would invalidate existing sessions. [Laravel 13 guide](https://laravel.com/framework/docs/13.x/upgrade)
- Upgrade Scout, Meilisearch client, Sitemap, Backup, Google Drive adapter, Guzzle/HTTP factory, and dev tools to jointly compatible stable releases. Test the actual search server and cloud adapter; a Composer solve is insufficient.
- Breeze generated application code is already present. Update/remove the scaffolding dependency as appropriate without regenerating auth over customized routes/views. Retain Sanctum until its API use is explicitly decided.
- Replace the Intervention 2 integration with a maintained release in a focused change. Preserve public resize URLs through a compatibility layer and verify supported formats, aspect ratio, EXIF orientation, transparency, and cache behavior. [Image maintenance status](https://image.intervention.io/v2), [Current installation documentation](https://image.intervention.io/v4/getting-started/installation)
- Update Vite/Laravel plugin, Axios, Sass, and related assets under the new Node runtime. Remove duplicate CDN/bundled dependencies only in a separate verified cleanup. Evaluate whether Laravel Share remains useful.
- Run registry security audits when connectivity is available. Record resolved constraints in both lockfiles; avoid wildcard production dependencies. Exact resulting versions are decided by dependency resolution and tests, not assumed by this plan.

**Exit:** clean reproducible dependency installation/build, passing store regression tests, isolated callback tests, validated runtime/container configuration, and documented advisory results. Release to production only after the data-copy, critical-finding, and payment gates below; a successful framework upgrade alone is not a release approval.

## Phase 2 Checkout integrity and management access

**Purpose:** make the upgraded application safe to extend and operate. Keep this before broad GUI rollout and before introducing customer registration.

### Staff boundary and validation

Add an explicit staff flag/role, policies, and management middleware. Identify existing legitimate staff before backfilling permissions; never promote every existing user. Disable public staff registration and use an invitation or controlled provisioning process. Test guests, customers, and staff separately. Use POST logout, validated request data, constrained redirects, and deliberate missing-record responses. Remove the import HTTP trigger and public diagnostics; configure and rotate exposed real secrets with the owner/provider.

### Prices, orders, and stock

- Share one pricing service across product views, cart, order snapshot, and provider request. Define discount bounds and rounding. Proposal: integer minor-unit amounts with explicit MKD currency for new calculations; keep old monetary columns until reconciled/backfilled. Never recompute historical paid totals from today's product prices.
- Revalidate product visibility, availability, price, and quantities at checkout. Save customer corrections and item changes even when total is unchanged. Treat a paid order as immutable; create a fresh checkout after completion.
- Add order status and item snapshots containing product reference, name/model, quantity, unit price, discount, and totals. Preserve old JSON for compatibility/audit until migration is verified. Keep payment state separate from fulfillment state.
- Introduce payment attempts and uniquely identified provider results. Map historical `finished=true` to paid only after reconciliation; `finished=false` cannot safely be classified as failed.
- Define stock reservation before leaving for the bank, expiration, release after confirmed failure, and reconciliation of late payments. Process competing buyers of the final unit atomically. A paid result arriving after expiration must enter an explicit reconciliation path rather than silently overselling or being discarded.

### Provider behavior

Obtain the merchant's current cPay protocol, sandbox details, signed examples, result semantics, and retry rules. Keep the hosted payment flow and required signing/encoding format. Verify incoming authenticity and the saved attempt's order, merchant, amount, currency, status, and reference according to that protocol. Distinguish a browser return from an authoritative payment confirmation; use provider verification/reconciliation if the return alone is insufficient.

Use one database transaction with appropriate locks to record paid state and finalize stock once. Repeated results must return the same safe outcome; failure or an old attempt must not downgrade a paid order. Redact logs and never depend on browser cookies being present on a provider POST. Restrict order confirmations to the rightful session/customer or a purpose-specific secure token. Clear only the completed checkout's cart state; preserve items added afterwards.

**Exit:** tests reject forged/mismatched callbacks, duplicates do not repeat side effects, concurrent checkout cannot oversell, corrected addresses/items persist, guest payment still works, and non-staff users cannot manage content or inspect orders. Complete provider sandbox acceptance before deploying payment changes.

## Phase 3 Native administration and media

### Product and category management

Implement listing/search/pagination, creation, editing, availability, category assignment, slug validation, and deliberate archive/delete behavior. Include out-of-stock products in admin lists. Require at least one valid image before publication; allow incomplete drafts. Preserve stable slugs on rename unless deliberately changed, and record redirects for changed public URLs.

Support a cover image and an ordered image gallery. Add uniqueness/indexes/foreign keys only after orphan/duplicate cleanup. Backfill primary image paths into the new media relationship without losing extra images. Keep a temporary legacy-image fallback during migration. Decide category hierarchy from actual business needs rather than reproducing all OpenCart features.

### Media library

Build an app-owned upload/browse/search/select workflow backed by records rather than raw directory scans. Add disk/path, original filename, MIME, size, dimensions, checksum, alt text, and upload metadata. Preserve originals, generate predictable derivatives, and track product/page/editor usage so deleting a referenced file cannot silently break content.

Validate actual file content, extension/type, maximum bytes, pixel dimensions, and staff permissions. Use generated storage names, safe paths, and a location that cannot execute uploads. Initially permit tested raster formats; exclude SVG unless a deliberate sanitization pipeline is added. Use constrained resize presets, cache invalidation, and bounded conversion work. Queue expensive processing only after worker supervision is verified.

Videos are part of the requested library: support an agreed browser-playable format, upload limits, poster/preview, selection, and HTML insertion. Avoid an unsolicited transcoding platform. If video volume exceeds simple storage/delivery, select an external delivery service separately. Track embedded links as well as product galleries.

### Pages and WYSIWYG authoring

Build page CRUD with title/slug, draft/published state, preview, and HTML content. Use the same editor and media picker in product descriptions and pages, with headings, paragraphs, lists, links, tables, image selection/upload, alt text, and video insertion. No manual OpenCart/file edits should be necessary.

TinyMCE is the initial integration candidate because it already exists. Decide current Tiny Cloud/self-hosted licensing and enabled plugins before adopting a new major; do not assume the existing key grants paid capabilities. Remove irrelevant demo/premium controls. If the required licensing is unsuitable, select an alternative in a short documented editor evaluation before UI work. [TinyMCE licensing/configuration](https://www.tiny.cloud/docs/tinymce/latest/license-key/)

Sanitize HTML on the server using an explicit allowed-element/attribute/URL policy. Preserve legitimate imported formatting via representative fixtures, normalize encoded HTML once, and reject scripts/event handlers/unsafe URLs. Preview must use storefront rendering rules. Escape textarea content correctly and protect draft previews from public indexing.

**Exit:** an authorized shop operator can create a category and product, upload/reuse/reorder images, insert image/video content, edit and publish a page, change stock, and inspect orders entirely in Laravel. Guest browsing/payment and legacy media URLs continue working. Include responsive layouts, labeled inputs, keyboard use, accessible errors, and useful empty states in manual acceptance.

## Phase 4 Data migration and OpenCart retirement

Start rehearsal when the owner supplies a current Laravel database copy and media snapshot, plus OpenCart data needed for reconciliation. This may occur during earlier phases; completion depends on native authoring being ready.

1. Restore isolated, sanitized copies and compare actual schema/migrations, counts, IDs, slugs, relationships, stock, prices, orders, paid totals, and encoding.
2. Establish which data is authoritative. Preserve Laravel orders, payment references, customer details, sold stock, discounts, and native edits; do not run the old importer indiscriminately.
3. Implement a resumable, idempotent import/reconciliation command with dry run, explicit source configuration, language/prefix selection, mapping, collision reports, and checkpoints. Keep legacy IDs/mappings while internal identifiers evolve.
4. Inventory and copy all originals: covers, galleries, nested images, videos, and assets embedded in descriptions/pages. Check counts, bytes/checksums, missing files, case sensitivity, URL encoding, and external references. Build a URL/path mapping, not a blanket string replacement.
5. Update media references using that mapping. Preserve existing public URLs through compatibility routes/redirects where necessary. Compare rendered representative pages and crawl the full local/staging site for broken references.
6. Freeze OpenCart authoring at cutover and prevent stale import jobs from running. Capture a final delta. Keep Laravel checkout active only if the migration accounts for concurrent orders/stock; otherwise use a bounded maintenance window while still handling provider notifications safely.
7. Switch media reads/writes/backups to the independent store. Rebuild search and sitemap; test the complete site with OpenCart DB credentials, application, and old image path unavailable.
8. Retain a protected rollback copy for an agreed observation period. After successful reconciliation and backup restore, remove `oc` connection/settings, importer runtime entry points, hardcoded OpenCart paths, and infrastructure dependencies. Remove old services/data only as a separate scheduled retirement step.

**Exit:** native authoring, storefront, media, checkout, search, backups/restores, and operation all work with OpenCart unavailable; no unexplained missing records/files or changed paid totals remain. An archive retained for rollback is not a live dependency.

## Phase 5 Persistent carts, accounts, and social features

### Persistent anonymous carts

Add server-side carts/items with an opaque unpredictable visitor token in a secure HttpOnly cookie, expiration, and cleanup. Proposed retention is 30 days, subject to owner choice. Store product IDs/quantities rather than treating browser prices as authoritative. A checkout always revalidates price/stock. Preserve compatible old session carts through a bounded migration.

### Optional customer accounts

Add registration/login, verification, password recovery, profile/address handling, and own-order history. Guest checkout remains equally available. On login, merge anonymous/account carts once, apply the agreed quantity cap, flag unavailable items, and retain changed-price notices. Do not attach historical orders merely because a submitted email matches; require proof of ownership. Customer accounts never inherit staff permissions.

### Google, Facebook, and sharing

Use Laravel Socialite as the initial OAuth integration candidate for Google/Facebook. Handle state validation, cancellation, provider outages, verified identity linking, and duplicate-email cases without account takeover. Provider registration, allowed domains, credentials, and any reviews are external prerequisites. [Laravel Socialite](https://laravel.com/framework/docs/13.x/socialite)

Separate customer sharing from business social publishing. Add correct public product URLs, titles/descriptions, preview images, and Facebook sharing. Clarify Instagram intent—profile link, share/download experience, catalog integration, or publishing—before choosing an API; do not promise Facebook-equivalent web sharing. Recheck current provider capabilities when implementing. Social features must not gate checkout.

**Exit:** carts survive the agreed retention period, login merges are deterministic, ownership is enforced, provider failures leave guest purchase available, and accepted social workflows work on target devices.

## Suggested review units and completion evidence

Use small PRs: baseline/test isolation; urgent containment; cart compatibility; each framework major; image compatibility; staff boundary; pricing/order/payment integrity; media storage/import mapping; media UI; product/category UI; page/editor UI; migration rehearsal; cutover; persistent carts; customer accounts; each OAuth/sharing integration. Package-resolution dependencies may require combining tightly coupled upgrades, but avoid bundling unrelated business changes.

For each unit record changed behavior, tests/build results, data migration/rollback implications, unresolved risks, and updated documentation. Estimates should follow the isolated baseline and database/media inventory; repository review alone cannot establish reliable migration effort.

The next task is **staging acceptance**, followed by the outstanding payment and production-cutover work. The unresolved inputs and timing are recorded in [decisions](decisions.md); release criteria are in [testing and release](testing-and-release.md).
