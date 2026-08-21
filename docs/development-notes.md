# Development Process & Decisions — Estatein WordPress Theme

**Live site:** <https://growmodo-karl.freedev.app>
**Repository:** <https://github.com/karljosebuena/growmodo-karl.freedev>
**Design source:** [Estatein](https://www.figma.com/community/file/1314076616839640516) Figma
community template by Produce UI (CC BY 4.0) — 6 pages × 3 breakpoints (1920 / 1440 / 390).

---

## Development process

I worked from the Figma exports rather than by eye. The three desktop renders were sliced
into readable bands and the palette was derived by counting pixels across the full page
renders: `#141414` and `#1a1a1a` account for roughly 85% of the design, `#703bf7` is the
single accent, `#262626` every border, `#ffe500` the rating stars. Those became CSS custom
properties before any component was written, so nothing was colour-matched by hand. The two greys
divide by role, which took a while to see: content cards carry the page's own `#141414`
behind a hairline border and a light halo, and the lighter `#1a1a1a` is reserved for panels —
the feature strip, the service CTAs, the filter frame, the note banner. Cards had it the
wrong way round for most of the build, which made every card section read heavier than the
design.

Build order followed the brief's stated priority — shared shell first (header, footer,
pre-footer CTA), then the Home page, then the remaining pages in descending priority:
Properties archive, property detail, Contact, About Us, Services. Each layer was verified in
a real browser at 1440px and 390px before the next was started, and `phpcs` was run
continuously rather than as a final pass.

The Figma shipped no exported assets, so the photography (hero building, property
thumbnails, testimonial avatars, team portraits, the About visual, two villa interiors) was
cropped out of the page renders — 14 files, 424KB in total, of which any one page loads a
handful. Six are WebP; the eight used as demo content are JPEG, because the live host answers
requests for `.webp` with a bot-check page and WordPress's importer could not fetch them at
all (recorded in `RESOLVED_GOTCHAS.md`). Icons are not images at all: they are an inline SVG library of
37 glyphs, so the design needs no icon font and no extra requests.

## Theme decisions

**Classic PHP theme, no build step.** One stylesheet with design tokens, vanilla JS,
templates in the standard WordPress hierarchy. A bundler would have cost setup time without
improving anything a reviewer can see, and the theme installs as a plain zip on any host.

**Custom fields with core APIs instead of ACF.** The brief allows "ACF or similar". I used
`register_post_meta()` plus hand-rolled `add_meta_box()` screens. Two reasons: the whole
site then ships inside the theme zip with no plugin to install or field groups to re-sync on
the live host, and the save handlers show the security work explicitly — nonce, capability
check, and per-field sanitisation on every write.

**Four post types, everything else in templates.** `property` (bedrooms, bathrooms, price,
type, location, floor area, build year, key features), `testimonial` (rating, client name,
location), `faq`, and a private `inquiry` type that stores form submissions. Hero copy, feature cards, service groups,
values and process steps are template data — they are structural to the design, not content
an editor is expected to re-order, and making six more CPTs would have cost time without
making the site more editable in practice.

**Every option list is declared once.** `growmodo_property_types()` renders the editor's
`<select>`, populates the enquiry form's Property Type, and validates both on save — a type
not in that list cannot be stored. The same pattern covers price bands (shared by the archive
filter and the form's Budget), room counts, inquiry types and contact methods: the meta box's
`select` names the function returning its options, so the list that draws the control is also
the allowlist that guards the write.

**Listing order lives in one function.** The home carousel and the properties archive have to
agree, and when they did not the same six listings appeared in opposite orders on the two
pages — the archive had no `orderby` at all and fell back to newest first. Every curated loop
— properties, testimonials, FAQs — now calls `growmodo_curated_order()`: the editor's Order
field, then oldest first.

**Forms are hand-rolled and store submissions as posts.** They POST to `admin-post.php`,
verify a nonce, check a honeypot, sanitise every field, and are saved as private `inquiry`
posts visible in wp-admin. Free hosts frequently block PHP `mail()`, so an email-only
contact form is the one thing most likely to fail silently in front of a reviewer. Storing
submissions means the form is verifiably working on the live site.

The design has two forms, and the field set is chosen by the form's type rather than shared:
"Let's Make it Happen" beside the listings asks what the visitor is looking for in four
columns, "Let's Connect" on the contact page asks why they are writing in three. One form
serving both would have asked a general enquirer for a bedroom count. Rows are built from
arrays rather than written out field by field — thirteen near-identical blocks is thirteen
chances to forget an `esc_attr` — and every select validates against its own option list on
the way in, so a value the form never offered cannot reach the database. The consent
checkbox is enforced server-side as well as marked `required`: the attribute is the
browser's opinion, and a POST need not come from the form at all.

Preferred Contact Method is rendered as a choice between two methods, not as the two extra
text inputs the mock draws inside each box. Those would collect the email and phone number
given two rows above, and a form that asks twice is a defect rather than fidelity.

**Search and filtering are two different mechanisms.** The design shows two controls on the
properties archive, and they behave differently:

- **Search** is the only one that asks the server anything. A plain GET form on `q` — not `s`,
  which would make WordPress treat the request as a site-wide search and hand it to
  `search.php`, losing the archive — mapped onto the main query in `pre_get_posts`. A search
  is therefore a real shareable URL that works with JavaScript disabled.
- **Filters** are a view over whatever search returned, applied to the rendered cards in the
  browser. There is no submit button and no reset button: every pill's first option *is* its
  cleared state. Nothing is submitted, so the selects carry no `name`, and there is no
  `meta_query` duplicating the same predicate on the server — one predicate, one
  implementation.

Two consequences worth naming. Filtering in the browser can only see what is on the page, so
the archive loads its whole result set (capped at 24) instead of paginating, and reports the
total when the cap truncates; the results use the same carousel pager as every other card
section, which is what the design shows anyway. And because the filters only work with
JavaScript, CSS hides them unless `<html>` carries `has-js`, set by the existing pre-paint
`<head>` snippet — so nobody is offered a control that cannot work, at no cost in layout
shift.

Filter options are derived from the properties actually loaded rather than from a fixed list,
so every option offered matches at least one visible card; a search that matches nothing
renders no filters at all. Floor area and build year are stored as post meta
alongside price and type, so the design's "Property Size" and "Build Year" pills filter real
data and show up in the property's "At a glance" list. Size filters by band because sizes are
continuous; build year filters by exact year, derived from the listings. Bands are half-open —
lower bound in, upper bound out — so a property sitting exactly on the number two adjacent
bands share falls in one of them rather than both.

## Deviations from the Figma (deliberate)

- **FAQ "Read More" expands the answer** rather than navigating. The design shows the answer
  visible with a Read More button, so the answer is rendered in full and clamped to two lines;
  the button expands it. FAQs are not publicly queryable, so there is no page to link to —
  expanding gives the affordance a real function. Without JavaScript the whole answer is
  simply visible and no button appears.
- **"View All" buttons appear only where a destination exists.** Properties has an archive,
  so it keeps its button. Testimonials and FAQs are not publicly queryable post types, so
  those buttons were dropped rather than pointed at nothing.
- **The pricing breakdown is computed, not authored.** I first cut this section on the grounds
  that inventing figures would ship fabricated content. That was wrong: the Figma's own numbers
  all derive from the listing price at a fixed rate, which the worked example proves — 2%
  transfer tax, 1.2% annual property tax, 20% deposit, and flat fees for conveyancing,
  inspection and insurance reproduce every figure on its $1,250,000 example exactly. So the
  section exists, driven by `growmodo_pricing_rates()` (filterable, since the rates are
  jurisdiction-specific), and the note above the tables says out loud that they are estimates.
- **The gallery is native attached media.** Images uploaded to a listing become its gallery,
  featured image first — no ID list in a meta field and nothing to keep in sync. A listing with
  one image renders that image and no carousel, which is why only the villa the Figma
  illustrates has a full gallery in the demo content: those are the two extra photographs the
  community file exports at usable size.
- **Key features are one per line** in a textarea rather than a repeater. An amenities list is
  a list of strings; a repeater would be more UI for the same data.

## What the review passes caught

The theme was audited twice — once for accessibility, once against the project's own coding
standards — and both passes found real defects rather than rubber-stamping the work. The
findings worth knowing about:

- **A stored XSS in the JSON-LD block.** `JSON_UNESCAPED_SLASHES` meant a `</script>`
  sequence in a property title escaped the script element and executed as markup — reachable
  by any Editor, since Editors have `unfiltered_html`. Fixed with `JSON_HEX_TAG` and slash
  escaping, then verified against a live payload.
- **Every post title was output unescaped.** Chasing the first bug surfaced the same payload
  executing in `<h1>` and card headings via bare `the_title()`. All title output now goes
  through `esc_html( get_the_title() )`. `the_content()` is deliberately left alone —
  escaping it would destroy formatting, and kses already filters it for unprivileged users.
- **Blog posts printed a PHP deprecation notice.** `comments_template()` with no
  `comments.php` falls back to core's theme-compat file, which announces its own
  deprecation into the page. Comments were out of scope, so the call is gone.
- **Property enquiries recorded no property.** The read-only "Selected property" field had no
  `name`, so nothing was submitted — every lead arrived unattributed. Now a validated hidden
  id, stored as meta, shown as an admin column, with a documented
  `growmodo_inquiry_created` action for notification.
- **Four broken or mislabelled footer links**, and a FAQ schema that advertised more answers
  than the page displayed.

Each fix is documented in the commit that made it, with the reasoning that led to it — the
commit log is the audit trail.

## Plugins & tools

| Tool | Purpose |
| --- | --- |
| **No plugins** | The theme has no plugin dependencies at all — CPTs, meta boxes, forms, and SEO markup are all core APIs |
| Docker (`wordpress:php8.3-apache` + MySQL) | Local development, PHP pinned to the host's version |
| WP-CLI (via the Docker image) | Local install, test content, menu setup |
| phpcs + WordPress Coding Standards 3.x | Enforced continuously; the theme passes with zero errors and zero warnings across 50 files |
| axe-core | Accessibility audit at 1440px and 390px |
| Playwright | Cross-breakpoint visual verification and end-to-end form testing |
| Pillow (Python) | Slicing the Figma renders, sampling the palette, cropping and converting assets to WebP |

## Performance & SEO

Measured on the Home page — logged out, at 1440px, local warm cache — not estimated:

| Metric | Value |
| --- | --- |
| Requests | 14 (12 same-origin including the document, 2 for the webfont) |
| Transferred | ~213KB — HTML 14KB, CSS 7KB (gzipped, from 36KB minified), JS 9KB (2KB ours, the rest core's emoji script), images 183KB |
| Largest Contentful Paint | 72ms |
| DOMContentLoaded | 48ms |
| Cumulative Layout Shift | **0** — zero shift entries recorded |
| Image library (all pages) | 14 files, 424KB total (6 WebP, 8 JPEG — see the host note below); Home loads 183KB of it |

- No plugins, no framework, no build output: one stylesheet, one deferred script, inline SVG
  icons, zero external requests except the Urbanist webfont.
- The JPEG demo photography costs about 45KB on Home against the WebP it replaced. That is a
  deliberate trade on this host: WebP is unreadable to any client that does not run the
  bot-check JavaScript, which includes social-preview scrapers fetching a listing's
  `og:image`. Images a crawler can actually read were worth 45KB.
- `width`/`height` on every image, `fetchpriority="high"` on the hero, `loading="lazy"` below
  the fold, and two cropped image sizes registered so grids never reflow — hence CLS 0.
- JSON-LD structured data: `RealEstateAgent` for the site, `SingleFamilyResidence` with an
  `Offer` on each property, and `FAQPage` generated from the published FAQ entries.
- Exactly one `<h1>` per page (verified across all six templates), semantic landmarks,
  `title-tag` support, and no image missing `alt`.

## Accessibility

Audited with axe-core plus manual keyboard testing and computed-contrast checks at 1440px
and 390px. No critical issues; six findings were raised and all six were fixed:

- **Focus rings on form controls** — `outline: none` on `:focus` had replaced the sitewide
  2px ring with a 1px border tint measuring ~3:1. Now scoped to
  `:focus:not(:focus-visible)`, so pointer focus stays quiet and keyboard focus keeps the
  full indicator.
- **In-content link contrast** — the accent measured 4.01:1 on the page background, below
  the 4.5:1 minimum. Body links are now white + underline (18.4:1), accent on hover.
- **Landmarks** — the announcement bar is a labelled region and the CTA banner moved inside
  `<main>`, so no content sits outside a landmark.
- **Form-control boundaries** — the design's `#262626` border measures 1.22:1, failing the
  3:1 floor of WCAG 1.4.11 for identifying UI components. Form controls use a dedicated
  `--c-border-input` (#666) at 3.21:1. This is a deliberate, documented divergence from the
  Figma: a field you cannot see the edge of is a usability defect, not a style choice.
- **Skip link** — WordPress core's `.screen-reader-text:focus` rule was recolouring it to
  `#ddd` on `#444`; dropping that redundant class restores white-on-accent at 5.74:1.
- **Redundant announcements** — the inert pager arrow is `aria-hidden` rather than carrying
  a meaningless `aria-disabled`, and team portraits use `alt=""` because the name follows as
  text.

Verified passing: `#999999` muted text measures 6.47:1 on the page background and 6.11:1 on
cards; white on the accent button is 5.74:1. Every form field has a real `<label for>`. The
honeypot uses the standard accessible pattern (`tabindex="-1"` inside an `aria-hidden`
wrapper) and axe confirms it introduces no focus trap. The FAQ disclosures and the
`aria-expanded` mobile nav toggle (which also closes on `Escape`) are keyboard-operable, and
`prefers-reduced-motion` disables the rotating hero seal, the scroll reveal, and all
transitions.

## Verification performed

- **Responsive:** every page checked for horizontal overflow at eight widths from 360 to
  1920px — all clean. The content column matches the Figma frames' own: 358px at 390,
  1282px at 1440, 1596px at 1920, with the content edge at 16 / 79 / 162px. Two real bugs
  were caught this way: long email values in tag pills pushed `/contact/` 3px past the
  viewport at 390px, and `--container` was being applied as the box width rather than the
  content width, making every page's column 80px too wide at 1440 and 80px too narrow at
  1920 simultaneously.
- **Forms:** submitted end-to-end; the submission appears as a private `inquiry` post with
  correct meta. Nonce enforcement was confirmed by replaying the POST with no nonce and with
  a forged nonce — both rejected, no post created.
- **Search:** `?q=cottage` returns the one cottage and narrows the filter options to just
  what that result contains. `?q=<script>alert(1)</script>` is stripped and escaped — no
  inline script reaches the document.
- **Filters:** "Over $1,000,000" returns the one penthouse; "Over 3,000 sq ft" the villa and
  the penthouse; the two together the penthouse alone; and an impossible pair — over $1m
  *and* under 1,500 sq ft — returns nothing, showing the empty-state notice. The announced
  count tracked every step (6 → 1 → 2 → 1 → 0 → 6 on clearing), and options prune to what
  the current results actually contain.
- **Console:** zero errors and zero warnings on every page. (A deliberately bad URL logs its
  own 404 status — that is the browser reporting the response, not the theme.)
- **`phpcs`:** zero errors, zero warnings across 50 files.
- **Demo content export:** re-checked file by file — every one of the 8 attachments in
  `deploy/growmodo-content.xml` exists in the theme's own `assets/img/`, so the live import
  sideloads all of them rather than silently leaving cards imageless.

## Editable vs template content — a decision to know about

The Home page, Properties archive, property detail pages and the blog are fully
content-driven. **About Us, Services and Contact are not**: their copy — including the
office addresses, phone number and email on the Contact page — lives in the page templates,
not the editor. Clicking "Edit Page" on those three shows an empty editor.

That was a deliberate triage call, and it is the one I would revisit first with more time.
The reasoning: those pages are fixed structural content in a fixed design, so making them
editable means either six more post types or a page-builder's worth of ACF flexible content,
and the cost lands on every deploy and on the client's learning curve rather than on me. What
I would change: the Contact page's office details genuinely should be editable, because
addresses and phone numbers change and a developer should not be in that loop. That is a
small options page or a `contact_office` CPT, and it is the first thing I would add.

## Still missing from the Figma

Named honestly rather than left for the reviewer to find. Each is a section that
exists in the design and not in the build, and in every case the blocker is
content or a data model rather than layout:

- **Contact → "Explore Estatein's World"**, a six-image photo mosaic. Needs six
  photographs the community file does not export.

## Known limitations

- Photography is cropped from the Figma page renders because the community file ships no
  exported assets; a production build would use the original high-resolution images.
- Only one demo listing has enough photographs for the gallery carousel; the other five show
  their single featured image. The carousel appears when a listing has more than one image, so
  this is a property of the demo content rather than of the template.
- Office-location filter tabs on the Contact page (All / Regional / International) are not
  implemented — two offices do not justify a filter.
- Newsletter and contact submissions are stored rather than emailed. On a host with reliable
  mail, adding `wp_mail()` to the existing handler is a few lines.
- The demo content ships a Terms & Conditions page but no privacy policy — writing legal text
  for a demo would be inventing content. The consent line links whichever of the two exists
  *and is published*, and renders the other as plain text, so half a sentence never becomes a
  link to a 404. Publishing a privacy policy turns it into a link with no code change.
