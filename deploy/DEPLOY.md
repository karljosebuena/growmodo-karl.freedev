# Deploy checklist — InfinityFree

Everything below happens in the browser; InfinityFree has no SSH or WP-CLI. Work top to
bottom, it takes about 15 minutes.

## 0. Before you start

You need, from the repo root:

- `growmodo-theme.zip` — the theme, ready to upload (237KB)
- `deploy/growmodo-content.xml` — the demo content export

## 1. Host and WordPress

1. InfinityFree → create a site on a free subdomain → **enable free SSL**.
2. Control panel → Softaculous → **install WordPress**. Note the wp-admin credentials.
3. Control panel → **set PHP to 8.3** (the theme requires 8.1+).

## 2. Theme

1. wp-admin → **Appearance → Themes → Add New → Upload Theme**.
2. Choose `growmodo-theme.zip` → Install → **Activate**.

If the upload is rejected for size, upload via FTP instead: extract the zip locally and copy
the `growmodo/` folder into `/htdocs/wp-content/themes/`.

## 3. Settings (do these before importing content)

1. **Settings → Permalinks** → select **Post name** → Save.
   *Required — the `/properties/` archive 404s on the default permalink structure.*
2. **Settings → Reading** → Your homepage displays → **A static page** → set **Homepage** to
   the page named *Home* (it appears after the import; come back to this step if needed).

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
4. Spot-check: Properties should list 3 listings with images and prices; Testimonials should
   have avatars.

### Option B — enter it by hand

Create these, matching the fields exactly:

**Properties** (Properties → Add New; fill the *Details* box in the sidebar, set a featured
image from `growmodo/assets/img/property-N.webp`, and write a one-line excerpt):

| Title | Beds | Baths | Price | Type | Location |
| --- | --- | --- | --- | --- | --- |
| Seaside Serenity Villa | 4 | 3 | 550000 | Villa | Malibu, California |
| Metropolitan Haven | 2 | 2 | 425000 | Apartment | New York City |
| Rustic Retreat Cottage | 3 | 2 | 375000 | Cottage | Aspen, Colorado |

**Testimonials** — rating 5, a client name and location, avatar from `avatar-N.webp`.

**FAQs** — question as the title, answer as the content.

**Pages** — `Home`, `About Us`, `Services`, `Contact`, `Terms & Conditions`.
The slugs matter: `contact`, `about-us`, and `services` select their templates by slug.

## 5. Menus

**Appearance → Menus**:

1. Create **Primary** → add Home, About Us, Properties (custom link `/properties/`),
   Services → assign to **Primary Menu**.
   *Do not add Contact — it is the header button.*
2. Create **Footer** → add Terms & Conditions → assign to **Footer Menu**.

## 6. Verify on the live URL

Click through in a normal browser window, not the editor preview:

- [ ] `/` — hero image loads, properties/testimonials/FAQ sections populated
- [ ] `/properties/` — filter by type and by max price; results change; **Reset** clears
- [ ] a single property — spec panel correct, enquiry form present
- [ ] `/contact/` — submit the form; expect the success notice, then confirm the submission
      appears under **Inquiries** in wp-admin
- [ ] `/about-us/` and `/services/`
- [ ] a bad URL like `/nope/` — styled 404
- [ ] on a phone: hamburger menu opens, closes, and navigates
- [ ] dismiss the announcement bar, reload — it stays dismissed

## 7. Final

- [ ] Site title set to **Estatein** (Settings → General) — the header and footer use it
- [ ] Update the live URL in `README.md`, then push
