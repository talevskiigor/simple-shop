# Simple Shop project guidance

## Read first

- **Live production policy (latest owner instruction, October 7):** `https://forkids.mk` is live. Disable the 1-denar override and remove identified test records after a verified private backup; provision the two owner-requested real administrators. Use `APP_ENV=production`, `STORE_SANDBOX=false`, `PAYMENTS_ENABLED=true`, `STORE_ALLOW_INDEXING=true`, and no active `PAYMENT_TEST_AMOUNT_MKD`. Keep the commented 1-denar reference for local/isolated staging only. Do not run PHPUnit, create fixtures, make test orders/payments, or perform synthetic editing on live. All future tests belong on local or an independently isolated staging deployment. `forkids.tail.mk` redirects to live and is **not staging**. `/srv/forkids-staging`, `stg_forkids`, Compose/cron/backup names and `.env.staging` are legacy names for production. Preserve email configuration and email DNS records.

- Latest payment/mail/backup/search operation: `docs/payments-backups-search.md`. The owner explicitly authorized real-card 1-denar tests, outgoing email copies and scheduled staging backups; this supersedes older sandbox prohibitions below.

- Current implementation and verification: `docs/implementation-log.md` and `docs/admin-and-media.md`. Historical analysis sections describe the preserved 1.0 baseline, not the present implementation.

- Read the current status in `README.md`, then `docs/decisions.md`, `docs/project-analysis.md`, and `docs/modernization-plan.md` before implementation. The plan's "Next implementation checkpoint" defines the next bounded task and acceptance criteria.
- Read `docs/testing-and-release.md` before running the application, database commands, or tests.
- The initial assessment covers `develop` at `93d6e04d9efb3a905097c1973edad74813549e34`. Check current code and Git status rather than assuming that snapshot is still current.
- Tag `1.0` preserves the inspected baseline; work is on `codex/refactor-simple-store`. The owner subsequently authorized restoring `ForKIDS.zip`, replacing the Docker setup, and deploying staging at `forkids.tail.mk` with a dedicated `stg_forkids` database. Read `docs/environments.md` for current operation. Laravel 13 and native administration are implemented; read `docs/implementation-log.md` and `docs/admin-and-media.md` for current state. The 198 compared source files match the supplied backup, not necessarily today's live deployment.

## Current authorized implementation

The owner authorized the complete work recorded in `docs/implementation-log.md`, including commits/pushes, staging deployment, admin-only management, WYSIWYG/media, OpenCart retirement, image-resizer fixes, and verified unused/duplicate media cleanup. Resolve ordinary implementation choices autonomously. Keep staging public and preserve private recovery snapshots before migrations/cleanup. The latest production authorization above supersedes the earlier staging-only payment permission. Preserve the existing SMTP/email-copy and backup configuration. No customer roles or role-management system in this stage. Do not change the original production deployment or destroy its files as an incidental staging cleanup.

## Product requirements

- Keep a simple store: products, categories, one or more images per product, HTML descriptions, price, and availability.
- Preserve checkout without a customer account and the existing CaSys/cPay integration.
- Upgrade Laravel before broad feature work. Complete native administration and migrate media before removing OpenCart.
- Persistent guest carts, customer accounts, Google/Facebook login, and social sharing are later phases.
- Preserve product/page URLs, Macedonian content, and MKD pricing unless the task explicitly changes them.

## Working boundaries

- Never assume `.env` points to a disposable database. Do not print credentials, payment secrets, customer records, or dumps.
- Do not run `db:seed`, `migrate:fresh`, `migrate:refresh`, `OCSeeder`, the public `/update` route, or the parent repair script against an existing environment as setup or diagnosis.
- `tests/bootstrap.php` forces SQLite `:memory:` across all environment sources, and `tests/CreatesApplication.php` rejects another effective database before `RefreshDatabase`. Run tests inside the local or isolated staging app container, never the live container. The bootstrap rejects an inherited production environment before forcing test settings; never remove either guard to get a test passing. The full suite includes aligned auth, guest checkout, administration, content/media and resizer/cleanup tests.
- Automated tests must use local fakes and synthetic credentials. The owner authorized real-card staging tests and email copies to igor.talevski+forkids@gmail.com. Do not enter or submit a real card yourself; verify the hosted form and leave the charge to its owner. Do not modify original production data or reuse its backup destination.
- Document exposed credentials by location, never by value. Coordinate rotation of real credentials with deployment and the payment provider.
- Keep release tag `1.0` immutable once created. Do not accidentally tag documentation or refactoring commits as the old baseline.
- Retain OpenCart production files until inventory, URL checks, backups, and cutover verification pass. The supplied ZIP and raw extracted SQL must be deleted after verified restoration, as explicitly requested. Do not commit dumps, media, `.private`, `.env.docker`, `.env.staging`, or credentials.
- Keep `STORE_SANDBOX=true` locally and on independent staging; live uses `false`. `STORE_TRACKING_ENABLED=false` preserves the previously disabled tracking independently of the environment. Public indexing is separately authorized on `forkids.mk` through `STORE_ALLOW_INDEXING=true`; local/tests retain noindex. Admin, cart, order and payment pages remain noindex. The deployed store uses full order totals, signed bank callbacks through `PAYMENTS_ENABLED`, real SMTP with `MAIL_COPY_TO`, and backup jobs through `BACKUPS_ENABLED`. Production rejects the 1-denar override and reuse of an old test attempt. Local/tests remain disabled by default. Test payments preserve real totals and never fulfill orders/decrement stock; bank receipts require merchant-portal reconciliation before admin confirmation. Do not treat a browser return as settlement.
- Staging HTTP Basic authentication was removed at the owner's request. Keep the storefront public and application admin authentication enabled; do not reintroduce an HTTP password as a default deployment step.
- Local/staging Scout uses `collection` so the query and product fields share ASCII normalization. Do not switch to `database` without a Cyrillic/Latin result regression check; its SQL engine reads original columns. Run `NavigationSearchTest` when changing search. Compare actual result IDs/cards for Cyrillic and Latin equivalents, not just HTTP success; the verified examples and limitations are in `docs/environments.md`.
- Use `scripts/dev` for local Compose commands; the ordinary `.env` is legacy and is deliberately overlaid. Do not reset named volumes or modify unrelated server sites/databases/containers. Staging uses PHP 8.3 in Docker because the host PHP 8.5 is unsuitable for this locked baseline.

## Implementation expectations

- Favor the existing Laravel/Blade application and focused services over a rewrite. Separate dependency upgrades from business behavior changes.
- Use forward migrations for deployed schemas; reconcile data before adding constraints or soft-delete behavior.
- Use validated input and explicit admin authorization. Authentication alone does not grant management rights.
- Sanitize authored HTML on the server and constrain media uploads and transformations.
- Use deterministic money calculations. Confirmed payment processing must be authenticated, atomic, and idempotent.
- Cover changed behavior, particularly guest checkout, stock, repeated callbacks, media URLs, and admin access.
- Update documentation in the same change. Distinguish verified behavior, proposals, and unresolved live-environment facts.
