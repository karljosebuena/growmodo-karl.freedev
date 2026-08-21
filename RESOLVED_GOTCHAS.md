# Resolved Gotchas

**Living document.** Add an entry when you hit a gotcha, a persistent error, or a non-obvious
WordPress behaviour that cost real time. Keep it minimal — only what **cannot** be discovered
by reading the code. Style rules belong in `docs/best-practices/`, patterns in `PATTERNS.md`.

## Format

```markdown
## <Short symptom, as you'd search for it>

**Symptom:** what you actually saw (error text, wrong output, silence).
**Cause:** the real reason.
**Fix:** the change, with the file/function if it lives in this repo.
```

---

## "Failed to import Media" for every attachment on InfinityFree

**Symptom:** the WordPress importer reported `Failed to import Media "..."` for all 8
attachments, while every post, page and menu item imported cleanly. The image URLs were
correct and returned HTTP 200 when opened in a browser.

**Cause:** InfinityFree answers requests for some file extensions with its JavaScript
bot-check page instead of the file. Measured on the live host: `.css`, `.jpg`, `.png` and
`.svg` are served normally; **`.webp` and `.js` return a ~900-byte HTML challenge** to any
client without the challenge cookie. A browser loads the HTML first, solves the check, and
carries the cookie on every later request — so the front end is unaffected. The importer
fetches server-side with `wp_remote_get()`, has no cookie and runs no JavaScript, so it
received HTML where it expected WebP and rejected all 8 files.

**Fix:** upload the 8 images through wp-admin (a browser POST, which carries the cookie) and
attach them by hand — featured image per listing, plus the two extra villa photographs. Do
not re-run the WXR import to fix thumbnails: the importer skips posts whose GUID already
exists, so `_thumbnail_id` is never remapped, and you get eight orphaned attachments instead
of linked ones.

**Also worth knowing:** any non-JavaScript client sees the same challenge, so a `.webp`
`og:image` is unreadable to social-preview scrapers on this host. Browsers and JS-executing
crawlers are fine.

<!--
Candidates to watch for on a WordPress project (delete this comment as real entries land):
- wp_unslash() needed before sanitizing superglobals — WP adds slashes
- Hook fired before your callback was registered (priority / wrong hook, e.g. init vs plugins_loaded)
- Rewrite rules 404 until flushed — flush on activation only
- Autoloaded option bloat slowing every request
- wp_cache_* silently per-request without a persistent object cache installed
- Transients not stored when an external object cache is present but full
- WP-Cron not firing on low-traffic sites / DISABLE_WP_CRON set
- REST route 401 because X-WP-Nonce wasn't sent by a cookie-authenticated caller
- Block editor not seeing a CPT/meta because show_in_rest was false
- Secondary WP_Query breaking the main loop without wp_reset_postdata()
-->
