# Simple Shop project guidance

## Read first

- Read `docs/decisions.md`, `docs/project-analysis.md`, and `docs/modernization-plan.md` before implementation.
- Read `docs/testing-and-release.md` before running the application, database commands, or tests.
- The initial assessment covers `develop` at `93d6e04d9efb3a905097c1973edad74813549e34`. Check current code and Git status rather than assuming that snapshot is still current.
- The owner subsequently authorized publishing the documentation on `codex/refactor-simple-store`. Tag `1.0` preserves the inspected `develop` source baseline; deployed-code parity remains unverified. Application implementation is the next task, not part of documentation publication; follow subsequent user instructions when scope changes.

## Product requirements

- Keep a simple store: products, categories, one or more images per product, HTML descriptions, price, and availability.
- Preserve checkout without a customer account and the existing CaSys/cPay integration.
- Upgrade Laravel before broad feature work. Complete native administration and migrate media before removing OpenCart.
- Persistent guest carts, customer accounts, Google/Facebook login, and social sharing are later phases.
- Preserve product/page URLs, Macedonian content, and MKD pricing unless the task explicitly changes them.

## Working boundaries

- Never assume `.env` points to a disposable database. Do not print credentials, payment secrets, customer records, or dumps.
- Do not run `db:seed`, `migrate:fresh`, `migrate:refresh`, `OCSeeder`, the public `/update` route, or the parent repair script against an existing environment as setup or diagnosis.
- The stock feature suite uses `RefreshDatabase`; its database override is commented out. Establish isolation before running it.
- Use local fakes and provider sandbox services. Do not send test payments, email, backups, or search writes to production.
- Document exposed credentials by location, never by value. Coordinate rotation of real credentials with deployment and the payment provider.
- Keep release tag `1.0` immutable once created. Do not accidentally tag documentation or refactoring commits as the old baseline.
- Retain OpenCart files until inventory, URL checks, backups, and cutover verification pass.

## Implementation expectations

- Favor the existing Laravel/Blade application and focused services over a rewrite. Separate dependency upgrades from business behavior changes.
- Use forward migrations for deployed schemas; reconcile data before adding constraints or soft-delete behavior.
- Use validated input and explicit admin authorization. Authentication alone does not grant management rights.
- Sanitize authored HTML on the server and constrain media uploads and transformations.
- Use deterministic money calculations. Confirmed payment processing must be authenticated, atomic, and idempotent.
- Cover changed behavior, particularly guest checkout, stock, repeated callbacks, media URLs, and admin access.
- Update documentation in the same change. Distinguish verified behavior, proposals, and unresolved live-environment facts.
