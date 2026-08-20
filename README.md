# Estatein — Custom WordPress Theme

A custom WordPress theme built from the [Estatein](https://www.figma.com/community/file/1314076616839640516)
Figma community template (by Produce UI, CC BY 4.0). Dark real-estate site with property
listings, testimonials, FAQs, and working enquiry forms.

**Live site:** _added at deploy_

- **No plugin dependencies** — custom post types, meta boxes, forms, and structured data all
  use core WordPress APIs
- **No build step** — one stylesheet with design tokens as CSS custom properties, vanilla JS
- **Responsive** at the design's three breakpoints (390 / 1440 / 1920)
- **`phpcs` clean** against WordPress Coding Standards — zero errors, zero warnings

## Installing the theme

1. Zip the `growmodo/` directory (or download it from this repo).
2. In wp-admin: **Appearance → Themes → Add New → Upload Theme**, choose the zip, activate.
3. **Settings → Permalinks** → select **Post name** (required for the property archive).
4. **Settings → Reading** → set a static front page.
5. **Appearance → Menus** → create a menu with Home, About Us, Properties, Services and
   assign it to **Primary Menu**; optionally a second menu for **Footer Menu**.

Pages are picked up by slug: `contact`, `about-us`, `services`. The properties archive lives
at `/properties/`.

## Content model

| Post type | Fields | Notes |
| --- | --- | --- |
| `property` | bedrooms, bathrooms, price, type, location + featured image, excerpt | Public, archive at `/properties/` |
| `testimonial` | rating, client name, client location + featured image | Admin-only, shown in page sections |
| `faq` | title + content | Admin-only, rendered as disclosures |
| `inquiry` | email, phone | Private; created by form submissions, not by hand |

Property type options are defined once in `growmodo_property_types()`
(`growmodo/inc/property-query.php`) and shared by the editor select, the archive filter, and
validation.

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
│   ├── assets.php             enqueues (deferred script, footer)
│   ├── icons.php              inline SVG icon library
│   ├── helpers.php            price formatting, card/section rendering
│   ├── post-types.php         the four CPTs and their registered meta
│   ├── meta-boxes.php         editor screens — nonce, capability, sanitise
│   ├── property-query.php     type allowlist + archive filtering
│   ├── form-handler.php       admin-post handler for enquiry forms
│   └── schema.php             JSON-LD structured data
├── template-parts/
│   ├── home/                  hero, features, properties, testimonials, faq
│   ├── card-*.php             property, testimonial, faq cards
│   ├── form-inquiry.php       shared enquiry form
│   ├── section-head.php       heading + lede + optional action
│   ├── announcement-bar.php   dismissible top bar
│   └── cta-banner.php         shared pre-footer CTA
├── front-page.php             Home
├── archive-property.php       listings + working filters
├── single-property.php        property detail + enquiry form
├── page-{contact,about-us,services}.php
├── page.php  index.php  404.php
└── assets/{js,img}
```

## Credits

Design: Estatein by Produce UI, licensed CC BY 4.0. Typeface: Urbanist (Google Fonts).
