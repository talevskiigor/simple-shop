# Local, production and isolated staging environments

Current base-environment guide. The subsequent [payment/mail/backup/search update](payments-backups-search.md) supersedes older claims below that all payments, mail and scheduled jobs are disabled. Deployment history and checks are in [implementation-log.md](implementation-log.md); daily use and acceptance steps are in [admin-and-media.md](admin-and-media.md).

Implemented October 6, 2026, America/Phoenix (October 7 UTC), after the owner authorized using then deleting `ForKIDS.zip`, replacing the old Docker setup, and creating staging at `forkids.tail.mk`.

## What runs where

- **Local:** `http://localhost:8088`, Compose project `forkids-local`, PHP/Apache app and MariaDB containers. Only the app's port is published, bound to localhost. Database: `local_forkids`.
- **Deployed store:** `https://forkids.mk`, SSH `italevski@server.tail.mk`, root `/srv/forkids-staging`. `www.forkids.mk` and the previous `forkids.tail.mk` redirect browser requests to the main domain. An exact-host Apache vhost proxies to the PHP container at `127.0.0.1:8086`. The existing Cloudflare tunnel supplies public HTTPS. The storefront is publicly accessible; application admin authentication remains enabled.
- **Live database (legacy name):** `stg_forkids`, account `stg_forkids` restricted to that database on localhost. PHP connects through the server MariaDB Unix socket, mounted into the container. No new database network listener was exposed.
- **Current runtime:** PHP 8.3.35, Laravel 13.35.0, MariaDB 11.8.9 locally / 11.8.6 on staging, Node 24 for asset builds. The server's existing PHP 8.5 and other sites were retained. Docker was installed on the server; both PHP 8.3 runtimes passed the release suite.
- **Runtime image identities:** both environments use the tag `forkids-runtime:php8.3`. The verified local image ID is `sha256:ef519c014d342c4ca23e3563d040e6e69614979e436c5cc4f35ca27657532f62`; the running staging image ID is `sha256:7f501a68c4e0c3fa747f6d2b14f98b39db01d8199cb3f52f250267d514791333`. These are distinct builds, both checked with PHP 8.3.35 and the full suite. A shared tag does not prove identical image bytes; inspect and record the running image digest on each future release.

The restored baseline has been upgraded through Laravel 11 and 12 to 13, with updated lockfiles and native administration/media. The original production server files and database were not modified; the October 7 domain cutover makes this deployment available through the public store domain.

## Public-domain cutover — October 7, 2026

**Latest owner instruction:** live testing has ended. Production uses `APP_ENV=production`, `STORE_SANDBOX=false`, `PAYMENTS_ENABLED=true`, `STORE_ALLOW_INDEXING=true`, `STORE_TRACKING_ENABLED=false`, and a commented-out `PAYMENT_TEST_AMOUNT_MKD=1` reference. Full order totals apply. All tests must use local or independently isolated staging; `forkids.tail.mk` redirects to live. Internal staging names, including `.env.staging`, identify production and must not be treated as test targets. The details below record the initial cutover; its original noindex state is superseded by [the indexing operations guide](payments-backups-search.md#public-search-engine-indexing).

The owner authorized switching `forkids.tail.mk` to `forkids.mk` and adding `www.forkids.mk`, with email configuration explicitly preserved.

- Cloudflare Tunnel **USA-HOME** (`4ed6ca22-1c8e-44ff-b57d-f4801567c7c7`) has two additional published applications, `forkids.mk` and `www.forkids.mk`, both pointing to `http://127.0.0.1:80`. All pre-existing tunnel routes remain intact.
- The apex CNAME already pointed to that tunnel and was retained. The existing `www` CNAME was changed from DNS-only `forkids.mk` to the proxied tunnel target. No MX, TXT, mail-service CNAME, Email Routing or SMTP setting was changed.
- `/etc/apache2/sites-available/020-forkids.tail.mk.conf` remains the enabled filename. Its primary `ServerName` is now `forkids.mk`, with `www.forkids.mk` and `forkids.tail.mk` aliases. The tracked template is `deploy/staging/apache.conf`.
- GET/HEAD requests to either alias receive permanent **308** redirects to `https://forkids.mk`, preserving path and query. POST requests remain proxied so pending payment attempts with old signed callback URLs can still reach the application. Invalid unsigned callbacks continue to be rejected.
- Only `APP_URL` changed in the deployed environment, to `https://forkids.mk`; all other environment lines were compared and preserved. The app container was recreated and caches cleared. `APP_ENV=staging`, `STORE_SANDBOX=true`, `PAYMENT_TEST_AMOUNT_MKD=1` and the existing mail settings remain effective. This is a domain cutover, not evidence of completed bank acceptance or normal-price payment activation.
- Existing database, media, Compose project, runtime volumes, backup archive name/directory, cron and logrotate names are retained. Renaming these would add migration risk without changing the public URL. Visitors establish fresh hostname-specific cookies; old cookies are not transferred.
- Configuration rollback copies are private under `/srv/forkids-staging/backups/20261007-domain-cutover`. Restore its `app.env` and Apache configuration, recreate only this app, clear caches, validate Apache and reload if reverting the public hostname. Preserve newer orders and media; no database restoration is required for a hostname rollback.

Verified: public HTTPS storefront at the main domain, public HTTPS alias/old-host redirects preserving a login path and query, origin home/login 200, anonymous admin management redirect to the new login URL, and rejected old-host unsigned callback without redirect. The existing cron service invokes the scheduler every minute; backup/retention/monitor times remain 03:15/04:15/04:30 Europe/Skopje. A fresh full backup passed AES decryption, all original-media hashes and an isolated database restore; see the implementation log.

The historical initial-environment procedure below must be read with this section and the newer payment/mail/backup guide. In particular, its old statements that scheduling and payments are disabled no longer describe the deployed environment.

## Credentials and private data

This workspace contains `.private/access-credentials.json` with local/staging admin logins and staging DB credentials. It is mode 0600 inside a mode 0700 directory. Open it locally to retrieve passwords; do not paste them into chat, logs, tickets, commits, or command-line arguments.

Local settings are in `.env.docker`; the database root password is separately stored in `.private/database-root-password` and mounted as a Docker secret into the database only. `.env.staging` is the protected local copy of **live production** settings, despite its filename. The server uses `/srv/forkids-staging/secrets/app.env`. The former staging HTTP password file is no longer used.

The app login is `/admin/login`; there is no separate HTTP password prompt. Local retains its `admin@forkids.test` account. Production uses the two owner-requested administrators, `d.trpkovski@yahoo.com` and `igor.talevski@gmail.com`; their passwords were supplied by the owner and are not recorded here. The former temporary administrator and anonymized users have been removed from live. Historical credentials in the private workspace file must not be mistaken for current live access.

The ZIP, raw extracted SQL, and temporary deployment archives were deleted after verification. The anonymized `.private/sanitized.sql`, checksum manifest, restore reports, and private credentials are retained locally for recovery. Media is under `public/media/images` locally and `/srv/forkids-staging/media` on the server. These files are ignored by Git and must stay outside commits and Docker build contexts. Do not copy the backup's production `.env` or cached configuration into either environment.

## Local commands

Use `scripts/dev`, which explicitly selects `compose.yaml` and `.env.docker`; it never imports the older application/parent `.env` into Compose configuration.

```bash
# Fresh checkout only; refuses to overwrite existing credentials.
scripts/init-local
scripts/dev up -d --build
scripts/dev exec --user www-data app composer install --no-interaction --prefer-dist
scripts/dev --profile tools run --rm node

# Normal operation.
scripts/dev up -d
scripts/dev ps
scripts/dev logs --tail=100 app
scripts/dev exec --user www-data app bash
scripts/dev exec --user www-data app vendor/bin/phpunit --filter 'NavigationSearchTest|NonProductionSafetyTest'
scripts/dev exec --user www-data app php artisan schedule:list
scripts/dev exec --user www-data app php scripts/verify-release.php
scripts/dev stop
```

The `vendor`, `runtime`, `bootstrap_cache`, and `database` volumes persist. Source and original media are host files. Generated derivatives are written to `public/cached-media`. The app and Node tool containers mask `.private`; the runtime image build context permits only `docker/` files. Do not use `down -v`, global Docker cleanup, or the old importer to reset this environment.

For a **new, empty local database only**, obtain the private anonymized SQL and original-media copy through a secure channel, then restore:

```bash
scripts/dev exec -T db sh -c 'MYSQL_PWD="$MARIADB_PASSWORD" exec mariadb --user="$MARIADB_USER" "$MARIADB_DATABASE"' < .private/sanitized.sql
```

Check the target database and confirm it has no tables before importing; the dump includes DROP TABLE statements. Do not repeat this command against an established environment. Restore media to `public/media/images`, preserving nested paths, and check against the private manifest. Keep a generated admin password in a mode 0600 temporary file, then provision it without command-line exposure:

```bash
scripts/dev exec -T --user www-data app php scripts/sanitize-copy.php < .private/new-admin-password
```

Remove that temporary password file after saving the credential securely. The sanitizer rejects production and accepts only an isolated `local_forkids` or `stg_forkids` with `STORE_SANDBOX=true`; the currently live `stg_forkids` is not an authorized target. In an isolated copy it it preserves catalog and order counts, anonymizes customers and bank references, resets users, and clears sessions/tokens/queued jobs. It is an intentional copy-preparation operation, not a routine startup command.

## Staging layout and operations

The source templates are `deploy/staging/compose.yaml` and `deploy/staging/apache.conf`. On the server:

- `/srv/forkids-staging/app`: application source, locked Composer dependencies, and compiled assets; mounted read-only.
- `/srv/forkids-staging/app/docker/apache/site.conf`: read-only container Apache configuration mount. `STORE_ALLOW_INDEXING=true` removes its default noindex header while local environments remain excluded. The existing runtime image is unchanged; future builds include the same template. Apache's [conditional headers](https://httpd.apache.org/docs/2.4/mod/mod_headers.html) read the process environment with [`osenv`](https://httpd.apache.org/docs/2.4/expr.html#functions).
- `/srv/forkids-staging/media`: originals and new uploads, writable by container `www-data` (UID 1000).
- `/srv/forkids-staging/cached-media`: writable image derivatives.
- `/srv/forkids-staging/secrets`: environment, protected verification manifest, and Apache template; outside the web root.
- `/srv/forkids-staging/compose.yaml`: deployed Compose definition.
- `/etc/apache2/sites-available/020-forkids.tail.mk.conf`: enabled host vhost. Its ordering precedes the existing wildcard `*.tail.mk` site.
- Docker volumes `forkids-staging_runtime` and `forkids-staging_bootstrap_cache`: sessions, logs, compiled views, and Laravel bootstrap cache.

```bash
ssh italevski@server.tail.mk
cd /srv/forkids-staging
sudo docker compose -f compose.yaml ps
sudo docker compose -f compose.yaml logs --tail=100 app
sudo docker compose -f compose.yaml exec --user www-data app php artisan schedule:list
sudo docker compose -f compose.yaml exec --user www-data app php scripts/verify-release.php
sudo docker compose -f compose.yaml exec --user www-data app php artisan migrate:status
sudo docker compose -f compose.yaml exec --user www-data app vendor/bin/phpunit --do-not-cache-result --filter 'NavigationSearchTest|NonProductionSafetyTest'
```

The existing SSH configuration routes this hostname through Cloudflare Access. Large transfers disconnected repeatedly. On the same LAN, the verified direct connection used these options while retaining SSH authentication and the hostname's host-key check:

```bash
ssh -o ProxyCommand=none -o HostName=192.168.0.146 -o HostKeyAlias=server.tail.mk italevski@server.tail.mk
```

That LAN address is environment-specific. Away from the LAN, use the normal authorized connection and resumable transfers; do not disable host-key checking.

For subsequent application updates:

1. Review the diff, run isolated tests, install from `composer.lock` in PHP 8.3, and build assets with the Node service. Include the compiled `public/build` and that container's `vendor` directory in the prepared release; the local named vendor volume is distinct from the host's old vendor directory.
2. Package only reviewed application files, locked dependencies, and build output. Explicitly exclude `.git`, `.private`, dumps, ZIP files, real `.env` files, logs, sessions, and old bootstrap caches. Never send the whole working directory with an unrestricted recursive copy.
3. Transfer over SSH to the protected staging root, verify archive hashes, and preserve a previous application release for rollback. Keep database, media, secret files, and runtime volumes separate. The first deployment restored only an empty `stg_forkids` database; normal code updates must not reimport it.
4. Ensure empty mount targets exist in the prepared app: `.env`, `storage`, `bootstrap/cache`, `public/media/images`, and `public/cached-media`. Docker cannot create a missing nested mount target within a read-only source mount. The empty `.env` target is overlaid with `secrets/app.env` at runtime.
5. Activate the prepared source, then run `sudo docker compose -f compose.yaml up -d` and `... exec --user www-data app php artisan optimize:clear`. Rebuild/load the runtime image only if Docker/PHP configuration changed. Apply reviewed forward migrations only when the release requires them; never reset or seed this database.
6. Recheck anonymous storefront/assets access, admin routes redirecting guests to `/admin/login`, guest cart/confirmation, no payment form, blocked callbacks/import/registration, no scheduler, and expected record/media counts. Keep `STORE_SANDBOX=true`.

Apache config changes require `sudo apache2ctl configtest` before a graceful reload. Do not restart unrelated sites, enable server PHP 8.5 for this app, change existing MariaDB listeners, or modify the Cloudflare tunnel token. Staging has no scheduled backup job; preserve database/media externally before future migrations. Its writable cached-media directory and runtime volumes are disposable only after checking session/log retention needs; originals are not.

## Restored snapshot and media exceptions

The supplied archive's CRC check passed. Its DB member was `db-dumps/mysql-ss_db-2026-10-06-06-00-03.sql.gz`; originals came from `var/www/forkids.mk/image/catalog/`. Production configuration was not reused. The 198 compared application/routes/configuration/migration/view/manifest files matched the inspected source baseline. Current production may have changed after the backup.

At initial restoration, both copies contained 220 products, 15 categories, 1,806 media rows, four pages, 86 orders, and 11 users, with 15 historical migrations. The native administration migration is now also applied. Current local/staging totals are 220 products, 15 categories, four pages, 11 users (one administrator), and 316 media rows (313 active). Local has 86 orders; staging has 87, including one additional row that already existed before the feature deployment and was preserved. Customer names, addresses, phones, emails, comments, passwords/tokens, and bank references were anonymized or cleared; product data, historical order totals/items, payment-state flags, and stock were retained. The temporary smoke-test order in each environment was removed after validation.

All 384 supplied original files (385,940,978 bytes: 382 images and two HTML placeholders) were initially recovered and checked by SHA-256 on local and staging. After reviewed cleanup, each environment retains 306 originals; 74 unused files and four duplicate copies were archived/removed, reclaiming 38,217,946 bytes. The final audits find no further unused/duplicate candidates. No video files were present. Apache denies serving HTML/executable content from the media tree. Product cover/gallery references use 313 distinct paths, including an empty path. There are 50 missing reference occurrences: four empty values and references to the following ten absent files. Twenty-seven occurrences belong to in-stock products.

```text
images/4d01d5944784021649646ff32fbb494d.webp
images/Britax-Infant-Car-Seat-7.webp
images/IMG_0197-128X_null_100.webp
images/MODEL-227-–-DECIJI-AUTO-crveni-1-600x600.webp
images/MODEL-413-–-TRICIKL-PLAYTIME-„RELAX-bez.jpg-1-600x600.webp
images/britax_b_agile-2016-20.webp
images/britax_b_agile-2016-4.webp
images/britax_b_agile-2016-5.webp
images/traktor.webp
images/trotinet-jednobojni-655-zuti-cene.webp
```

These filenames were checked against the entire supplied ZIP and were absent; the old application's media directory in the backup contained no alternative originals. Preserve the references and recover the missing files from a later live-media copy. Do not silently substitute, delete gallery rows, or rerun OpenCart import. The later cleanup also scanned archived records, descriptions/pages, order snapshots and templates, normalized imported HTML, and retained compatibility aliases for published duplicate paths.

## Safety controls and validation

`STORE_SANDBOX=true` enables the early `NonProductionSafety` middleware. It blocks `/update`, `/bank/*`, and `/admin/register` before controller/database mutations. `CaSys::get` refuses to generate payment fields, confirmation renders a test-environment notice, and a response policy restricts form submissions to the same origin. Existing production behavior remains the default when the flag is absent. Meta Pixel is omitted in sandbox layouts.

Both environments use log mail, local storage/cache/sessions, and Scout's collection driver; they have no live OpenCart, Google Drive, SMTP, or Meilisearch credentials. Scheduled tasks return early in sandbox mode, and no worker/scheduler service is configured. Collection search uses the model's ASCII-normalized fields, allowing Macedonian Cyrillic and equivalent Latin queries to match. It reads the catalog into memory for each query, which is acceptable for this 220-product baseline; revisit an isolated Meilisearch index as the catalog grows. This does not establish Meilisearch typo-tolerance or relevance parity. The source still contains legacy hardcoded secrets and security findings; no claim of production remediation is made.

Verified release results:

- Composer strict manifest validation and full Composer/npm audits pass; no advisories were reported. Node 24 builds the locked frontend. Bootstrap emits Sass deprecation warnings, without build failures.
- The full suite passes locally, on staging and in CI: **61 tests / 309 assertions**. The packaged release was activated and checked at revision `42144f7`; later documentation-only revisions are recorded by the deployed `RELEASE` file. The test bootstrap forces SQLite `:memory:` before migrations.
- Public home/search/category/product/page and representative assets/media work. Admin access requires an explicitly authorized account; public registration/import are absent. The storefront has no HTTP authentication challenge.
- Authenticated administration, guest cart/order confirmation with payment disabled, actual video upload/range requests, image generation and ETag responses were exercised. Disposable browser and checkout/upload fixtures were removed afterward.
- The scheduler reports no tasks. Payment-provider acceptance and current production security findings remain separate work.

## Cyrillic and Latin navigation search

### Behavior and implementation

The main navigation submits `GET /search?find=...` through the **Барај** button or Enter key. `routes/web.php` normalizes the query with `Str::ascii`. `Product::toSearchableArray()` normalizes the product name and description in the same way. With **`SCOUT_DRIVER=collection`**, Scout applies those model transformations before comparing the text, case-insensitively. Cyrillic names stay stored/displayed as Cyrillic; no catalog text or schema was rewritten.

The initial `SCOUT_DRIVER=database` setting was incompatible with this route: its SQL engine compared a Latin-normalized query against original Cyrillic columns, so valid searches could return no matches. Both private environment configurations and `scripts/init-local` now select `collection`. When changing an existing environment setting, recreate its app container to refresh process variables and clear Laravel's configuration cache; changing the environment file alone is insufficient.

Supported and verified behavior includes Cyrillic and corresponding Latin names/descriptions, upper/mixed case, English brand text, surrounding whitespace, empty searches, and the **Нема резултати.** message for no matches. Latin equivalence follows Laravel's `Str::ascii` transliteration. The model's existing searchable fields are name, description, ID, and price; a separate model/SKU search field has not been added.

Search retains the existing stock behavior: result cards may include sold-out products, while the home/category pages filter for stock. Hidden and archived products are excluded everywhere. An empty query returns the visible search catalog. Count all card titles when verifying results: sold-out cards do not have a clickable product link. The driver loads the catalog into memory on each search; this is the baseline for 220 products, with an isolated Meilisearch index to reassess as the catalog grows. Collection search is substring matching; typo tolerance, stemming, relevance ranking, and production Meilisearch parity are not established.

### Verified examples on the restored snapshot

The following counts refer to result cards, including sold-out products, and can change after catalog edits. Each pair returned identical cards on both local and public staging:

- `трицикл`, `tricikl`, and `ТРИЦИКЛ`: **43** matching cards.
- `количка` and `kolicka`: **12** matching cards.
- `коцки` and `kocki`: **39** matching cards.
- `nonexistent-product-zzzz`: no product cards and the visible no-results message.

Browser verification submitted a Cyrillic query with **Барај**, then a Latin query using Enter. Direct HTTP checks compared actual card titles on both environments. The original smoke check had checked only HTTP 200 and missed the bug; success status alone is not a search acceptance criterion. Private `.private/search-verification.json` retains the comparison results.

### Regression coverage

`tests/Feature/NavigationSearchTest.php` creates two synthetic products in guarded SQLite `:memory:`. Its eleven cases assert matching result IDs and rendered content for Cyrillic/Latin names and descriptions, upper/mixed case, English brand names, whitespace, no matches, the navigation form/empty query, and sold-out inclusion with hidden/archived exclusion. `tests/Feature/NonProductionSafetyTest.php` adds six environment-safety cases.

```bash
scripts/dev exec --user www-data app vendor/bin/phpunit --filter 'NavigationSearchTest|NonProductionSafetyTest'
```

The original combined search/safety suite passed **16 tests with 54 assertions** on both copies. The new sold-out/visibility case adds one test and 12 assertions; the full current suite has 61 tests and 309 assertions. Tests do not use or reset either restored MariaDB database. Preserve these checks when upgrading Laravel/Scout or switching search drivers; verify result parity rather than relying on a page loading successfully.

## Old Docker cleanup and next step

The outer `/home/unknown/Code/simple-store` repository is separate from the application. Its obsolete Compose, Ubuntu/Xdebug/supervisor files, Meilisearch Dockerfile and old environment template were removed. Its convenience scripts call `www/html/scripts/dev`. The obsolete tracked `opencart.sql` was privately archived and removed. These changes were committed/pushed as `f7ef363` and `ad5a8f1`. Its old `.env`, legacy volumes, unrelated containers and untracked owner-maintained `repair.sh` were left untouched.

Next is owner acceptance using [the admin guide](admin-and-media.md#next-acceptance-stage), followed by missing-media recovery and provider-approved payment/cutover work. The framework and authoring implementation is complete.

## Native administration deployment procedure

After a clean committed checkout, `scripts/package-release` creates `.private/staging-release.tar.gz` from Git, the running container's locked vendor volume, and built frontend assets. It excludes secrets/dumps/media/runtime data by construction. A `RELEASE` file records the exact commit. The archive root is explicitly mode 0755 so Apache can read it after extraction; the archive itself remains mode 0600. Verify the printed SHA-256 after transfer. In a remote SSH heredoc, append `</dev/null` to `docker compose exec -T` commands that should not consume script input.

For the native-media release, install the updated `deploy/staging/compose.yaml`: the separate media mount becomes writable by container `www-data` (UID 1000). Keep application source read-only and all ports loopback-only. Preserve a private DB/media/app snapshot, enter maintenance mode, activate the prepared release, recreate only the staging app container, clear caches, and apply forward migrations. Run `admin:manage admin@forkids.test --promote-existing` once to authorize the restored staging administrator; no other restored accounts are promoted. Do not run the sanitizer again.

Run `media:reconcile` first in dry-run mode. With maintenance mode active, apply it with a new private path under `storage/app/media-recovery`, then repeat the dry run. Verify the copies/checksums/completion manifest before clearing legacy derivative caches. Return the application to service, run `scripts/verify-release.php`, and check the full test suite inside the staging container (the test bootstrap still forces in-memory SQLite). Inspect public home/search/media, unauthenticated admin redirects and authenticated admin/media APIs; keep sandbox payment/mail/scheduler controls enabled.

Rollback before accepting user edits: stop only the staging app, restore the matching database/media/app snapshot from `/srv/forkids-staging/backups/20261007-native-admin-predeploy` (the immediately preceding release; the earlier `20261007-modernization-baseline` is also retained), restore the old Compose media mount if required, then recreate the staging app and clear caches. Once new data has been entered, take a fresh backup and reconcile those edits before a database rollback. Do not use a blind `migrate:rollback` as a substitute for a matched database/media recovery.
