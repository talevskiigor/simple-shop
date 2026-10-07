# Modernization implementation log

## Production payment mode and test-data cleanup — October 7, 2026

The owner ended live testing, requested full-price payments, two named administrators and removal of identified test data. The supplied password has no trailing space. Source now rejects the 1-denar override and reuse of old test attempts in production, preserves private-page noindex and payment form controls after sandbox mode is disabled, and keeps tracking disabled independently. HTTPS URL generation follows the configured canonical URL in production as well as staging.

The test bootstrap rejects an inherited production environment before applying isolated test settings. PHPUnit no longer overwrites that environment before the guard can inspect it. The copy sanitizer explicitly rejects production. All future tests and synthetic workflows must use local or independent staging; the old staging hostname redirects to live, and server/database/backup names retain their historical staging labels.

Local verification passed **79 tests / 420 assertions**, covering normal production totals, refusal of test mode and old attempts, and private indexing/form controls. A separate invocation of the actual PHPUnit runner with `APP_ENV=production` was refused locally before tests ran. No test suite or synthetic checkout was run on live for this release.

Before cleanup, full encrypted backup `ForKIDS-Staging_2026-10-07-17-36-51.zip` completed at **339,955,100 bytes**. All **628** file entries decrypted with AES-256; all **306** original media hashes and the current environment matched, and the compressed SQL was valid with no cross-database directives. The previous app, environment, Compose and host Apache configuration are also retained privately under `/srv/forkids-staging/backups/20261007-live-cutover`. Deployment and cleanup results will be recorded below after activation.

## Public indexing with 1-denar payments retained — October 7, 2026

The owner explicitly requested enabling search-engine indexing while leaving the real-card 1-denar charge in place for their manual acceptance test. Added `STORE_ALLOW_INDEXING`, independent of sandbox/payment configuration, with a disabled default for local/test environments. The host Apache override was removed and the container's default header now follows this setting. Compose mounts the versioned container Apache configuration read-only, preserving the change across recreation without replacing the runtime image. Admin/cart/order/bank/payment-result responses retain noindex, and `robots.txt` excludes these paths for all crawlers. No mail or payment settings were changed by the implementation.

Added a dynamic `/sitemap.xml` containing current active products, categories and published pages on the configured domain; hidden/draft/archived records are excluded and visible sold-out products remain listed. Retired the unused static `dummy:run` generator. The obsolete local generated sitemap, containing localhost URLs, was preserved privately so it cannot shadow the dynamic route. The deployed tree had no static sitemap file.

Local and deployed verification passed **77 tests / 411 assertions**, including current sitemap membership, private-page exclusion, preserved sandbox/admin controls and a 1-denar guest checkout that keeps the original total and uses `https://forkids.mk` bank return URLs. Both Apache syntax checks passed. The owner's manual bank transaction remains pending; these tests do not claim a completed card charge.

Implementation revision `1ae189c` was deployed after preserving the previous full application, environment, Compose and host Apache configuration in `/srv/forkids-staging/backups/20261007-indexing-predeploy`. The deployment added only `STORE_ALLOW_INDEXING=true` to the environment; all other lines were compared against the recovery copy and matched, including payment, email and backup settings. The existing app container was recreated with the Apache configuration mount, caches cleared and host Apache gracefully reloaded. Runtime image, database and media were unchanged; no migrations were needed.

Public checks through Cloudflare returned HTTPS 200 without `X-Robots-Tag` for `/`, `/robots.txt` and `/sitemap.xml`. The XML parsed successfully and contained **241 canonical URLs**, all on `https://forkids.mk`, with no private workflow URLs. Admin login and cart returned 200 with noindex; empty checkout redirected with noindex. Local home, sitemap and robots remained noindex. Effective deployed payment configuration confirmed enabled 1-denar mode, present merchant configuration, and both callback URLs on `https://forkids.mk`. No card was entered or charged.

## Public-domain cutover and backup verification — October 7, 2026

The owner authorized replacing the staging hostname with `forkids.mk`, adding `www.forkids.mk`, and verifying scheduled backups. They expressly required email configuration to remain unchanged. The existing deployment/database/media were retained; original production files and data were not modified.

Cloudflare's existing **USA-HOME** tunnel now publishes `forkids.mk` and `www.forkids.mk` to the existing Apache service. Its prior twelve routes were preserved. The apex DNS record already targeted this tunnel; only the existing `www` DNS record required a change to a proxied tunnel CNAME. Email Routing, MX/TXT records and SMTP2GO-related CNAME records were left intact. The server environment was changed only at `APP_URL=https://forkids.mk`; all remaining lines, including all mail settings, were compared and preserved.

The tracked Apache template uses the apex domain with aliases for `www` and the old staging hostname. Both aliases return 308 for GET/HEAD and preserve the requested path/query. Other methods still reach the app, preserving old signed bank POST callback endpoints. Apache configuration validation passed, the app was recreated, and application caches were cleared. There were no migrations, order changes or media changes.

Verification: public HTTPS storefront rendered with new-domain links; public `www` and old-host login URLs redirected to the same new-domain path/query. Origin checks returned 200 for home/login, 302 from unauthenticated admin management to `https://forkids.mk/admin/login`, and 308 for both aliases' search URL with its query intact. An unsigned POST to the old bank callback returned 422 without redirect. One-denar payment mode and noindex/tracking controls remain enabled; the earlier provider acceptance blocker is not resolved by these checks.

Before the cutover, encrypted full backup `ForKIDS-Staging_2026-10-07-16-44-35.zip` completed at **339,948,107 bytes**. All **627** file entries decrypted with AES-256; all **306** original-media hashes and the environment file matched. The contained database restored successfully into a new disposable database: products 220, categories 15, pages 4, orders 89, users 11, media 316 and payment attempts 2, matching all seven source counts. The disposable database and decrypted temporary files were then removed. Existing encrypted backups and recovery snapshots were retained.

The cron service is active, `/etc/cron.d/forkids-staging` is installed with its every-minute scheduler command, and recent cron journal entries confirm actual invocations. The exact scheduler command also succeeded manually after container recreation. Daily jobs remain **03:15 backup, 04:15 retention, 04:30 health check, Europe/Skopje**. Backup health passed. Cron/logrotate files remain root-owned 0644, with the environment root:1000 0640 and archives private. Same-host storage is verified; an independent off-host backup is still not configured.

Private configuration rollback copies are in `/srv/forkids-staging/backups/20261007-domain-cutover`; the initial full-file backup also preserves source and database/media. Runtime paths, `stg_forkids`, backup directory/archive names, scheduler names and cookies/session configuration were not renamed. New-domain visitors receive fresh hostname-specific cookies.

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

### Payment testing, mail copies, backups and suggestions — October 7, 2026 UTC

Owner authorization extends staging to real-card one-denar tests, application mail copies to `igor.talevski+forkids@gmail.com`, scheduled backups and live navigation suggestions. See [the operating guide](payments-backups-search.md). Earlier blanket sandbox-payment/mail/scheduler statements describe the previous release. Production remains unchanged.

Implemented immutable payment attempts, optional one-denar total override without catalog/order-price changes, environment-owned merchant credentials, signed return verification, duplicate handling and explicit admin reconciliation. The documented bank response does not independently sign result routing; returns do not auto-fulfill orders. Test confirmations do not decrement stock. Normal admin-confirmed payments update stock atomically once.

Global mail BCC preserves recipients and avoids duplicates. Backup jobs are explicit, isolated and encrypted on staging with a dedicated host cron/retention configuration. Live suggestions support Cyrillic/Latin, thumbnails, six-result popup, keyboard selection/completion and stale-request cancellation. Local DB-only backup execution passed. Deployment, provider-form and encrypted restore evidence follow after verification.

Local verification: 74 tests / 386 assertions pass; route compilation and Composer strict validation pass. Frontend build succeeds. Browser QA confirmed six Cyrillic/Latin suggestions, all-results link, popup layout and Down/Tab name completion. The local forward migration and database-only backup both completed without changing catalog data.

### Staging payment/mail/backup/search verification

Release `9418b52478ea26495028f98de1c5db3a37aa9916` was committed, pushed and activated; [CI passed](https://github.com/talevskiigor/simple-shop/actions/runs/37635170201). The staging suite also passed **74 tests / 386 assertions**. Recovery snapshot `/srv/forkids-staging/backups/20261007-payment-search-predeploy` contains the prior app, database, media, environment, Compose file and checksums. Automatic deployment review initially cited the obsolete blanket sandbox rule; deployment was approved after rechecking the owner's latest request and the updated project instructions. Original production was not changed.

Before the synthetic checkout, staging retained 220 products, 15 categories, four pages, 87 orders, 11 users, 316 media rows and 306 original files. Browser verification confirmed the supplied administrator credentials and matching six-item Cyrillic/Latin search suggestions with 43 total results. Admin's old fixed “payments/email disabled” banner was corrected to reflect the effective payment mode and mail transport.

Full backup `ForKIDS-Staging_2026-10-07-14-24-20.zip` is **339,945,282 bytes**. All 627 file entries use AES-256 and were successfully decrypted; all 306 original-media hashes and the environment file matched. Its SQL was restored into a new disposable database, and all seven checked table counts matched, including zero payment attempts at snapshot time. The disposable database and decrypted temporary files were removed. The initial full-backup permission failure was fixed by keeping the environment root-owned and granting read-only access to the app group (`root:1000`, `0640`). Backup health checks pass.

The dedicated `/etc/cron.d/forkids-staging` and `/etc/logrotate.d/forkids-staging` are installed; the host cron service is active, and the exact scheduler invocation passed. Daily backup runs at 03:15, retention at 04:15 and health checking at 04:30 **Europe/Skopje** (the UTC schedule display is two hours earlier in October). These are encrypted same-host backups; off-host disaster recovery is still a separate task. SMTP accepted the explicit verification message addressed only to `igor.talevski+forkids@gmail.com`; this confirms service acceptance, not inbox placement.

Guest browser checkout used real product 273 and synthetic delivery details. The order retained **5,600 MKD**, while payment attempt reference **190384DF5C** requested **100 minor units = 1 MKD**, with test mode enabled. Synthetic order 90 is retained for diagnosis/audit, bringing the order count to 88; its attempt remains pending, without a bank reference, fulfillment or stock change. No card data was entered and no charge was made.

**Payment acceptance remains blocked at the provider:** cPay displayed “Грешка при процесирање на страната. Грешката е регистрирана.” at its current vPOS error page before card entry. Its legacy bridge was independently checked and preserved all signed fields, adding only `isSimple` and `OriginalReferrer`. The error gives no specific cause. The merchant's current redirect template, integration specification/provider log and staging-domain approval must be checked before calling the real payment flow verified. Signed callbacks and reconciliation pass automated tests with synthetic credentials; they have not yet been exercised by a real bank transaction.
