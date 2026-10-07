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
- Framework upgrade, native administration, editor/media, resizer repair and OpenCart retirement are deployed to staging. Local and staging media cleanup are complete. Owner acceptance testing is next.

Update this file at every release checkpoint with versions, commits, tests, deployment evidence, and remaining limitations. Never include credentials or customer records.

### Framework checkpoint: Laravel 11.57 / PHPUnit 11.5.57

46 tests / 158 assertions pass. Kept the existing application structure; updated Sanctum middleware, cart 4.2.6, backup 9, collision 8 and IDE helper 3; removed unused Breeze scaffolding and Doctrine DBAL. Existing Sanctum migration is already owned by the app. Reviewed migration type/modifier changes: no affected column-change calls. This intermediate release is local only: Composer's advisory block required a one-command override for Laravel 11; no persistent security-ignore setting was added. Continue directly to supported framework versions before deployment.

### Framework checkpoint: Laravel 12.69.3 / Carbon 3.14.2

46 tests / 158 assertions pass with security blocking enabled and no Composer security advisories. Existing explicit filesystem/session settings and legacy application structure retained. Next, replace the unsupported cart dependency before Laravel 13.

### Framework checkpoint: Laravel 13.35 / PHPUnit 12.5.38 / backup 10.3.3

48 tests / 170 assertions pass; Composer reports no security advisories. Replaced unsupported darryldecode/cart with app-owned scalar session IDs and immutable order snapshots. Preserves one unit per product and guest checkout, rechecks availability/current price, uses the actual product discount with integer minor-unit totals, prevents empty orders and updates corrected delivery details. Finished orders are not overwritten. Session serialization is now JSON: **this staging release starts fresh sessions/carts**; any later production rollout must explicitly accept that session reset or add a separate legacy-cart migration. Removed obsolete cart configuration. Updated Laravel 13 request-forgery middleware and disabled cached object deserialization. No production deployment performed.

Upgrade references: [Laravel 11](https://raw.githubusercontent.com/laravel/docs/11.x/upgrade.md), [Laravel 12](https://laravel.com/docs/12.x/upgrade), [Laravel 13](https://laravel.com/docs/13.x/upgrade), [backup package](https://github.com/spatie/laravel-backup/blob/main/UPGRADING.md).


### Native administration / media release

Implemented administrator-only authorization/provisioning/management, protected account deletion, POST logout, product/category/page authoring, ordered image/video galleries, draft/public pages, locally bundled Tiptap editor, Symfony server-side HTML sanitization, validated deduplicated uploads and protected media removal. Converted price/discount/order totals to decimal columns with a forward migration. Missing records remain visible for replacement; archived catalog data/order snapshots are preserved.

Replaced Intervention Image 2 with bounded GD transformations and source-aware, atomic WebP caching. Removed OpenCart routes/importer/connection/settings/backup path and public diagnostics. Removed duplicated tracked branding files with URL redirects. Full suite: 60 tests / 297 assertions at the latest run; frontend build and production-package audit pass. The Laravel 13 checkpoint also passed [GitHub Actions](https://github.com/talevskiigor/simple-shop/actions/runs/37586250655).

Browser QA locally: administrator login, visual editor, Cyrillic text, library image insertion, publication and storefront rendering, plus real image upload succeeded. No admin-page browser errors observed. Disposable account/page/upload were removed after verification.

Local cleanup completed in maintenance mode: 384 → 306 originals; 74 unused originals and four duplicate copies, 38,217,946 bytes archived/removed. Recovery: app runtime `storage/app/media-recovery/20261007-native-admin` (protected metadata snapshots, checksums, originals and completion manifest). Follow-up audit: zero unused/duplicate candidates; the original ten absent filenames remain reported. Existing published duplicate URLs resolve through aliases. Staging received the same verified cleanup after its separate pre-deployment backup.

Outer repository OpenCart bootstrap removal committed/pushed as `ad5a8f1`; its untracked owner-maintained `repair.sh` was left untouched. Full frontend audit is now clean after updating the compatible picomatch patch. All production and development Composer/npm dependencies were checked.

Final pre-deploy checks: 60 tests / 297 assertions pass, including a real MP4 fixture and byte-range delivery. Route caching/clearing passes. Composer strict validation and Composer/npm audits pass with no reported vulnerabilities. Local inventory remains 220 products / 15 categories / 4 pages / 86 orders / 11 users (one administrator); 316 media rows, 313 active library entries, 306 originals. Search counts remain 43 / 12 / 39 for the recorded Cyrillic/Latin pairs.

### Staging activation and final verification — October 7, 2026 UTC

Feature release `0b96a50` was committed/pushed and activated at <https://forkids.tail.mk>. Its [GitHub Actions run passed](https://github.com/talevskiigor/simple-shop/actions/runs/37591468025). Follow-up release `42144f7` includes the packaging-permission correction, an operational search-filter correction, extra search regression coverage, removal of the unused Intervention configuration, and these final documents. Its [GitHub Actions run passed](https://github.com/talevskiigor/simple-shop/actions/runs/37593499019). It was activated and checked on staging. Documentation-only follow-ups preserve that application code; `/srv/forkids-staging/app/RELEASE` records the exact deployed branch revision.

Immediately before activation, `/srv/forkids-staging/backups/20261007-native-admin-predeploy` captured the database, originals, secrets, Compose configuration and checksums; the prior entire app is retained there as `app/`. The earlier `20261007-modernization-baseline` snapshot remains available. Media cleanup also produced a separate protected per-file recovery directory and completion manifest inside the staging runtime volume. No production site/database was modified.

Staging runs Laravel 13.35.0 on PHP 8.3.35, with the native administration migration applied and exactly one explicitly authorized administrator. Source stays read-only; the separate media mount is now writable for uploads. The old OpenCart environment keys were removed. Staging remains public without Basic Auth; real payments/callbacks, outgoing mail, tracking and the scheduler remain disabled.

After removing the exact disposable smoke fixtures, staging has 220 products, 15 categories, four pages, **87 orders**, 11 users, 316 media records (313 active), and 306 originals. The extra order relative to the original 86-order backup already existed before this feature deployment and was preserved. Both final cleanup audits report zero unused or duplicate candidates and the same ten previously absent originals.

Verification included authenticated admin screens, guest cart and order confirmation with payment suppression, real MP4 upload/byte-range playback, WebP generation and ETag 304 responses, anonymous admin redirects, and blocked importer/registration/callback endpoints. The staging container first passed the 60-test / 297-assertion feature suite. The final search regression covers sold-out cards plus hidden/archived exclusions: **61 tests / 309 assertions now pass locally, on staging and in CI**. After activation, public assets, actual search cards and authenticated admin screens were checked again. All 306 retained original paths and SHA-256 hashes match between local and staging.

Actual rendered card titles match for `трицикл` / `tricikl` / `ТРИЦИКЛ` (43), `количка` / `kolicka` (12), and `коцки` / `kocki` (39), on both local and public staging. A final smoke script initially counted only purchasable product links (12 / 1 / 38); sold-out cards have no such link. Comparing all card titles resolved that check discrepancy; no catalog data or visibility flag was changed. The operational verifier now applies the same active-product filter as the public search route.

Deployment lessons: the packaged app root must be mode 0755, while the archive and recovery data remain private. `scripts/package-release` now enforces that mode. In an SSH heredoc, noninteractive `docker compose exec -T` commands must receive `</dev/null` unless intentionally reading input, otherwise they may consume the remaining deployment script. Both issues were corrected and staging returned to service. The preceding feature app is additionally retained at `/srv/forkids-staging/backups/20261007-post-feature-0b96a50/app`; the final follow-up did not change the database schema or catalog data.

Remaining work is owner acceptance, replacement/recovery of the ten missing originals, provider-approved payment hardening/verification, and a separately planned production cutover. Customer accounts/social login and durable anonymous carts remain later phases.
