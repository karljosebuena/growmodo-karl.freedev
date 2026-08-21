# Estatein — Custom WordPress Theme

A custom WordPress theme built from the [Estatein](https://www.figma.com/community/file/1314076616839640516)
Figma community template (by Produce UI, CC BY 4.0). Dark real-estate site with property
listings, testimonials, FAQs, and working enquiry forms.

**Live site:** <https://growmodo-karl.freedev.app>

- **No plugin dependencies** — custom post types, meta boxes, forms, and structured data all
  use core WordPress APIs
- **No build step** — one stylesheet with design tokens as CSS custom properties, vanilla JS
- **Responsive** at the design's three breakpoints (390 / 1440 / 1920)
- **`phpcs` clean** against WordPress Coding Standards — zero errors, zero warnings

Write-up of the process and decisions:
[`docs/development-notes.md`](docs/development-notes.md).

## Installing the theme

1. Zip the `growmodo/` directory (or download it from this repo).
2. In wp-admin: **Appearance → Themes → Add New → Upload Theme**, choose the zip, activate.
3. **Settings → Permalinks** → select **Post name** (required for the property archive).
4. **Settings → Reading** → set a static front page, and a posts page for the blog.
5. **Appearance → Menus** → create a menu with Home, About Us, Properties, Services and
   assign it to **Primary Menu**; optionally a second menu for **Footer Menu**.

Pages are picked up by slug: `contact`, `about-us`, `services`. The properties archive lives
at `/properties/`. Demo content — properties with meta and images, testimonials, FAQs, posts,
pages and menus — can be imported from `deploy/growmodo-content.xml`; see
[`deploy/DEPLOY.md`](deploy/DEPLOY.md).

## Content model

| Post type | Fields | Notes |
| --- | --- | --- |
| `property` | bedrooms, bathrooms, price, type, location, floor area, build year, key features (one per line) + featured image, excerpt | Public, archive at `/properties/`. Extra images uploaded to the listing become its gallery |
| `testimonial` | rating, client name, client location + featured image | Admin-only, shown in page sections |
| `faq` | title + content | Admin-only, rendered as disclosures |
| `inquiry` | email, phone, selected property, and the submitted enquiry fields | Private; created by form submissions, not by hand |

Every option list is declared once and reused: `growmodo_property_types()`
(`growmodo/inc/property-query.php`) fills the editor select, the archive filter and the
enquiry form's Property Type, and validates all three on save. Price and size bands, room
counts, contact methods and enquiry types follow the same pattern — the function that draws
the control is the allowlist that guards the write.

## Local development

Requires Docker.

```bash
docker compose up -d
docker compose run --rm cli wp core install \
  --url=http://localhost:8080 --title=Estatein \
  --admin_user=admin --admin_password=admin \
  --admin_email=admin@example.com --skip-email
docker compose run --rm cli wp theme activate growmodo
docker compose run --rm cli wp rewrite structure '/%postname%/'
```

The site runs at <http://localhost:8080> with the theme directory mounted, so edits are live.

### Coding standards

```bash
composer install
composer lint       # phpcs
composer lint:fix   # phpcbf
```

## Theme structure

```
growmodo/
├── functions.php              bootstrap — loads inc/ modules only
├── style.css                  design tokens + all component styles
├── inc/
│   ├── setup.php              theme supports, menus, image sizes
│   ├── assets.php             enqueues (deferred script, footer, .min when available)
│   ├── icons.php              inline SVG icon library
│   ├── helpers.php            formatting, property images, card/section rendering
│   ├── post-types.php         the four CPTs and their registered meta
│   ├── meta-boxes.php         editor screens — nonce, capability, sanitise
│   ├── admin-columns.php      list-table columns for properties and enquiries
│   ├── property-query.php     option lists, archive search, facets, bands
│   ├── pricing.php            pricing breakdown derived from the listing price
│   ├── form-handler.php       admin-post handler for every form
│   ├── seo.php                title, description, OG/Twitter tags
│   └── schema.php             JSON-LD structured data
├── template-parts/
│   ├── home/                  hero, features, properties, testimonials, faq
│   ├── card-*.php             property, testimonial, faq, post, client, image cards
│   ├── carousel.php           scroll-snap track + pager, shared by every card section
│   ├── property-search.php    server-side search form
│   ├── property-filters.php   browser-side filter row
│   ├── property-pricing.php   pricing tables
│   ├── faq-section.php        FAQ loop, shared by Home and property pages
│   ├── form-inquiry.php       enquiry form — three field sets, chosen by type
│   ├── service-cta.php        services group CTA panel
│   ├── section-head.php       heading + lede + optional action
│   ├── announcement-bar.php   dismissible top bar
│   └── cta-banner.php         shared pre-footer CTA
├── front-page.php             Home
├── archive-property.php       search + filters + results
├── single-property.php        gallery, specs, pricing, FAQ, enquiry form
├── page-{contact,about-us,services}.php
├── archive.php  single.php  search.php  searchform.php  sidebar.php   blog
├── page.php  index.php  404.php
└── assets/{js,img}
```

## Credits

Design: Estatein by Produce UI, licensed CC BY 4.0. Typeface: Urbanist (Google Fonts).
