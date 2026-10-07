# Simple Shop

A simple Laravel 13 store with native administrator management, products/categories, rich-text pages, image/video media library, Cyrillic/Latin search and guest checkout. The existing CaSys/cPay integration remains disabled in isolated local/staging environments pending provider acceptance.

## Local development

The Docker setup now lives in this repository. It uses PHP 8.3, MariaDB 11.8, and Node 24. The locked framework is **Laravel 13.35**, upgraded through separately tested 11 and 12 checkpoints.

```bash
scripts/init-local                 # once; preserves existing settings
scripts/dev up -d --build
scripts/dev exec --user www-data app composer install --no-interaction --prefer-dist
scripts/dev --profile tools run --rm node
```

For an empty installation, run forward migrations and `php artisan admin:manage` inside the app container. This workspace already has a recovered, anonymized database/media copy; do not reimport or reset it. See [Environments](docs/environments.md).

- Local store: <http://localhost:8088>
- Staging: <https://forkids.tail.mk> (public storefront; admin login required for management)
- Admin: `/admin/login` on either environment
- Credentials in this workspace: `.private/access-credentials.json`; never commit or paste its contents into chat.

Both environments disable real payments/callbacks, imports, scheduled tasks, outgoing mail, and tracking. Staging is separate from production and uses its own database, `stg_forkids`.

## Current status and next work

The environment, regression/CI and Laravel upgrades are committed. Native administration, visual editing, media management, OpenCart retirement and image cleanup are implemented; deployment evidence and exact release revisions are maintained in [the implementation log](docs/implementation-log.md).

The recovered catalog contains 220 products, 15 categories and four pages. Local has 86 anonymized orders; staging has 87, preserving one additional pre-existing staging order. Cleanup on both copies retained 306 originals, archived 74 unused files and four duplicate copies, and preserved ten missing-original records with placeholders. No live customer dump or media is committed. `ForKIDS.zip` and the raw extracted SQL were deleted after restoration.

Search supports **Macedonian Cyrillic and equivalent Latin text** (`трицикл` / `tricikl`, `количка` / `kolicka`, `коцки` / `kocki`) using Scout's collection engine and shared normalization. Keep matching-result regression checks when changing search drivers.

Staging is public, without HTTP Basic authentication. Management requires administrator login. Sandbox controls disable real payments/callbacks, outgoing mail, tracking and scheduled tasks. Production is unchanged. This release deliberately starts new JSON sessions; existing sessions/carts reset during this staging upgrade.

**Next:** owner acceptance testing on staging using the [admin and media guide](docs/admin-and-media.md), recovery/replacement of the ten missing originals, then payment-provider verification and production-cutover planning. Customer accounts/social login and longer-lived guest carts remain later work.

## Project documentation

- [Admin, editor and media guide](docs/admin-and-media.md): daily management, image delivery, cleanup and recovery.
- [Implementation log](docs/implementation-log.md): completed checkpoints and release evidence.
- [Environments and recovery](docs/environments.md): Docker commands, staging operations, restored data, media exceptions, and private files.
- [Application analysis](docs/project-analysis.md): original architecture, data, features, dependencies, and findings, with subsequent evidence noted.
- [Modernization plan](docs/modernization-plan.md): Laravel upgrade, administration, media, payments, OpenCart retirement, and customer features.
- [Testing and release guide](docs/testing-and-release.md): test isolation, validation, production deployment, and rollback.
- [Decisions and open questions](docs/decisions.md): requirements, implementation choices, and remaining inputs.
- [Guidance for Codex](AGENTS.md): context and working boundaries.

Tag `1.0` preserves `develop` at `93d6e04d9efb3a905097c1973edad74813549e34`. Work continues on `codex/refactor-simple-store`. The supplied October 6 backup matched the 198 compared application/configuration/view/migration/manifest files; this does not prove that production has had no changes since the backup.

## Media and data boundaries

Recovered original files are now independent copies under `public/media/images` locally and `/srv/forkids-staging/media` on staging. They are private runtime data, outside Git. Ten referenced image files were absent from the supplied backup; see the recovery report. Active application dependencies on OpenCart are removed. Original production files are preserved outside this staging rollout.

Never use `db:seed`, `migrate:fresh`, `migrate:refresh`, `/update`, or the old parent `repair.sh` on the restored databases. Tests force an independent SQLite in-memory database and reject other database settings before Laravel test traits run.
