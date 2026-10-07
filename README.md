# Simple Shop

A Laravel store already used in production, with products, categories, HTML descriptions, guest checkout, and CaSys/cPay payment. Native content/media administration and the Laravel upgrade remain planned work.

## Local development

The Docker setup now lives in this repository. It uses PHP 8.3, MariaDB 11.8, and Node 24. Laravel remains at the locked **10.31.0** baseline.

```bash
scripts/init-local                 # once; preserves existing settings
scripts/dev up -d --build
scripts/dev exec --user www-data app composer install --no-interaction --prefer-dist
scripts/dev --profile tools run --rm node
```

A fresh installation also needs the isolated database/media restore described in [Environments](docs/environments.md). Do not seed the legacy importer. This workspace already has the recovered, anonymized copy.

- Local store: <http://localhost:8088>
- Staging: <https://forkids.tail.mk> (public storefront; admin login required for management)
- Admin: `/admin/login` on either environment
- Credentials in this workspace: `.private/access-credentials.json`; never commit or paste its contents into chat.

Both environments disable real payments/callbacks, imports, scheduled tasks, outgoing mail, and tracking. Staging is separate from production and uses its own database, `stg_forkids`.

## Current status and next work

The local Docker environment and staging deployment are working. The supplied backup restored 220 products, 15 categories, four pages, 86 anonymized orders, and 384 original media files. `ForKIDS.zip`, the raw extracted SQL, and temporary deployment archives were deleted after verification. Ten referenced image files were absent from the backup and still need recovery.

Staging has a public storefront with no HTTP password prompt. Management still uses `/admin/login`. Sandbox controls keep payments/callbacks, imports, outgoing mail, tracking, and scheduled tasks disabled. This environment work has not changed production.

Navigation search supports **Macedonian Cyrillic and equivalent Latin text**. For example, `трицикл` / `tricikl`, `количка` / `kolicka`, and `коцки` / `kocki` return the same respective result sets. The initial database-driver mismatch was corrected using Scout's collection driver. See [search behavior, limitations, and verification](docs/environments.md#cyrillic-and-latin-navigation-search).

The combined search/safety suite passes **16 tests and 54 assertions**. Broader commerce/auth tests, the Laravel upgrade, the new administration GUI/WYSIWYG/media library, and OpenCart retirement remain unfinished.

**Next:** establish a reviewed Git checkpoint for the environment work, add focused catalog/cart/guest-checkout tests, then upgrade Laravel **10 → 11** in a separate change. Continue to 12 and 13 after compatible dependencies and regression checks are confirmed. The [next implementation checkpoint](docs/modernization-plan.md#next-implementation-checkpoint) defines the concrete scope and acceptance criteria.

At this handoff, tag `1.0` and the initial analysis commit are published; the environment/search/access changes are deployed to staging but remain uncommitted locally. The outer Docker-wrapper repository has separate pending changes. Check both working trees before starting the next change.

## Project documentation

- [Environments and recovery](docs/environments.md): Docker commands, staging operations, restored data, media exceptions, and private files.
- [Application analysis](docs/project-analysis.md): original architecture, data, features, dependencies, and findings, with subsequent evidence noted.
- [Modernization plan](docs/modernization-plan.md): Laravel upgrade, administration, media, payments, OpenCart retirement, and customer features.
- [Testing and release guide](docs/testing-and-release.md): test isolation, validation, production deployment, and rollback.
- [Decisions and open questions](docs/decisions.md): requirements, implementation choices, and remaining inputs.
- [Guidance for Codex](AGENTS.md): context and working boundaries.

Tag `1.0` preserves `develop` at `93d6e04d9efb3a905097c1973edad74813549e34`. Work continues on `codex/refactor-simple-store`. The supplied October 6 backup matched the 198 compared application/configuration/view/migration/manifest files; this does not prove that production has had no changes since the backup.

## Media and data boundaries

Recovered original files are now independent copies under `public/media/images` locally and `/srv/forkids-staging/media` on staging. They are private runtime data, outside Git. Ten referenced image files were absent from the supplied backup; see the recovery report. OpenCart application dependencies remain in source until native administration and a complete cutover are verified.

Never use `db:seed`, `migrate:fresh`, `migrate:refresh`, `/update`, or the old parent `repair.sh` on the restored databases. Tests force an independent SQLite in-memory database and reject other database settings before Laravel test traits run.
