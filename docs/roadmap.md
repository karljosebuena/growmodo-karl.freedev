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
| Shared: header / footer / CTA banner | Done (local) | Announcement bar, mobile nav, newsletter, socials |
| Home | Done (local) | Hero, features, properties, testimonials, FAQ |
| Properties (archive) | Done (local) | Filters genuinely work (type/beds/baths/max price) |
| Contact | Done (local) | Info cards, working form, office locations |
| Property single | Done (local) | ✅ Design confirmed to exist; pricing tables omitted |
| About Us | Done (local) | Journey, values, achievements, 6-step process, team |
| Services | Done (local) | Three service groups, data-driven from one array |

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
4. **Hard deadline: today, Wed 2026-08-20, end of day.** The 3-day window closes tonight.
   Submit before midnight, whatever state the omit gate leaves standing.

---

## Deployment reality check

**Host: InfinityFree** (classic free LAMP host, explicitly allowed by the brief). Vercel-like
platforms have no PHP/MySQL; headless would fail the "custom WordPress theme" requirement.

| InfinityFree gives us | Constraints to design around |
| --- | --- |
| PHP 8.3 selectable, MySQL, free subdomain + SSL | No SSH / no WP-CLI — wp-admin + FTP only |
| Softaculous one-click WordPress install | Subdomain/SSL provisioning lag — **signup started first, in parallel** |
| Full wp-admin | 30,000 requests/day and inode caps |
| | PHP `mail()` unreliable — nothing may depend on email delivery |
| | JS browser-check on first visit (fine in browsers; curl/bots see a challenge page) |

**Deploy model (decided 2026-08-20):**

1. Karl drives the live wp-admin: theme zip uploads, activation, settings (permalinks
   `Post name`, static front page, menus). Claude hands over a zip + checklist per round.
2. Claude verifies each deploy on the **public URL** with browser tools — no credentials.
3. **Content is entered exactly once, on live.** Local Docker gets 2–3 throwaway test posts
   to verify loops and meta boxes; real content (Figma text + exported images) goes straight
   into live wp-admin. No WXR migration (live can't sideload media from `localhost` anyway).
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

- [ ] Launch Docker.app (Claude's compose stack needs the daemon)
- [ ] InfinityFree signup → new site on free subdomain → enable SSL → Softaculous WordPress
      install → hand Claude the public URL
- [ ] Duplicate the Estatein Figma community file to drafts
- [ ] Export **full-page screenshots of every page at desktop width** (tablet/mobile too if
      quick) → `docs/figma/`
- [ ] Export assets: logo, icons, hero/section images (WebP where possible) → theme assets
- [ ] Confirm from the Figma: does a **property single** page design exist? Correct the page
      status table
- [ ] Later, once CPTs are deployed: enter real content on live (properties, testimonials,
      FAQs, page text)

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

- [x] **Properties archive**: hero, **working** filter bar, grid, pagination
- [x] **Contact**: hero, contact info cards, hand-rolled form → `inquiry` CPT, offices
- [x] Property single (`single-property.php`) — spec panel + prefilled enquiry form
- [x] About Us: journey/stats, values panel, achievements, 6-step process, team
- [x] Services: hero + three service groups with side CTAs

#### Phase E · Creativity

- [x] JSON-LD structured data (RealEstateAgent / SingleFamilyResidence + Offer / FAQPage)
- [x] Working property filters (server-side via `pre_get_posts`, allowlist-validated)
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

#### Deploy — **blocking deliverable, not yet done**

- [ ] `growmodo-theme.zip` (237KB, built) → live wp-admin upload + activate
- [ ] Live settings: permalinks `Post name`, static front page, menus assigned
- [ ] Content entered on live (properties w/ meta + images, testimonials, FAQs, pages)
- [ ] Verify every page on the public URL; then the DoD's live-site box can be ticked

#### Phase F · Verification — checkpoint 22:15

- [ ] Responsive: every shipped page at 3 breakpoints + one in-between width
- [ ] SEO: title-tag, meta descriptions, one `h1` per page, alt text, semantic landmarks
- [ ] Performance: image sizes/WebP, lazy loading, no unused CSS/JS, scripts in footer
- [ ] Accessibility: keyboard-only walkthrough, focus visible, contrast spot-check
- [ ] `phpcs` — zero errors

#### Phase G · Omit gate (hard) + final deploy — checkpoint 22:45

- [ ] Every section vs the Definition of Done — anything failing is **removed** from
      templates, nav, and live content (mark ~~Omitted~~ above, note in
      the write-up as a scoped decision)
- [ ] Final zip → live; Karl re-deploys; content/menus verified
- [ ] Cross-browser: Chrome, Firefox, Safari; real phone if possible
- [ ] All links, forms, hover states, mobile menu verified live

#### Phase H · Docs + submit — before midnight

- [ ] the write-up → final 1–2 pages: **decisions-focused** (architecture, triage,
      security approach, plugins/tools used) — per the write-up decision, no
      duration/tooling-process narrative
- [ ] Repo README: what it is, live URL, theme install instructions
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
