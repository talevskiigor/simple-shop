# Simple Shop

A Laravel store already used in production, with products, categories, HTML descriptions, guest checkout, and CaSys/cPay payment. OpenCart supplies imported content and the documented source image directory.

## Project documentation

- [Current application analysis](docs/project-analysis.md): architecture, data, features, dependencies, and findings.
- [Modernization plan](docs/modernization-plan.md): Laravel upgrade, administration, media, payments, OpenCart retirement, and customer features.
- [Testing and release guide](docs/testing-and-release.md): isolated development, database-copy checks, release baseline, deployment, and rollback.
- [Decisions and open questions](docs/decisions.md): requirements, proposed choices, and information needed at each phase.
- [Guidance for Codex](AGENTS.md): project context and working boundaries.

The initial analysis covers `develop` at `93d6e04d9efb3a905097c1973edad74813549e34`, reviewed on October 6, 2026, America/Phoenix. Tag `1.0` preserves that source baseline, and `codex/refactor-simple-store` contains the documentation. Planned application changes are not implemented; deployed-code parity remains unverified.

## Before running the application

Read the testing and release guide first. The feature-test configuration does not select an isolated database. The default seeder truncates users and runs the OpenCart importer. Do not use ordinary seed/reset commands against an existing database.

The application is locked to Laravel 10.31.0. Dependencies are recorded in `composer.lock` and `package-lock.json`. The surrounding local Docker setup is two directories above this application, outside this Git repository.

## Legacy media layout

The previous README documented this production mapping:

```text
/var/www/html/simple-shop/public/media/images
  -> /var/www/forkids.mk/image/catalog
```

This is a legacy dependency to migrate, not a new-install instruction. The local checkout lacks `public/media/images`. Store-owned media and preserved URLs are required before retiring OpenCart.
