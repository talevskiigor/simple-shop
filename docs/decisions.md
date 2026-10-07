# Simple Shop decisions and open questions

> **October 7 modernization update:** Laravel 13, native administrator/content/media management, server-sanitized visual editing, resizer repair and OpenCart retirement are implemented. The earlier baseline/next-step sections below are historical. Current status, executed tests, cleanup results and deployment evidence are authoritative in [implementation-log.md](implementation-log.md) and [admin-and-media.md](admin-and-media.md). Payment-provider acceptance and production rollout remain separate.

This is the decision register for the initial October 6, 2026 analysis. It separates the owner's requirements from proposed implementation choices. The environment work, search correction, and staging access change are implemented; the decisions below distinguish them from the pending framework and feature work.

## Publication decision

The owner authorized committing and pushing the documentation to a new branch. Remote `develop` was checked and matches `93d6e04d9efb3a905097c1973edad74813549e34`. Annotated tag `1.0` preserves this source baseline, and `codex/refactor-simple-store` starts from it. Production deployment parity is still an open verification item; the tag does not assert it. That publication changed documentation only. Subsequent environment work is recorded below.

## Environment implementation decision — October 6, 2026

The owner supplied `ForKIDS.zip` and authorized using then deleting it, without committing it. They requested a local Docker replacement and staging on `server.tail.mk`, vhost `forkids.tail.mk`, database `stg_forkids` with dedicated credentials.

- Keep the existing lockfiles and Laravel 10.31.0 for this restore baseline. Use PHP 8.3 in the same container image locally and on staging; use Node 24 for asset builds. Revisit PHP 8.4 during framework upgrades.
- Local MariaDB runs in a private Compose network. Staging uses the existing server MariaDB through its Unix socket and a database-scoped account; other databases/sites are untouched.
- Place staging outside the server's default document root at `/srv/forkids-staging`. Reuse Apache and the existing Cloudflare HTTPS tunnel, with an exact-host vhost. HTTP Basic authentication was initially enabled, then removed at the owner's request. The storefront is public; application admin login and sandbox protections remain enabled.
- Sanitize customers, credentials, tokens, and payment references before serving copies. Disable production payment/import/tracking/scheduled-backup paths; mail logs locally. These controls apply only when `STORE_SANDBOX=true`.
- Use Scout's collection driver for the 220-product baseline environments. The initial database-driver choice compared a Latin-normalized query with original Cyrillic columns and returned incorrect empty results; collection search applies the same model normalization as the query. It loads the catalog in memory, so reassess an isolated search index as the catalog grows. Production Meilisearch parity/relevance tests remain pending.
- Preserve all 384 recovered original files and document 10 missing filenames plus empty references. Do not silently change product data to hide missing originals.
- Version reusable Docker/deploy files in this application repository. Retire the outer repository's obsolete Docker build files and keep its convenience scripts as wrappers. Preserve its independent Git history, old volumes, unrelated containers, and user-owned `repair.sh`.

See [Environments](environments.md) for verified results and operational details. Production code/runtime inventory, cPay sandbox access, complete media recovery, and the broader regression suite remain release prerequisites.

## Confirmed requirements

- The store is already used in production and its payment integration functions.
- Analyze and document before making application changes.
- Preserve the current `develop` baseline as version `1.0`, then begin refactoring on a new branch during implementation.
- Upgrade Laravel before broad feature additions.
- Keep a simple product/category store: one or more product images, HTML description, price, and availability.
- Complete native administration, WYSIWYG HTML editing, and an image/video media library.
- Eventually remove OpenCart completely as an operational dependency.
- Use a production database copy for testing in a later phase.
- Preserve payment without a customer account. Later add durable guest carts, optional accounts, Google/Facebook login, and social features including Facebook/Instagram.
- Keep documentation useful for future Codex development.

## Implemented choices — October 7, 2026

- **Architecture:** existing Laravel/Blade/Bootstrap application; no SPA or page-builder rewrite.
- **Runtime:** Laravel 13.35 on PHP 8.3 containers, MariaDB 11.8 and Node 24. Framework majors were independently tested. Unsupported cart and image packages were replaced with focused application services.
- **Admin:** one explicitly provisioned administrator account type, no role system, no public registration or automatic promotion of restored users.
- **Content:** flat categories, one Macedonian storefront, explicit product visibility, ordered galleries with a required cover image, draft/published pages and server-sanitized HTML. Existing slugs are preserved unless deliberately edited; no automatic redirect is created for a manually changed product/page slug.
- **Editor:** locally bundled MIT Tiptap 3 with a shared media picker, replacing the old TinyMCE dependency/key. Basic images and MP4/WebM video, 32 MB uploads and a 20-megapixel image limit; no transcoding service.
- **Money/cart:** decimal persisted amounts and integer minor-unit cart totals, current product discount, availability revalidation, corrected delivery details and immutable finished-order snapshots. Retain one unit per product. JSON sessions intentionally reset old sessions/carts during this staging upgrade.
- **Media:** retain originals outside Git, source-aware bounded WebP variants, audited hash deduplication and unused-file removal, compatibility aliases and protected recovery copies. Both environments now retain 306 originals; the ten absent originals remain documented.
- **OpenCart:** importer/connection/settings/backup dependency removed; local/staging operate independently. Original production is unchanged and must have a separate reconciled cutover.
- **Access:** staging storefront stays public without Basic Auth; management requires admin login. No live payment/mail/tracking/scheduled tasks in isolated copies.

## Inputs needed before production and later work

### Before production deployment

Local/staging choices are settled and documented: PHP 8.3 containers, MariaDB 11.8, Node 24 builds, application-owned Docker/deploy files, and collection search. The supplied backup/source comparison and restored data/media are verified. These remaining questions concern current production and final cutover:

- What exact commit is live? Does it match the inspected `develop` commit, and are there server-only edits?
- What hosting, web-server PHP/CLI PHP, database versions, deployment process, and rollback mechanism are in use?
- Where are scheduler/workers configured, and which backup/restore has most recently succeeded?

These affect production rollout, not the ability to start synthetic test isolation and dependency analysis.

### Before payment and pricing changes

- Is a CaSys/cPay sandbox merchant available? Obtain the current merchant protocol, verification examples, callback semantics, and support contact.
- Are prices tax-inclusive, is shipping always free as the current view states, and what discount/rounding rules are intended?
- Should customers buy several units when stock permits, or retain one unit per product? How should reservations, failed payments, cancellations, and refunds affect stock?
- Which existing users are legitimate staff? Coordinate any real credential rotation without interrupting payment.

### Content acceptance and production reconciliation

The native GUI/editor/media decisions above are implemented. Owner testing should verify the workflows before production cutover. Recover the ten missing originals from an authorized newer media copy or provide replacements; do not rerun OpenCart import. Establish the final live-data capture, authoring freeze, retention and rollback window without overwriting new production orders or stock changes.

### Before customer and social phases

- How long should anonymous carts persist, and how should duplicate items merge after login?
- What account/address/history features are actually needed? How should guests prove ownership of past orders?
- What does Instagram integration mean: a profile link, customer sharing experience, catalog integration, or business publishing?
- Who owns Google/Meta app registration, domains, credentials, and required provider reviews?

The local/staging runtime and access choices above are resolved. Production runtime, provider sandbox access, quantity rules, and other unanswered business questions remain open. Preserve current business behavior during framework compatibility work until those choices are made.

## Next agreed direction

The owner authorized the complete implementation, commits, pushes and staging deployment for this session. The environment/tests, framework upgrade, admin/editor/media, OpenCart removal and cleanup are complete. Next is owner acceptance on staging, missing-original recovery, then provider-approved payment work and a separately prepared production rollout. Customer/social features remain later scope. See [implementation-log.md](implementation-log.md) for the executed release evidence and [admin-and-media.md](admin-and-media.md) for acceptance steps.

## Scope boundaries

No multi-vendor system, subscriptions, variant engine, ERP, marketplace, tax engine, or full shipping platform is requested. Add any such scope only when a concrete business requirement emerges. Existing tax/catalog scaffolding does not prove those features are needed.

Completion means a supported, tested Laravel store with native content/media authoring, reliable guest checkout, standalone files/data, and verified operations without OpenCart. Optional identity/social work follows that foundation.

Update this register when a decision is made: record the choice, reason, date, and affected phase/tests. Keep the [analysis](project-analysis.md), [plan](modernization-plan.md), and [release guide](testing-and-release.md) aligned.
