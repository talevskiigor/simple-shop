# Simple Shop decisions and open questions

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

## Proposed defaults

- **Architecture:** extend Laravel/Blade/Bootstrap; no separate SPA, ecommerce platform replacement, or general page builder.
- **Upgrade:** Laravel 13, PHP 8.4, Node 24 LTS, through separately tested framework majors. Resolve cart compatibility before 13; use 12 only as a time-limited fallback.
- **Admin:** a small explicit staff role and policies. Public customer registration must never grant management access.
- **Editor:** evaluate upgrading the existing TinyMCE integration with one shared media picker. Confirm license/deployment constraints before committing to editor details.
- **Media:** extend app-owned storage/metadata, retain originals, order gallery images, and track usage. Basic playable video support, not a transcoding platform.
- **Money:** integer minor-unit amounts and explicit currency for new calculations, with reconciled migration and preserved historical snapshots.
- **Cart quantity:** preserve the existing one-unit rule during the framework upgrade. Decide multiple-unit behavior before stock/cart feature changes.
- **Guest retention:** 30 days is an initial proposal, not an accepted business requirement.
- **URLs/content:** preserve slugs, Macedonian content, current MKD currency, and guest flow until deliberately changed.

## Inputs needed before implementation and release

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

### Before GUI and content migration

- Are categories flat or hierarchical? Should public lists show sold-out products, or retain the current hiding behavior?
- Is one Macedonian language sufficient? What source OpenCart language/store/prefix is authoritative?
- What image/video count, disk volume, maximum file size, and video format are needed? Are assets hosted outside the known OpenCart directory?
- What editor licensing/deployment constraints exist? Is preview plus draft/publish sufficient initially, or is revision history needed?
- What data is maintained directly in Laravel today versus OpenCart? Stock, discounts, and native edits need explicit ownership.
- The supplied Laravel database/media snapshot has been restored and its raw ZIP deleted. When can the ten missing originals and any required OpenCart database snapshot be supplied for reconciliation, and what final cutover/retention window is acceptable?

### Before customer and social phases

- How long should anonymous carts persist, and how should duplicate items merge after login?
- What account/address/history features are actually needed? How should guests prove ownership of past orders?
- What does Instagram integration mean: a profile link, customer sharing experience, catalog integration, or business publishing?
- Who owns Google/Meta app registration, domains, credentials, and required provider reviews?

The local/staging runtime and access choices above are resolved. Production runtime, provider sandbox access, quantity rules, and other unanswered business questions remain open. Preserve current business behavior during framework compatibility work until those choices are made.

## Next agreed direction

The owner requested documentation of completed work and the next step. The recommended sequence is a reviewed environment checkpoint, focused catalog/cart/guest-checkout regression fixtures and CI, then Laravel 10 → 11 in a separate change. The [implementation checkpoint](modernization-plan.md#next-implementation-checkpoint) records acceptance criteria and the later 12/13, GUI/media, OpenCart-retirement, and account/social phases. Those later features have not started.

The original analysis commit and baseline tag are published. At this handoff, the environment/search/access updates are deployed on staging but their repository changes remain uncommitted; the outer Docker-wrapper repository also needs a separate checkpoint. Do not confuse a working staging deployment with a published source release.

## Scope boundaries

No multi-vendor system, subscriptions, variant engine, ERP, marketplace, tax engine, or full shipping platform is requested. Add any such scope only when a concrete business requirement emerges. Existing tax/catalog scaffolding does not prove those features are needed.

Completion means a supported, tested Laravel store with native content/media authoring, reliable guest checkout, standalone files/data, and verified operations without OpenCart. Optional identity/social work follows that foundation.

Update this register when a decision is made: record the choice, reason, date, and affected phase/tests. Keep the [analysis](project-analysis.md), [plan](modernization-plan.md), and [release guide](testing-and-release.md) aligned.
