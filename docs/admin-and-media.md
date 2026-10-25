# Native administration and media

> **Current live operation:** use [the live admin login](https://forkids.mk/admin/login) with `d.trpkovski@yahoo.com` or `igor.talevski@gmail.com` and the password supplied by the owner. The temporary test administrator is removed. Live payments use full order totals. Keep all testing and disposable content in local or independent staging; the old `forkids.tail.mk` address redirects to production. See [current operations](payments-backups-search.md).

## Daily management

Open `/admin/login` and use an explicitly provisioned administrator. There is one account type and no roles system. Public registration is removed. Existing restored accounts do not automatically gain access. The restored `admin@forkids.test` now belongs only to local development; its old live credentials are retired.

- **Products:** add/edit name, stable URL, model/SKU, MKD price, percentage discount, stock quantity, visibility and categories. Select one or more media files; the first must be an image and becomes the cover. Move gallery items with the arrows. Videos may follow the cover. Zero stock means unavailable; hidden products are excluded from browsing/search/direct product URLs. Archive removes a product from the storefront while retaining media and order history.
- **Categories:** create/edit categories. Move active products before removing their category. Preserve existing URL names to retain shared links; renaming URLs intentionally changes their address.
- **Pages:** edit with the visual editor or HTML view, insert library images/videos, and save as draft or published. Published pages appear in the storefront footer. Drafts are not publicly accessible.
- **Media:** upload JPG, PNG, WebP, GIF, MP4 or WebM; maximum 32 MB, images maximum 20 megapixels. Files use content-hash names; repeat uploads reuse the same original. Name and alternative text are editable. Files referenced by catalog content, archived content, templates or orders cannot be removed. Library removal archives the row; physical files are handled through audited cleanup.
- **Administrators:** add/edit administrator accounts and passwords (minimum 12 characters). Administrators have full store management access. You cannot delete your own account or the last administrator. A changed password invalidates the affected account's existing authenticated sessions through Laravel's session-authentication middleware.
- **Orders:** review actual order snapshots and verify returned bank payments in the merchant portal before explicit administrator confirmation. Normal confirmed orders update stock once. A browser return alone is not settlement; see the payment operations guide.

The editor uses locally bundled Tiptap 3 (MIT), including headings, lists, links, tables, undo/redo and media insertion. No Tiny Cloud subscription/key is required. Symfony HTML Sanitizer runs on saved content and on storefront rendering; scripts, event handlers, unsafe links and unsupported active embeds are removed. Existing imported HTML is normalized during media reconciliation. Backups retain pre-normalization content. GIF thumbnails show the first frame; originals remain available. Videos are stored and played directly, without transcoding.

First-admin provisioning on an empty installation:

```bash
scripts/dev exec --user www-data app php artisan admin:manage owner@example.com --name="Store owner"
```

Enter the password at the hidden prompt. To authorize an existing account deliberately, add `--promote-existing`; this does not change its password. Do not run the old importer or a database reset. The default seeder intentionally does nothing.

## Image delivery

`App\Services\ImageVariants` replaces the old Intervention Image 2 resizer. Existing `/media-resize/images/...` URLs remain valid. Sources are constrained to the media root, including checks against traversal and symlink escapes. Supported raster sources are JPEG, PNG, WebP and GIF. Image requests reject dimensions outside 1–1600, round to documented size buckets in `config/media.php`, preserve aspect ratio and avoid upscaling. Quality is respected within 30–95. EXIF rotations and alpha transparency are preserved. A maximum source pixel count bounds memory usage.

Variants are WebP files under `public/cached-media/v2`. Cache keys include full path, source SHA-256, size and quality; source changes invalidate old variants. Atomic file writes and locks prevent partial responses. Cache hits stream the existing file without decoding it; ETags support conditional requests. Missing or unsupported originals return a localized placeholder. The route has a request-rate limit. Cached derivatives may be discarded and regenerated; originals must be backed up first.

October 25 repair: existing-source rendering now permits up to 25 megapixels (`media.max_source_pixels`), independently of the unchanged 20-megapixel upload limit. Five existing 24-megapixel originals (IMG_0483.JPG and four `Remove background project bri` PNGs) were incorrectly receiving placeholders. Their originals were intact. After the bounded rendering-limit adjustment, all 82 distinct homepage image URLs returned WebP images, including public HTTPS checks of the previously affected JPEG and PNG. No stored originals exceeded the new limit. Local ImageVariantsTest passed 5 tests / 35 assertions; no tests ran on production. Previous production files are retained in `/srv/forkids-staging/backups/20261025-image-source-limit`. A filename search of `/var/www`, `/srv` and `/backup` did not locate the ten historically absent originals listed in environments.md; those still require a separate original source or owner-provided replacements. No catalog records or originals were altered.

### Image optimization operation (October 25)

Image helper URLs now default to WebP quality 80 instead of 100 (previously clamped to 95). Explicit quality arguments still work. Catalog cards defer images after the first four cards, and product galleries defer secondary images. The host proxy preserves the image route's one-hour public cache policy while retaining private/no-store application pages. Image URLs are not immutable: replacing a source at its existing path is reflected after cache revalidation; the resizer's content hashes invalidate its disk variants immediately.

`scripts/optimize-media.php SOURCE NEW_OUTPUT_DIRECTORY` is an offline candidate generator, not an in-place cleanup command. It bounds supported still images to 2560 pixels on the longest side, uses JPEG quality 88 / WebP quality 85 / PNG compression 9, preserves transparency and EXIF orientation, and retains the original bytes whenever a candidate is larger. Unsupported/animated images are copied unchanged. It creates a private hash/size manifest alongside the separate output tree. Never use the media reconciliation cleanup command as an optimization shortcut.

Recovery for this operation is `/srv/forkids-staging/backups/20261025-media-optimization/original-media.tar.gz`, verified file-by-file against all 306 originals before activation. Preserve this archive and the accompanying optimization manifest. Media metadata recovery is in the separately private scheduled-backup mount at `recovery-20261025-media/media-metadata.json`; it contains the pre-optimization media rows. Restore only affected image files and their matching media metadata if rollback is needed; do not replace orders or restore the whole database. Image URLs, filenames, galleries and catalog text remain unchanged. Clear only regenerable `cached-media/v2` files after stopping the application if reclaiming obsolete derivative space.

Verified outcome: 306 files processed, 271 replaced with smaller validated candidates; active media bytes fell from 347,723,032 to 133,736,471 (61.5% reduction). All 306 originals in the recovery archive matched the pre-change SHA-256 manifest, and all activated files matched the candidate hashes. The 271 matching media records had dimensions, byte sizes and SHA-256 metadata refreshed after a private metadata snapshot. Every stored image decoded, produced a 256-pixel WebP variant, and matched its media metadata. Obsolete derivatives and temporary candidate trees were removed; the warmed cache used 2.4 MiB at verification. The retained 339,130,732-byte recovery archive consumes separate backup space; active-media savings do not imply an equivalent reduction in total server disk usage.

The homepage's 82 distinct image downloads fell from 985,348 to 465,944 bytes (52.7% reduction); 95 card images were lazy-loaded and the first four remained eager. Three representative product pages, home, admin login and sitemap returned 200, as did public HTTPS home and image checks. Public images retained `max-age=3600, public`; admin remained private/no-store. Visual before/after checks covered a photograph and a transparent product PNG, and the isolated local suite passed 81 tests / 429 assertions. Production verification used existing media only, without synthetic records, orders, payments or mail.

Physical duplicate cleanup records redirects internally in `media_aliases`: old original and resizer URLs continue to resolve. Twelve redundant tracked files from `public/logo` were removed; their known URLs redirect to the single recovered branding originals. Product and page URLs are retained.

## Audited cleanup and recovery

Dry run:

```bash
scripts/dev exec --user www-data app php artisan media:reconcile
```

The report identifies unused files, duplicate content hashes, reclaimable bytes and missing referenced originals. Usage includes product covers/galleries, archived records, HTML, order snapshots and templates. Conservative basename matching may retain an ambiguous file; it never deliberately deletes one whose reference is uncertain.

Applying cleanup is limited to sandbox `local`/`staging`, requires maintenance mode and a new private backup path:

```bash
scripts/dev exec --user www-data app php artisan down
scripts/dev exec --user www-data app php artisan media:reconcile --apply --backup=/var/www/html/storage/app/media-recovery/UNIQUE-RELEASE
scripts/dev exec --user www-data app php artisan up
```

Before any unlink, the command copies originals, verifies SHA-256, and saves metadata/reference snapshots plus a deletion plan in the private recovery directory. A completion manifest records successful removal. It reconciles repeated media rows and gallery links, updates duplicate cover paths, preserves published aliases and missing-file records, and normalizes HTML. Run the audit again afterward. Keep a complete database/media snapshot as well: restoring the database snapshots is an operational recovery action, not a daily admin function.

Local and staging cleanup on this release: 384 → 306 originals; 74 unused files and four duplicate copies archived/removed, reclaiming 38,217,946 bytes. The second audit reports zero unused/duplicate candidates. Ten filenames were absent from the supplied backup; their records remain and the storefront uses placeholders. Recover or replace those originals through the media library before production acceptance. Staging's final numbers and recovery paths are recorded in `implementation-log.md`.

## Removed OpenCart dependencies

The public `/update` importer, `OCSeeder`, OpenCart database connection/environment settings and external OpenCart backup path are removed. Native management and recovered media are self-contained. OpenCart is not needed by local or staging. The obsolete outer-repository SQL file is removed from the active checkout and retained privately for recovery. The original production server and its historical OpenCart files remain untouched until an explicit production cutover.

## Next acceptance stage

The following acceptance workflows are for local or independently isolated staging only. Do not follow them on `forkids.mk` or either alias: those are live. Provision isolated staging with separate database, media, credentials and disabled integrations before using it. The currently available local store is `http://localhost:8088`.

1. Create a category and product with Macedonian text, price/discount and stock. Upload two images, change their order, and verify the cover/product page. Hide the product and confirm it disappears from search and its direct URL.
2. Edit a product description and a page with headings, links, a library image and a short MP4/WebM. Save the page as draft, then publish and verify its footer link and playback.
3. Search with Cyrillic and Latin equivalents. The unchanged recovered examples yield 43 cards for `трицикл` / `tricikl`, 12 for `количка` / `kolicka`, and 39 for `коцки` / `kocki`; counts include sold-out cards and change after catalog edits.
4. Create another administrator, sign in with it, update its password, and remove it from another administrator account. Confirm public registration stays unavailable. No customer roles exist.
5. Sign out and complete guest delivery details. Verify cart changes and order confirmation; staging displays the payment-disabled notice and does not send money.
6. Review the ten missing originals listed in [environments.md](environments.md#restored-snapshot-and-media-exceptions), then supply or upload replacements and reselect affected gallery entries. Existing missing references use placeholders rather than fabricated images.

Real payments remain disabled there. Before any production release, rotate the previously exposed production credentials and complete provider-approved callback authenticity, idempotency, concurrency and stock-reservation tests. This stage does not claim those legacy payment weaknesses are fixed. Customer accounts, Google/Facebook login, social enhancements and carts surviving the ordinary guest session remain later work.
