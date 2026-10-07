# Modernization implementation log

## Authorized scope — October 7, 2026

The owner authorized implementation, decisions where requirements are unclear, commits, pushes, and staging deployment for the environment checkpoint, regression tests/CI, Laravel upgrade, admin-only management, WYSIWYG editing, image/video media library, OpenCart removal, and removal of unused/duplicate media after reviewing/fixing image resizing. Production deployment and irreversible deletion of original production systems are outside this staging rollout.

Decisions:

- One administrator account type; no role-management system or customer accounts in this stage. Public registration is disabled. Existing accounts are not automatically promoted.
- Preserve Macedonian content/URLs, Cyrillic/Latin search, guest checkout, and current one-unit cart behavior.
- Keep real payments, imports, outgoing mail and scheduled tasks disabled in local/staging. Provider acceptance is separate from this work.
- Review image paths, bounds, format support, quality, cache identity/invalidation, and missing-source behavior before media cleanup.
- Identify duplicates by content hash; preserve all referenced media, rewrite references deliberately before removing duplicates, and keep a private recovery archive and deletion manifest. Missing originals are reported, not silently invented.
- Follow individually tested framework majors; recheck package constraints at each step. Package replacement is allowed when required for supported versions.

## Progress

- Environment checkpoint: application `bfc8c9c` committed/pushed on `codex/refactor-simple-store`.
- Outer Docker cleanup: `f7ef363` committed/pushed on the same-named branch in the separate `simple-docker` repository.
- Staging recovery snapshot: `/srv/forkids-staging/backups/20261007-modernization-baseline` complete (391 MB, protected database/media/app/config copies with checksums).
- Regression checkpoint: 46 tests / 158 assertions pass on Laravel 10.31 / PHP 8.3, including Cyrillic/Latin search, guest checkout and authentication. Repaired missing password/profile form components; added isolated GitHub Actions tests and frontend build.
- Pending: framework upgrades, complete admin/content/media authoring, image resizer repair, OpenCart retirement, media reconciliation/cleanup, final staging deployment and verification.

Update this file at every release checkpoint with versions, commits, tests, deployment evidence, and remaining limitations. Never include credentials or customer records.
