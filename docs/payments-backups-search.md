# Payment testing, outgoing mail, backups and live search

## Authorized operating changes

**Current production instruction — October 7, 2026:** the owner has ended live testing. `forkids.mk` now uses `APP_ENV=production`, `STORE_SANDBOX=false`, enabled payments at full order totals and public indexing. `PAYMENT_TEST_AMOUNT_MKD` is commented out, with its `=1` reference retained only for future local/isolated staging. Production rejects a configured test amount and reuse of a prior test attempt. PHPUnit also refuses a production environment before applying test overrides.

Live administrators are `d.trpkovski@yahoo.com` and `igor.talevski@gmail.com`, provisioned with the password supplied privately by the owner, without a trailing space. No password is stored in documentation. The temporary `admin@forkids.test` and anonymized copied accounts are removed from live. Identified test orders and payment attempts are privately archived before deletion, including the bank reference on the returned-success test payment; this does not refund or reverse a bank transaction. The unclassified unpaid order #88 is preserved pending the owner's identification.

The older real-card staging authorization below is historical and applies only to isolated environments in future. Do not run tests, create fixtures, or make test payments on production. `forkids.tail.mk` redirects to live; there is currently no independent staging deployment at that address. Internal deployment/database/backup names still contain `staging` and must never be used to infer safety. Email configuration and DNS are preserved. The internal `APP_NAME` remains unchanged because the existing mail sender name references it. `STORE_TRACKING_ENABLED=false` retains the previously disabled tracking after promotion.
## Public search-engine indexing

`STORE_ALLOW_INDEXING=true` permits indexing of the public storefront, categories, products, published information pages and media. It is independent of `PAYMENTS_ENABLED`, `PAYMENT_TEST_AMOUNT_MKD` and `STORE_SANDBOX`; do not turn off the sandbox or test amount just to enable indexing. Set the value to the literal lowercase `true`, as both Laravel and Apache read it. Local/test environments keep the default `false`.

The host proxy no longer forces a global noindex header. The app container uses the environment-controlled Apache template, mounted read-only through Compose so this behavior persists across recreation. Laravel still sends noindex on admin, cart, order, bank and payment-result responses. `robots.txt` permits public crawling and excludes those private paths for all crawlers.

`GET /sitemap.xml` is generated from current active products (including publicly viewable sold-out products), non-archived categories and published pages. Hidden/archived products and draft/archived pages are excluded. The old unused `dummy:run` static generator was retired. Do not place an old static `public/sitemap.xml` in a release, as Apache would serve that file instead of the current route. Indexing permission is not a guarantee of when search engines will crawl or list the site.

## One-denar payment switch — local or isolated staging only

```dotenv
PAYMENTS_ENABLED=true
PAYMENT_TEST_AMOUNT_MKD=1
CPAY_MERCHANT_ID=your-merchant-id
CPAY_MERCHANT_NAME="your-registered-merchant-name"
CPAY_SECRET=your-private-checksum-key
```

`PAYMENT_TEST_AMOUNT_MKD=1` charges **one denar for the whole order**, represented as `AmountToPay=100` in cPay. Production keeps this variable commented out for normal totals and rejects any active 1-denar override. Any other nonempty value is rejected. Product prices, discounts, cart contents and the order's original total remain unchanged. The confirmation page prominently identifies a real-card test and the 1-denar charge. Card data is entered only on cPay, never on this application.

Payment attempts have unique ten-character references, fixed charge amounts, test flags, immutable outgoing fields and random return tokens. Changing the environment afterward does not change an existing attempt. Edited customer/cart details start a separate order when the previous snapshot has already been prepared for the bank. Repeated unchanged confirmation reuses the attempt. Normal totals must be whole MKD as required by the merchant protocol; no silent rounding is performed.

The formerly hardcoded merchant key is now read only from the environment. Staging uses the existing merchant configuration privately. Public source history still contains the previous key: coordinate bank rotation before any production cutover. Do not put a secret into a browser field, code, test fixture or log.

After changing environment settings, recreate the app container and clear Laravel configuration caches. Local remains payment-disabled by default; tests force fake payment settings and cannot use the inherited real key.

## Bank return and confirmation

The new callback verifies the return checksum, declared parameter lengths/order, exact amount/currency/merchant/reference and all original fields, plus the per-attempt token. It rejects missing signatures, modified amounts and reflected outgoing checksums. Bank returns are stored under a transaction/row lock; duplicates are harmless and a later failure cannot downgrade a successful or confirmed receipt. No browser session is required. A 303 redirect restores a normal first-party result page; it does not expose customer contact details.

The available cPay v2.9 protocol signs the echoed payment fields but does not independently sign the success/failure URL selection. A verified browser return is therefore recorded as **returned_success**, pending confirmation in the merchant portal. It is not automatically treated as a fulfilled sale. In **Admin → Orders**, match the bank reference and actual charge in cPay, tick the explicit verification checkbox, then confirm. The audit records the administrator and time. A normal confirmed order updates stock once, under locks, and refuses a stock-shortage/double-completion condition for manual reconciliation. A test confirmation never marks the normal order fulfilled or changes stock.

This deliberately avoids pretending that a browser redirect proves settlement. A future authenticated provider status API/push integration can automate reconciliation once its current merchant-specific credentials/protocol are available. Stock reservation, refunds and complete production concurrency acceptance remain separate work.

Test procedure (isolated staging only; never on `forkids.mk` or its aliases):

1. Add real catalog products, complete guest delivery details, and check both the original total and the **1 denar total charge** notice.
2. Open the bank's hosted card page. Confirm its displayed amount before entering your own card. The developer checks do not submit a card or make a charge.
3. Complete/cancel payment and return. Check the attempt reference in Admin → Orders and compare it with cPay's actual transaction record.
4. Confirm the test receipt after checking cPay; verify stock and original prices are unchanged. Keep the real bank reference for reconciliation. The owner-authorized production cleanup preserves this evidence in a private encrypted archive before removing the live test rows; never fabricate a paid result.

New payment attempts use `https://forkids.mk` for bank return URLs; existing attempts preserve their original signed fields and old-host POST callbacks remain reachable. Never bypass the bank's domain checks or spoof a referrer. A successfully generated form is not evidence of a completed card payment. The release log records how far the hosted-bank check reached.

**Historical developer provider check, before the domain switch (October 7, 2026):** the hosted check reached `https://vpos.cpay.com.mk/mk-MK/ErrorHandle/Error`, displaying a generic processing error before card entry. The public legacy gateway forwards to `https://vpos.cpay.com.mk/mk-MK`; a diagnostic confirmed every signed field survives unchanged, with the provider adding `isSimple` and `OriginalReferrer`. This does not identify the underlying merchant/configuration error. Obtain the current **Redirect code template**, **Redirect Integration Specification**, and provider error details through the merchant portal, and check that the staging domain is approved. Do not claim a successful real payment until the owner completes and reconciles one. No card was entered or charged during developer verification.

References: [cPay's published amount/redirect specification](https://www.cpay.com.mk/repository/documents/cPay_Merchant_Params.pdf), [cPay-authored v2.9 specification mirrored on Scribd](https://www.scribd.com/document/526497083/cPay-Merchant-integration-specification-v2-9), [merchant portal](https://merchant.cpay.com.mk/en-US). Reconcile against the bank's current merchant documentation before production.

The provider's [current merchant-module instructions](https://merchant.cpay.com.mk/repository/documents/Instructions%20for%20Merchant%20Module-EN.pdf) explain downloading the merchant-specific integration documents under **Documentation** after login.

## Copies of outgoing email

`MAIL_COPY_TO=igor.talevski+forkids@gmail.com` adds BCC to every application message at Laravel's `MessageSending` event. Original To/CC/BCC recipients remain intact; the copy is not added twice if already present. It covers contact mail, password/verification mail and backup notifications, including queued mail when sent. It does not control emails generated independently by cPay/the bank. Leave it empty to disable copying.

Staging SMTP settings use the existing configured mail service, transferred privately. Local/tests still log/capture mail. Backup notices go directly to the same address, without duplicate BCC. An explicit delivery check to that address is part of deployment; acceptance by the SMTP service does not prove inbox placement.

## Scheduled backups

```dotenv
BACKUPS_ENABLED=true
BACKUP_NAME=forkids-staging
BACKUP_DISKS=backups
BACKUP_TIME=03:15
BACKUP_TIMEZONE=Europe/Skopje
BACKUP_NOTIFICATION_EMAIL=igor.talevski+forkids@gmail.com
BACKUP_ARCHIVE_PASSWORD=private-generated-password
```

The dedicated server cron entry in `/etc/cron.d/forkids-staging` invokes Laravel's scheduler every minute inside the PHP container. Jobs run at **03:15 backup, 04:15 retention cleanup, 04:30 health check**, Europe/Skopje. Locks prevent overlapping execution. The application default leaves scheduling disabled until explicitly configured. Templates are `deploy/staging/cron` and `deploy/staging/logrotate`.

Encrypted ZIP archives live at `/srv/forkids-staging/scheduled-backups/forkids-staging`, mounted at `storage/app/backups`; permissions are private and the directory is outside the web root. The decryption password is in the private credentials file under `staging_backup_archive_password`. They include the database, application/configuration and original media, including future uploads. The live cleanup archive is separately retained at `/srv/forkids-staging/scheduled-backups/recovery-20261007-production/test-records.json.enc`; decrypt it with Laravel Crypt using the matching private application key. It is outside automatic ZIP retention. They exclude runtime sessions/logs, Git, private workspace data, dependency directories and regenerable image derivatives. Compiled frontend assets are included. Recover Composer dependencies from the committed lockfile; the release marker and deploy templates are backed up with source.

The host `secrets/` directory remains private. Its mounted `app.env` must be owned by `root:1000` with mode `0640`, so the container's `www-data` group can read it for encrypted backup; the mount remains read-only. Root-only `0600` still permits injected environment variables but makes full-file ZIP creation fail. Keep backup destination directories `0700`, archives `0600`, and the host scheduler/logrotate files `root:root 0644`.

Retention keeps all backups for seven days, daily copies for fourteen days, weekly for four weeks and monthly for two months, capped at 8 GB. The newest backup is retained. These scheduled copies are on the staging host; independent off-host disaster recovery remains to be configured separately. Existing pre-deployment/manual recovery snapshots are outside this retention policy and are not removed.

Manual commands from `/srv/forkids-staging`:

```bash
sudo docker compose exec --user www-data app php artisan backup:run
sudo docker compose exec --user www-data app php artisan backup:list
sudo docker compose exec --user www-data app php artisan backup:monitor
sudo docker compose exec --user www-data app php artisan schedule:list
```

The host scheduler log is `/var/log/forkids-staging-scheduler.log`; job output is in the runtime `storage/logs/backups.log`. The host log has rotation. Inspect failures and verify a recent archive can be decrypted, that its database dump parses and that its media hashes match, before relying on recovery. Restore into an isolated empty database, never over active orders. Run forward migrations only after checking the recovered release/schema. Record each restore rehearsal in the implementation log.

## Main navigation suggestions

Typing at least two characters opens up to six matching product suggestions with thumbnail, name, price/availability and a link to all results. Requests wait 220 ms after typing and cancel stale responses. Cyrillic and corresponding Latin text share the same normalization as full search. Hidden/archived products are excluded; sold-out items remain identified. Dynamic labels use DOM text, never injected HTML.

Use Up/Down to select, Enter to open, Tab to complete a selected name, and Escape to close. Clicking a result opens its product page; ordinary Enter/Барај still performs the full search. Blur/outside clicks close the popup. The endpoint validates input, limits results, is rate-limited and uses no-store responses. Without JavaScript or on request failure, the existing search form still works.
