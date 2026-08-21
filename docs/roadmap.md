# Roadmap — Estatein WordPress Theme (Growmodo Trial Task)

> **The tracking document.** Every piece of work lives here as a checkbox. Update it as work
> lands — this file is the single answer to "what's done and what's left."
>
> Scope authority: [`project-brief.md`](./project-brief.md).
>
> **Re-planned Wed 2026-08-20 after a grill session** — the original 3-day timeline expired
> with nothing built. Decisions from that session are logged in the
> [Decision log](#decision-log-2026-08-20-grill-session) below; the timeline is now a
> single-day wall-clock schedule.
>
> **Status Thu 2026-08-21.** The build is complete: seven page types, six review and
> design-fidelity passes, `phpcs` clean, no plugin dependencies. **The 2026-08-20 EOD
> deadline passed with the site undeployed** — the two remaining items (public GitHub repo,
> InfinityFree deploy) were both blocked on decisions only Karl could make.
>
> **The site is now live at <https://growmodo-karl.freedev.app>** and verified on the public URL.
> The one remaining deliverable is the public GitHub repo, still held on the account question.

---

## How to use this file

- `[ ]` = to do · `[x]` = done. Strike through (`~~…~~`) anything **deliberately omitted** —
  omission is a first-class outcome here, not a failure (see ground rules).
- The **page status table** below is the executive summary; the phase checklists are the
  detail. Keep both current.
- Times are **boxes, not estimates** — when the box is spent, triage: finish the smallest
  shippable version or cut the section entirely.

### Page status

| Page | Status | Notes |
| --- | --- | --- |
| Shared: header / footer / CTA banner | Done (local) | Announcement bar, mobile nav, newsletter, socials; footer labels and mobile social row match the design |
| Home | Done (local) | Hero, feature strip, properties, testimonials, FAQ — card sections are pageable carousels |
| Properties (archive) | Done (local) | Server-side **search** + browser-side **filters** (location / type / price / size / build year), no submit or reset buttons |
| Property single | Done (local) | Gallery carousel with thumbnails, description + key features, pricing breakdown, FAQ, short enquiry form |
| Contact | Done (local) | Info cards, working form, office locations |
| About Us | Done (local) | Journey, values, achievements, 6-step process, team, valued clients |
| Services | Done (local) | Three service groups, data-driven from one array; two layouts (grid + rail) |
| Insights (blog) | Done (local) | `archive.php` / `single.php` / `search.php` + sidebar — the brief names the WP Loop for blog content |
| 404 + search results | Done (local) | Styled, share the shell |

**Figma inventory confirmed:** 6 pages × 3 breakpoints — Home, About Us, Services,
Properties, **Property Details**, Contact. Breakpoints are **1920 / 1440 / 390** (laptop, not
tablet, is the middle frame).

"Done (local)" = meets Definition of Done on the local build; the live-site column of the DoD
is pending deploy.

Statuses: `Not started` → `In progress` → `Done` (meets Definition of Done) → or `Omitted`
(cut deliberately, removed from the live site and nav).

**Scope decision (2026-08-20): all Figma pages are in scope** — built strictly in the
priority order above. The Phase-G omit gate is the safety net: anything not at Definition of
Done at final deploy is deleted from the live site, whatever the ambition was.

---

## Ground rules (from the brief — non-negotiable)

1. **Quality over quantity.** A half-finished section *lowers* the score. Anything not at
   Definition of Done by the final deploy gets **deleted from the live site**, not shipped.
2. **Header, Main Content, Footer are the named priority.** They reach 100% before anything
   else gets time.
3. **Live public URL required.** Local setups are rejected. Deploy early, keep it deployable.
4. **Hard deadline was Wed 2026-08-20, end of day — it passed undeployed.** The build met the
   omit gate; the submission did not go in, because a site with no public URL is not a
   submission. Deploying is therefore the only work that still counts: nothing else raises
   the score while deliverable 1 is missing.

---

## Deployment reality check

**Host: InfinityFree** (classic free LAMP host, explicitly allowed by the brief). Vercel-like
platforms have no PHP/MySQL; headless would fail the "custom WordPress theme" requirement.

| InfinityFree gives us | Constraints to design around |
| --- | --- |
| PHP 8.x, MySQL, free subdomain, SSL by default | No SSH / no WP-CLI — wp-admin + FTP only |
| | **PHP version is not selectable on the free plan** (premium only), so the theme must run on whatever the host provides — verified 2026-08-21 |
| Softaculous one-click WordPress install | Subdomain/SSL provisioning lag — **signup started first, in parallel** |
| Full wp-admin | 30,000 requests/day and inode caps |
| | PHP `mail()` unreliable — nothing may depend on email delivery |
| | JS browser-check on first visit (fine in browsers; curl/bots see a challenge page) |

**Deploy model (decided 2026-08-20):**

1. Karl drives the live wp-admin: theme zip uploads, activation, settings (permalinks
   `Post name`, static front page, menus). Claude hands over a zip + checklist per round.
2. Claude verifies each deploy on the **public URL** with browser tools — no credentials.
3. **Content ships as a WXR export** — *this reverses the original "enter it once, on live"
   decision.* Demo content grew past what anyone should retype (6 properties with meta and
   images, 6 testimonials, 6 FAQs, 2 posts, 6 pages, 6 menu items), so it is exported from
   Docker and imported live. `deploy/prepare-content.sh <live-url>` rewrites the export's
   attachment URLs to point at the theme's own `assets/img/`, which is how the live importer
   sideloads media it cannot fetch from `localhost`. Every one of the export's 8 attachments
   is verified to exist in the theme, so the import is self-contained. Hand entry remains
   documented as Option B in `deploy/DEPLOY.md`.
4. No plugins required by the build: **ACF is cut** (core meta boxes instead), forms are
   hand-rolled. Zero plugin-install risk on the flaky host.

---

## Definition of Done (per section — all boxes or it ships as Omitted)

- [ ] Matches the Figma at all three breakpoints (desktop / tablet / mobile)
- [ ] Semantic, accessible markup — keyboard navigable, visible focus, contrast passes,
      images have meaningful `alt`
- [ ] All dynamic output escaped (`esc_html` / `esc_attr` / `esc_url` / `wp_kses_post`)
- [ ] Images sized correctly, `loading="lazy"` below the fold, no layout shift
- [ ] `phpcs` clean, DocBlocks present
- [ ] No browser console errors; verified **on the live site**, not just locally

---

## Architecture decisions (fixed — see Decision log for reasoning)

- **Local dev:** Docker compose — `wordpress:php8.3-apache` + MySQL, theme dir
  volume-mounted. (LocalWP was never installed; Docker already is.)
- **Structured data: 4 CPTs, no ACF.**
  - `property` — meta: beds, baths, price, type, location (`register_post_meta` +
    `add_meta_box`, nonce + capability + sanitize on save)
  - `testimonial` — meta: stars, name, location
  - `faq` — title + content only, no meta
  - `inquiry` — private, not publicly queryable; stores form submissions
  - Everything else (hero copy, feature cards, values, process steps, service sections,
    team) is hard-coded in templates — triage documented in the write-up.
- **Forms:** hand-rolled. POST to `admin-post.php`, nonce + honeypot + sanitization,
  submission saved as an `inquiry` post. No email dependency (host `mail()` unreliable).
  Footer newsletter form uses the same handler pattern.
- **Git:** public GitHub repo from the start, conventional commits per phase on `develop`,
  merge to `main` before submit. No Co-Authored-By trailers.
- **No build tooling.** One stylesheet with design tokens as CSS custom properties, vanilla
  JS, scripts enqueued in the footer.

---

## Today — Wed 2026-08-20 (single-day schedule, hard checkpoints)

Two parallel tracks. **Karl's track is on the critical path early** — hosting provisioning
lag and Figma exports gate everything.

### Track A — Karl (parallel, starts NOW)

- [x] Launch Docker.app (Claude's compose stack needs the daemon)
- [ ] **InfinityFree signup → new site on free subdomain → enable SSL → Softaculous WordPress
      install → hand Claude the public URL** — outstanding, and the whole submission waits on it
- [x] Duplicate the Estatein Figma community file to drafts
- [x] Export **full-page screenshots of every page at desktop width** → `docs/figma/`, all
      three frames
- [x] ~~Export assets~~ — the community file exports no usable assets, so the photography was
      cropped out of the page renders and converted to WebP instead, and the icons were drawn
      as an inline SVG library
- [x] Confirm from the Figma: a **property single** design does exist; page status table
      corrected
- [x] ~~Enter real content on live~~ — superseded by decision 17: content ships as a WXR
      export and is imported, not retyped

### Track B — Claude (build)

#### Phase A · Scaffold — checkpoint 16:30 ✅ done ~15:45

- [x] Docker compose file; WP running locally on PHP 8.3 (`localhost:8080`, admin/admin)
- [x] `phpcs` + WPCS installed via Composer; ruleset in repo — **theme passes clean**
- [x] `style.css` theme header (slug/text-domain `growmodo`), `functions.php` (enqueues,
      theme supports, nav menus), base templates (`header.php`, `footer.php`, `index.php`,
      `front-page.php` shell)
- [x] Design tokens as CSS custom properties (Estatein defaults; refine from Figma exports)
- [x] CPT + meta registration for all four types; meta boxes with save handlers; test
      content seeded (3 properties w/ meta, 2 testimonials, 2 FAQs, pages + menus)
- [x] git init, two commits on `main` (docs, scaffold), `develop` branch created
- [ ] Push public GitHub repo — **held: gh is authed as `karl-bv` (work account?) —
      confirm which account before publishing**

#### Phase B · Shared shell — ✅ done

- [x] Announcement banner (dismissible, persists, works without JS)
- [x] Header: logo, nav (`wp_nav_menu`), Contact CTA, **responsive mobile menu**
      (accessible toggle: `aria-expanded` + Escape to close)
- [x] Pre-footer CTA banner as a `template-parts/` partial
- [x] Footer: logo, newsletter form, 5 link columns, socials, copyright bar
- [x] Reusable partials: buttons, section heading, card base, inline SVG icon library

#### Phase C · Home — ✅ done

- [x] Hero: headline, copy, dual CTAs, stat cards, hero image + rotating seal
- [x] Feature cards row
- [x] Featured Properties: `property` CPT + WP Loop with tags, price, detail button
- [x] Testimonials loop (CPT)
- [x] FAQ cards (CPT, native disclosures)
- [x] Responsive pass at the 3 breakpoints

#### Phase D · Remaining pages — ✅ done

- [x] **Properties archive**: hero, search, **working** filter row, results as a pageable
      carousel (the design's own pattern — see decision 14 for why there is no pagination)
- [x] **Contact**: hero, contact info cards, hand-rolled form → `inquiry` CPT, offices
- [x] Property single (`single-property.php`) — spec panel + prefilled enquiry form
- [x] About Us: journey/stats, values panel, achievements, 6-step process, team
- [x] Services: hero + three service groups with side CTAs

#### Phase E · Creativity

- [x] JSON-LD structured data (RealEstateAgent / SingleFamilyResidence + Offer / FAQPage)
- [x] Property search server-side via `pre_get_posts` (shareable URL, works with JS off) and
      filtering in the browser over the rendered cards — one predicate per feature, no
      `meta_query` duplicating the same logic. Filters are CSS-hidden unless `<html>` has
      `has-js`, so no control is offered that cannot work
- [x] Computed pricing breakdown on the property page — every figure derived from the listing
      price at filterable rates, proven against the Figma's own worked example
- [x] Announcement banner dismissal persisted in `localStorage`, no flash, no-JS safe
- [x] Scroll-reveal + card microinteractions, reduced-motion safe

#### Review passes — ✅ done

- [x] Accessibility audit (axe-core + manual keyboard/contrast): 6 findings, 6 fixed
- [x] Standards audit against CLAUDE.md / PATTERNS.md / docs/best-practices: found a
      critical JSON-LD XSS, unescaped titles, a shipped deprecation notice, four dead
      footer links, and doctrine duplication — all fixed
- [x] Figma fidelity audit against the exports: typography refitted to all three frames,
      hero seal/button geometry, team badges, property image sizing corrected; remaining
      gaps listed in the write-up
- [x] Brief re-read line by line — closed four explicitly-named gaps (sidebar, WP Loop
      for blog posts, meta tags, CSS/JS minification)

#### Design-fidelity rounds — ✅ done (screenshot review, 2026-08-20/21)

Nine rounds of side-by-side review against the Figma exports, each measured on the PNG with
pixel scans rather than judged by eye. What each round changed:

- [x] **Properties archive** — search section added as its own feature, filter icons, hero
      gradient, reset button dropped; filtering moved to the browser
- [x] **Filters** — icon family unified; Property Size and Build Year replaced the
      Bedrooms/Bathrooms substitution, and are now stored as real post meta
- [x] **Archive + home** — curated listing order shared by both, full enquiry field set, the
      invented "Showing 6 of 6" line removed from view (kept as a screen-reader status)
- [x] **Property detail** — gallery carousel, aligned description/key features, a two-column
      enquiry variant, the pricing tables, and the FAQ section
- [x] **Gallery pager** — the design's centred pill-with-dashes, not a count row
- [x] **Footer** — the design's own link labels, every one resolving to a section that
      exists; social row rearranged on mobile
- [x] **Services** — full-bleed feature strip, outlined cards, the design's two group layouts
- [x] **Services icons** — one distinct glyph per card, matched to its copy
- [x] **Feature strip + rails** — two concentric rings, corrected padding/label/arrow
      geometry, equal grid rows so a CTA panel matches the card beside it

#### Phase F · Verification — ✅ done locally

- [x] Responsive: every shipped page swept for horizontal overflow at ten widths from 320 to
      1920; content column matches the Figma frames (358 / 1282 / 1596px)
- [x] SEO: title-tag, meta descriptions, OG/Twitter tags, exactly one `h1` per template, alt
      text, semantic landmarks, JSON-LD
- [x] Performance (Home, logged out, local warm cache, admin bar off): 13 requests, ~172KB
      transferred (HTML 14KB, CSS 7KB gzipped from 36KB, JS 2KB, images 148KB), LCP 72ms,
      DCL 48ms, **CLS 0 with zero shift entries recorded**
- [x] Accessibility: axe-core plus keyboard-only walkthrough and computed-contrast checks
- [x] `phpcs` — zero errors, zero warnings across 50 files
- [x] Console: no errors on any page (a 404 URL logs its own status; that is the browser, not
      the theme)

#### Phase G · Omit gate — ✅ done, nothing cut

Every section was measured against the Definition of Done and all of them pass, so nothing
was removed. The two deliberate content-driven omissions are the Contact page's
"Explore Estatein's World" photo mosaic (needs six photographs the community file does not
export) and the Contact office filter tabs (two offices do not justify a filter); both are
named in the write-up. The DoD's "verified on the live site" box stays unticked
until deploy.

#### Deploy — ✅ **live at <https://growmodo-karl.freedev.app>** (2026-08-21)

- [x] Theme zipped and uploaded to live wp-admin, activated (PHP 8.3.19, WordPress 7.0.2)
- [x] Live settings: permalinks `/%postname%/`, static front page *Home*, posts page
      *Insights*, site title *Estatein*, both menus assigned to their locations
- [x] Content imported. The WXR media sideload failed on all 8 attachments — InfinityFree
      answers `.webp` with a bot-check page (see `RESOLVED_GOTCHAS.md`) — so the demo
      photography was converted to JPEG, uploaded through wp-admin, and linked by replaying
      each post's own edit form
- [x] Verified on the public URL: 8 URLs return the right status and title with one `h1`
      each, nav restored, 6 property cards with images, villa gallery of 3, pricing tables
      computing from the listing price, FAQ section, filters narrowing live (6→1→2→1→0→6),
      search plus its XSS probe, contact form submitting into an `inquiry`, mobile nav
      toggling and closing on Escape, and **zero horizontal overflow across 30 page/width
      combinations from 320 to 1920**
- [x] Softaculous's bundled plugins (Loginizer, Loginizer Pro, W3 Total Cache, Akismet,
      Hello Dolly) all deactivated — the live site runs with no plugin active but the
      importer
- [ ] Karl to finish: delete those five plugin folders, trash `Sample Page` and the
      auto-draft `Privacy Policy`, delete the `[contact] Deploy Verification` test inquiry,
      take the WordPress 7.1 update, and change the admin password shared in chat
- [ ] Cross-browser: Chrome, Firefox, Safari; real phone if possible

#### Phase H · Docs + submit — **blocked on the repo decision**

- [x] [`development-notes.md`](./development-notes.md) — the 1–2 page write-up, decisions-focused per the write-up
      decision, kept current as decisions landed rather than reconstructed at the end
- [x] Repo README: what it is, install instructions, content model, structure
- [ ] Push the public GitHub repo — **held: `gh` is authenticated as `karl-bv`, a work
      account. Confirm which account owns the public trial repo before publishing**
- [ ] Fill the live URL into `README.md`
- [ ] Merge `develop` → `main`, final push
- [ ] Final live-URL click-through from an incognito window
- [ ] Submit repo link + live URL + write-up on the task page

---

## Risk register

| Risk | Mitigation |
| --- | --- |
| InfinityFree provisioning lag (subdomain/SSL) | Signup started first, in parallel — lag runs concurrently with the build |
| Figma inventory differs from research (extra pages/sections) | Karl verifies inventory from the actual file during Track A; priority order is inventory-independent |
| Figma exports delayed | Phase A–B proceed on known Estatein tokens (dark theme, #703BF7, Urbanist); tokens corrected when screenshots land |
| InfinityFree 30k req/day cap or JS browser-check surprises the reviewer | Cap far above review traffic; check transparent in real browsers. Wasmer Edge is the fallback host |
| ~~ACF won't install~~ | Resolved by cutting ACF — core meta boxes, zero plugin risk |
| Time overrun | Checkpoints above + the Phase G hard gate. The brief rewards a small perfect site over a large rough one — the gate deletes, ambition notwithstanding |

---

## Decision log (2026-08-20 grill session)

| # | Decision | Choice |
| --- | --- | --- |
| 1 | Deadline | The 3-day window closes **today EOD**; single-day compressed schedule |
| 2 | Scope | **All Figma pages attempted**, in strict priority order, Phase G omit gate enforced |
| 3 | Custom fields | **Core meta boxes, no ACF** — everything ships in the theme zip |
| 4 | Local dev | **Docker compose** `wordpress:php8.3` (LocalWP never installed) |
| 5 | Hosting | InfinityFree, signup started immediately in parallel |
| 6 | Design source | Karl exports full-page Figma screenshots + assets into the repo |
| 7 | Forms | Hand-rolled, nonce + honeypot, stored as private `inquiry` CPT — no email dependency |
| 8 | CPT scope | 4 CPTs (`property`, `testimonial`, `faq`, `inquiry`); the rest hard-coded |
| 9 | Content | Entered once, directly on live; local uses throwaway test data |
| 10 | Git | Public GitHub from the start, conventional commits per phase |
| 11 | Deploys | Karl drives wp-admin; Claude verifies the public URL |
| 12 | Creativity shortlist | JSON-LD → working filters → scroll-reveal → banner persistence (gated on full DoD) |
| 13 | Write-up | Decisions-focused; no duration/AI-process narrative |

## Decisions taken after the plan (2026-08-20/21)

Each of these changed or reversed something above; they are recorded here so the plan and the
build cannot disagree silently.

| # | Decision | Choice and why |
| --- | --- | --- |
| 14 | Search vs filtering | **Two mechanisms, not one.** Search is the only server round-trip (`pre_get_posts` on a `q` param, shareable, no-JS safe); filters narrow the rendered cards in the browser. Reverses the Phase-E plan of server-side filtering — that version duplicated the same predicate in a `meta_query` |
| 15 | Filter set | The design's own: location, type, price, **Property Size**, **Build Year**. My Bedrooms/Bathrooms substitution was wrong and was overruled; size and year are now stored meta, not derived |
| 16 | No submit or reset buttons | Every pill's first option *is* its cleared state, and filtering fires on change. A reset button is a second way to do one thing |
| 17 | Content delivery | WXR export + `prepare-content.sh`, replacing decision 9's hand entry (see Deployment reality check) |
| 18 | Pricing breakdown | **Built, reversing an earlier cut.** I had refused it as fabricated content; the Figma's figures all derive from the listing price at fixed rates, which its worked example proves, so the section is computed from `growmodo_pricing_rates()` and labelled an estimate |
| 19 | Property gallery | Native attached media (featured image first), not an ID list in meta. A listing with one image renders no carousel |
| 20 | Blog | An `Insights` page plus `archive.php` / `single.php` / `search.php` and a sidebar — the brief names the WP Loop for blog content even though the Figma has no blog screen |
| 21 | Editable content triage | Home, Properties, property detail and the blog are content-driven; About Us, Services and Contact keep their copy in templates. Named as the first thing to revisit in the write-up rather than left for a reviewer to find |
