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

_No entries yet — this repo has no implementation code._

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
