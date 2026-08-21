# Deploy checklist — InfinityFree

Everything below happens in the browser; InfinityFree has no SSH or WP-CLI. Work top to
bottom, it takes about 15 minutes.

## 0. Before you start

You need, from the repo root:

- `growmodo-theme.zip` — build it from the repo root with `zip -r growmodo-theme.zip growmodo`
  (590KB). The `growmodo/` folder has to be *inside* the zip, not zipped from within, or
  WordPress installs the theme under the zip's name instead of its own. It is not committed,
  since a zip in Git is a binary that changes on every build
- `deploy/growmodo-content.xml` — the demo content export: 6 properties, 6 testimonials,
  6 FAQs, 2 posts, 6 pages, the two menus, 8 images

## 1. Host and WordPress

1. InfinityFree → create a site on a free subdomain. Free subdomains carry SSL by default,
   so there is no certificate to order.
2. Script Installer (Softaculous) → **install WordPress** with protocol `https://`, an
   empty *In Directory* field, and no bundled plugins. Note the wp-admin credentials.
3. PHP version is **not selectable** on the free plan, and does not need to be — the theme
   uses no version-specific syntax.

## 2. Theme

1. wp-admin → **Appearance → Themes → Add New → Upload Theme**.
2. Choose `growmodo-theme.zip` → Install → **Activate**.

If the upload is rejected for size, upload via FTP instead: extract the zip locally and copy
the `growmodo/` folder into `/htdocs/wp-content/themes/`.

## 3. Settings (do these before importing content)

1. **Settings → Permalinks** → select **Post name** → Save.
   *Required — the `/properties/` archive 404s on the default permalink structure.*
2. **Settings → Reading** → Your homepage displays → **A static page** → set **Homepage** to
   *Home* and **Posts page** to *Insights* (both appear after the import; come back to this
   step if needed).

## 4. Content

Two options.

### Option A — import the export (faster, recommended)

1. On your machine, rewrite the export for your live domain:

   ```sh
   ./deploy/prepare-content.sh https://your-subdomain.example
   ```

   This repoints the images at the theme's own bundled assets, so the importer can fetch
   them from your live site rather than from localhost.

2. wp-admin → **Tools → Import → WordPress** → install the importer if prompted → upload
   `deploy/growmodo-content-live.xml`.
3. Assign posts to your admin user and **tick "Download and import file attachments"**.
4. Spot-check: Properties should list 6 listings with images and prices; Testimonials should
   have avatars; the villa should have two extra gallery images.

### Option B — enter it by hand

Create these, matching the fields exactly:

**Properties** (Properties → Add New; fill the *Details* box in the sidebar, set a featured
image from `growmodo/assets/img/property-N.jpg`, and write a one-line excerpt):

| Title | Beds | Baths | Price | Type | Location | Size | Year |
| --- | --- | --- | --- | --- | --- | --- | --- |
| Seaside Serenity Villa | 4 | 3 | 550000 | Villa | Malibu, California | 4200 | 2019 |
| Metropolitan Haven | 2 | 2 | 425000 | Apartment | New York City | 1150 | 2021 |
| Rustic Retreat Cottage | 3 | 2 | 375000 | Cottage | Aspen, Colorado | 1650 | 2008 |
| Skyline Penthouse | 3 | 3 | 1250000 | Penthouse | Chicago, Illinois | 3100 | 2022 |
| Garden Court Townhouse | 4 | 3 | 690000 | Townhouse | Portland, Oregon | 2400 | 2015 |
| Lakeside Bungalow | 2 | 1 | 415000 | Bungalow | Burlington, Vermont | 1250 | 1998 |

Key features go one per line in the *Key features* textarea. Uploading more images to a
listing gives it a gallery carousel — `villa-living.jpg` and `villa-pool.jpg` are the two
spare interiors.

**Testimonials** — rating 5, a client name and location, avatar from `avatar-N.jpg`.

**FAQs** — question as the title, answer as the content.

**Pages** — `Home`, `About Us`, `Services`, `Contact`, `Insights`, `Terms & Conditions`.
The slugs matter: `contact`, `about-us`, and `services` select their templates by slug, and
`terms-conditions` is what the enquiry forms' consent line links to.

## 5. Menus

**Appearance → Menus**:

1. Create **Primary** → add Home, About Us, Properties (custom link `/properties/`),
   Services → assign to **Primary Menu**.
   *Do not add Contact — it is the header button.*
2. Create **Footer** → add Terms & Conditions → assign to **Footer Menu**.

## 6. Verify on the live URL

Click through in a normal browser window, not the editor preview:

- [ ] `/` — hero image loads, properties/testimonials/FAQ sections populated, carousel arrows
      page through the cards
- [ ] `/properties/` — search for `cottage` (the URL should carry `?q=cottage` and be
      shareable); then filter by type, price, size and build year — results narrow on change,
      with no submit or reset button by design
- [ ] a single property — gallery thumbnails switch the main image, specs correct, pricing
      tables present, enquiry form prefilled with the listing
- [ ] `/contact/` — submit the form; expect the success notice, then confirm the submission
      appears under **Inquiries** in wp-admin
- [ ] `/about-us/`, `/services/`, `/insights/` and one blog post
- [ ] a bad URL like `/nope/` — styled 404
- [ ] on a phone: hamburger menu opens, closes, and navigates; footer socials sit in one row
- [ ] dismiss the announcement bar, reload — it stays dismissed

## 7. Final

- [ ] Site title set to **Estatein** (Settings → General) — the header and footer use it
- [ ] Update the live URL in `README.md`, then push
