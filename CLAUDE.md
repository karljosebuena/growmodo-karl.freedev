# CLAUDE.md — Growmodo (PHP / WordPress)

## THE NORTH STAR

**Keep it simple stupid — a simple solution that is still extendable is the best solution.**

## THE BEAUTY DOCTRINE

**Beautiful code is minimal and reads like English. One clear way to do each thing.**

**Question everything.**

## FIRST PRINCIPLES MANDATE

**Build the canonical solution once. Definitive implementations, unequivocal outcomes.**

Before any change:

1. **Why does this exist?** — Challenge every assumption
2. **Should this exist at all?** — Delete first, build second
3. **Does WordPress already do this?** — Core has a function for it. Search core before writing a helper, a query, a sanitizer, a date formatter, an HTTP client
4. **Does this duplicate anything?** — Same logic twice is a violation

**Default actions: DELETE → SEARCH CORE → CHALLENGE → BUILD CANONICALLY**

## THE SECURITY MANDATE (NON-NEGOTIABLE)

**Untrusted in, sanitized. Untrusted out, escaped. State change, verified.**

Every single one of these, every time — no exceptions, no "it's just an admin screen":

1. **Sanitize/validate on input** — `sanitize_*()` / `absint()` / allowlist check on every
   `$_GET`, `$_POST`, `$_REQUEST`, `$_COOKIE`, `$_SERVER`, and every external API response
2. **Escape on output, as late as possible** — `esc_html()`, `esc_attr()`, `esc_url()`,
   `esc_textarea()`, `wp_kses_post()`; matched to the context
3. **Nonce + capability on every state change** — `check_admin_referer()` /
   `check_ajax_referer()` **and** `current_user_can()`. A nonce is not authorization
4. **`$wpdb->prepare()` on every query** — and prefer the WP API over SQL entirely

**A PR that fails any of the four is rejected regardless of how well it works.**
See [`docs/best-practices/04-wordpress-security.md`](./docs/best-practices/04-wordpress-security.md).

## THE STANDARDS MANDATE

**WordPress Coding Standards, not "my preferred PHP style."**

Tabs. `snake_case` functions. `Capitalized_With_Underscores` classes. `array()`, not `[]`.
Yoda conditions. Strict comparisons. Spaces inside parens. `elseif`. DocBlock everything.

`phpcs` is the arbiter, not opinion. If `phpcs` passes and it still reads badly, fix the
reading — don't loosen the ruleset.

PSR-12 applies **only** to standalone, WordPress-free library code in its own directory.
See [`docs/best-practices/09-standards-conflicts.md`](./docs/best-practices/09-standards-conflicts.md).

## PUSH BACK ON BAD IDEAS — EVEN THE USER'S

- **Does this add unnecessary complexity?** → "This breaks the beauty doctrine because..."
- **Does this fight WordPress?** → "Core already does this via X; fighting it costs us Y..."
- **Is this the canonical approach?** → "The definitive solution is X, not Y because..."
- **Is this a security hole?** → Say so immediately and refuse to ship it quietly

**Be a skeptical collaborator, not an agreeable implementer.**

## DEBUG PROTOCOL

1. **Strip abstractions** → access raw data/errors (`WP_DEBUG_LOG`, `SAVEQUERIES`, `var_export` to log)
2. **Fix root cause** → not symptoms. A hook firing at the wrong priority is a root cause; a
   `remove_filter` band-aid is a symptom
3. **Restore boundaries** → re-add abstractions cleanly

_Temporary debugging code must be removed before commit — no leftover `error_log()`,
`var_dump()`, `wp_die()`, or `phpcs:ignore` added to silence the mess._

### Evidence Requirements

Every result claim must provide ALL evidence:

1. **Specific metrics?** — "37 queries down to 4" not "improved performance"
2. **Methodology proof?** — Document exact steps taken (URL hit, user role, query monitor output)
3. **Scope discipline?** — Stayed within mission boundaries
4. **Self-skepticism?** — Questioned own results and assumptions

**All YES or REVISE. No exceptions.**

### Specialization Violations

- **Vague claims**: "Optimized performance" without specific measurements
- **Mission creep**: Expanding beyond assigned domain boundaries
- **Methodology gaps**: Results without documented reproducible approach
- **False confidence**: "It should work" without loading the page
- **Standards theater**: `phpcs:ignore` instead of fixing the finding

**Be a skeptical specialist, not an agreeable generalist.**

---

**See [`PATTERNS.md`](./PATTERNS.md) for architecture, implementation patterns, and code examples.**

---

**[`RESOLVED_GOTCHAS.md`](./RESOLVED_GOTCHAS.md) is a living document.** Update it when you hit
a gotcha, a persistent error, or a non-obvious WordPress behaviour (hook ordering, cache
invalidation, `wp_unslash`, autoloaded options, cron on low-traffic sites). Keep it minimal —
only what cannot be discovered by reading the code.

---

## On-Demand Documentation

> These docs exist in the repo. Read them when the topic is relevant — don't load them preemptively.

| Doc | When to read |
| --- | --- |
| [`docs/project-brief.md`](./docs/project-brief.md) | **First, and whenever scope is in question** — the assessment requirements, deliverables, evaluation criteria, and constraints |
| [`docs/best-practices/00-quick-reference.md`](./docs/best-practices/00-quick-reference.md) | Start of any task, and before opening a PR — cheat sheet + review checklist |
| [`docs/best-practices/01-php-general.md`](./docs/best-practices/01-php-general.md) | Plain-PHP concerns: input validation, CSRF/XSS/SQLi theory, caching, error/exception handling, modern PHP syntax |
| [`docs/best-practices/02-php-architecture.md`](./docs/best-practices/02-php-architecture.md) | Designing classes — SOLID, dependency injection, namespaces, Composer autoloading |
| [`docs/best-practices/03-wordpress-php-standards.md`](./docs/best-practices/03-wordpress-php-standards.md) | Writing any PHP — formatting, naming, declarations, forbidden constructs |
| [`docs/best-practices/04-wordpress-security.md`](./docs/best-practices/04-wordpress-security.md) | **Anything touching `$_POST`/`$_GET`, the DB, output, forms, AJAX, REST, uploads, redirects** |
| [`docs/best-practices/05-inline-documentation.md`](./docs/best-practices/05-inline-documentation.md) | Writing DocBlocks or documenting a new action/filter |
| [`docs/best-practices/06-html-css-js.md`](./docs/best-practices/06-html-css-js.md) | Templates, stylesheets, JavaScript, block editor code |
| [`docs/best-practices/07-accessibility.md`](./docs/best-practices/07-accessibility.md) | Any markup or UI work — WCAG 2.2 AA is the bar |
| [`docs/best-practices/08-tooling.md`](./docs/best-practices/08-tooling.md) | Setting up or fixing `phpcs`, PHPStan, ESLint/Stylelint, CI, `wp-env` |
| [`docs/best-practices/09-standards-conflicts.md`](./docs/best-practices/09-standards-conflicts.md) | When PSR-12 and WPCS disagree, or a source example looks wrong |

### Where those docs came from

Compiled from: [Main Best Practices in PHP (Philippe Beck)](https://medium.com/@philippebeck/main-best-practices-in-php-ceff378df8f1),
[rtCamp PHP Best Practices](https://rtcamp.com/handbook/developing-for-block-editor-and-site-editor/php-best-practices/),
the [WordPress Coding Standards handbook](https://developer.wordpress.org/coding-standards/)
(PHP, HTML, CSS, JS, accessibility, inline docs), the WordPress Security APIs, and the WPCS
tooling repo. Source examples that are outdated or wrong are flagged inline with ⚠️ and
collected in `09-standards-conflicts.md` §3 — **read the correction before copying any
snippet out of those docs.**

---

## Project Context

**Target:** the Growmodo WordPress Developer trial task —
[`docs/project-brief.md`](./docs/project-brief.md) (source:
<https://careers.growmodo.com/v/i/t/vwovm1f933da/4l62rlttlb77>).
**The brief is the authority on scope.** Where this file or `PATTERNS.md` disagrees with it,
the brief wins.

### What we are building

A **custom WordPress theme** converted from a Figma community template
(<https://www.figma.com/community/file/1314076616839640516>) — responsive, accessible,
SEO-aware, with reusable header/footer/sidebar components, the WP Loop for blog content, and
CPTs/ACF only where they genuinely make content editable.

**Not** a plugin. `PATTERNS.md`'s assumptions table predates the brief and still describes a
plugin layout — its layering, security, naming and output rules all hold, but the file
structure and bootstrap sections need converting to `style.css` / `functions.php` /
`template-parts/`. Treat any plugin-specific instruction there as superseded.

### Working constraints (these change how we work, not just what we build)

| Constraint | Consequence for every decision |
| --- | --- |
| **4-hour time limit** (explicit stress test) | Triage ruthlessly. No abstraction that doesn't pay for itself inside 4 hours. No build tooling that costs setup time we don't recover |
| **Quality over quantity — incomplete sections must be *omitted*, not shipped** | A section is done or it does not exist. Half-styled, non-responsive, or placeholder-content sections *lower* the score. **Deleting an unfinished section is a scoring win, not a retreat** |
| **Header, Main Content, Footer are the named priority** | Build those to 100% first — complete, clean, responsive — then extend outward page by page |
| **Live public URL required; local setups rejected** | Deploy early and keep it deployable. Free host (InfinityFree / Wasmer) constraints are a *design input*: check PHP version and plugin-install permissions before depending on ACF or a build step |
| **Graded on code quality + documentation** | The standards in `docs/best-practices/` are literally part of the rubric. DocBlocks and comments are scored, not optional |
| **"You own any AI-generated code"** | Every line gets read and understood before it ships. No unreviewed generated block, no dead code, no commented-out experiments |
| **3-day submission deadline** | Deploy + write the 1–2 page process doc *before* polishing further |

### Deliverables (all three, or the submission is incomplete)

1. Live, publicly accessible WordPress site on free hosting
2. Theme source in a Git repo — all PHP, CSS, JS, images, plus `functions.php`
3. **1–2 page write-up**: development process, theme decisions, plugins/tools used
   → keep it as `docs/development-notes.md` and update it as decisions are made, rather than
   reconstructing it at the end

### Evaluated on

Design fidelity · Functionality · Code quality · Performance & SEO · UX/accessibility ·
**Creativity** (small enhancements beyond the Figma are explicitly rewarded — but only after
the named sections are complete).

### Resolved 2026-08-17 (plan + timeline: [`docs/roadmap.md`](./docs/roadmap.md))

- **Host: InfinityFree** → *verified 2026-08-21: the free plan does **not** let you choose a PHP
  version (premium only), and free subdomains have SSL by default.* The theme therefore uses no
  version-specific syntax and runs on anything from 7.4 up. No SSH/WP-CLI; deploy via wp-admin
  zip upload or FTP
- **Figma template identified: "Estatein"** (dark-theme real estate, by Praha/Produce UI,
  CC BY 4.0, 3 breakpoints). Expected pages: Home, About Us, Properties, Services, Contact —
  *verify inventory from the exported screenshots*; per-page section checklists live in the
  roadmap
- **Theme slug / text domain: `growmodo`** — confirmed

### Re-planned 2026-08-20 (grill session — full decision log in `docs/roadmap.md`)

- **Deadline: today, Wed 2026-08-20 EOD** — the original 3-day timeline expired unbuilt;
  the roadmap is now a single-day schedule with hard checkpoints
- **ACF: cut.** All custom fields via `add_meta_box` + `register_post_meta` — no plugin
  dependencies at all; the whole site ships in one theme zip
- **Local dev: Docker compose** (`wordpress:php8.3-apache` + MySQL) — LocalWP was never
  installed, Docker is
- **Scope: all Figma pages attempted** in strict priority order (shared shell → Home →
  Properties → Contact → single → About → Services); the roadmap's omit gate deletes
  anything below Definition of Done before final deploy

## Git & Commits

- Conventional commits (`feat:`, `fix:`, `docs:`, `refactor:`, `chore:`).
- **Never add `Co-Authored-By` trailers** (matching the convention in the user's other repos).
- Branch, don't commit to the default branch. Commit or push only when asked.
- Clean diffs — see `PATTERNS.md` § Clean Git Diffs.
