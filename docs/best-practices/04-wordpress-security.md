# 04 — WordPress Security: Sanitize, Escape, Verify

Sources: WordPress Security APIs, referenced from the coding standards handbook —
[escaping](https://developer.wordpress.org/apis/security/escaping/),
[sanitizing](https://developer.wordpress.org/apis/security/sanitizing/),
[nonces](https://developer.wordpress.org/apis/security/nonces/).

The mental model:

```
                    ┌──────────────┐
   request ─────▶   │  VALIDATE    │  reject anything not of the expected shape
                    │  SANITIZE    │  clean what's left
                    └──────┬───────┘
                           ▼
                    ┌──────────────┐
                    │  AUTHORIZE   │  nonce  +  current_user_can()
                    └──────┬───────┘
                           ▼
                    ┌──────────────┐
   store / query    │  PREPARE     │  $wpdb->prepare()
                    └──────┬───────┘
                           ▼
                    ┌──────────────┐
   response ◀────── │  ESCAPE      │  as late as possible, matched to context
                    └──────────────┘
```

**Untrusted data comes from many sources** — users, third-party sites, APIs, and your own
database. Even administrator input requires sanitization. Treat the database as untrusted:
data may have been stored before the current rules existed.

---

## 1. Sanitizing input

Sanitization is "the process of securing/cleaning/filtering input data." **Validation is
preferred where possible** because it's more specific; sanitization is the fallback for
general-purpose data.

### Text & content

| Function | Use for |
| --- | --- |
| `sanitize_text_field()` | Single-line text: strips tags, checks UTF-8, trims extra whitespace |
| `sanitize_textarea_field()` | Multi-line text input |
| `wp_kses()` | Text where a specific allowlist of HTML is permitted |
| `wp_kses_post()` | Text where post-content HTML is permitted |

### Identifiers & naming

| Function | Use for |
| --- | --- |
| `sanitize_key()` | Array keys, option names, identifiers |
| `sanitize_user()` | Usernames |
| `sanitize_title()` | Slugs from titles |
| `sanitize_title_for_query()` | Titles used in a query |
| `sanitize_title_with_dashes()` | Dash-separated slugs |

### Specialised data

| Function | Use for |
| --- | --- |
| `sanitize_email()` | Email addresses |
| `sanitize_url()` / `esc_url_raw()` | URLs for storage |
| `sanitize_file_name()` | Uploaded/derived file names |
| `sanitize_hex_color()`, `sanitize_hex_color_no_hash()` | Colour values |
| `sanitize_html_class()` | CSS class names |
| `sanitize_mime_type()` | MIME types |
| `absint()` / `(int)` / `intval()` | Integers (`absint()` for non-negative) |

### WordPress-specific

| Function | Use for |
| --- | --- |
| `sanitize_meta()` | Post/term/user metadata |
| `sanitize_option()` | Settings and options |
| `sanitize_sql_orderby()` | `ORDER BY` clauses |
| `sanitize_term()`, `sanitize_term_field()` | Taxonomy terms |

### Pattern

```php
$email  = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
$title  = isset( $_POST['title'] ) ? sanitize_text_field( wp_unslash( $_POST['title'] ) ) : '';
$count  = isset( $_POST['count'] ) ? absint( $_POST['count'] ) : 0;
$status = isset( $_POST['status'] ) ? sanitize_key( $_POST['status'] ) : 'draft';

// Validation beats sanitization when the set of valid values is known.
$allowed_statuses = array( 'draft', 'publish', 'pending' );
if ( ! in_array( $status, $allowed_statuses, true ) ) {
	return new WP_Error( 'invalid_status', __( 'Invalid status.', 'growmodo' ) );
}

if ( ! is_email( $email ) ) {
	return new WP_Error( 'invalid_email', __( 'Please enter a valid email.', 'growmodo' ) );
}
```

Note `wp_unslash()` — WordPress adds slashes to superglobals, so strip them before use.

---

## 2. Escaping output

**Escape as late as possible, ideally as the data is being output.** The handbook's
reasoning: it makes code review faster, protects against a variable being modified
between assignment and use, and keeps intent clear at the point of rendering.

| Function | Use when |
| --- | --- |
| `esc_html()` | Data displayed inside an HTML element — removes HTML |
| `esc_attr()` | Everything printed into an HTML attribute value |
| `esc_url()` | All URLs, including `href` and `src` attributes (HTML-safe output) |
| `esc_url_raw()` | URLs being **stored** in the database or used in redirects/HTTP calls |
| `esc_js()` | Inline JavaScript |
| `esc_textarea()` | Content inside a `<textarea>` |
| `esc_xml()` | XML block content |
| `wp_kses()` | Content where only an explicit allowlist of tags/attributes is permitted |
| `wp_kses_post()` | Content where all post-content HTML is permitted |
| `wp_kses_data()` | Content where only comment-level HTML is permitted |
| `wp_json_encode()` | Data passed to JS (prefer `wp_localize_script()` / `wp_add_inline_script()`) |

Translation-aware variants exist for each and should be used instead of nesting calls:
`esc_html__()`, `esc_html_e()`, `esc_attr__()`, `esc_attr_e()`, `esc_html_x()`.

### Examples

```php
<a href="<?php echo esc_url( $link ); ?>" title="<?php echo esc_attr( $title ); ?>">
	<?php echo esc_html( $label ); ?>
</a>

<textarea name="bio"><?php echo esc_textarea( $bio ); ?></textarea>

<div class="content"><?php echo wp_kses_post( $rich_text ); ?></div>

<?php esc_html_e( 'Submit', 'growmodo' ); ?>
```

### The one exception

When output escaping would break the generated string (building a script tag, assembling
HTML fragments), escape at creation time and name the variable with an `_escaped`,
`_safe` or `_clean` suffix so reviewers can see it's already handled.

```php
$link_safe = sprintf( '<a href="%s">%s</a>', esc_url( $url ), esc_html( $label ) );
echo $link_safe; // phpcs:ignore WordPress.Security.EscapeOutput -- Escaped above.
```

### Don't confuse the pair

- **Sanitize** = input, on the way in.
- **Escape** = output, on the way out.
- They are not interchangeable. `sanitize_text_field()` on output is wrong;
  `esc_html()` before a database write is wrong.

---

## 3. Nonces (CSRF protection)

A nonce ("number used once") protects URLs and forms from misuse. WordPress nonces are
hashes with a limited lifetime — they are **not** strictly single-use.

> **"Nonces should never be relied on for authentication, authorization, or access
> control."** Always pair them with `current_user_can()`.

### Creating

```php
// For a URL.
$secure_url = wp_nonce_url( $base_url, 'trash-post_' . $post->ID );

// For a form (outputs hidden nonce + referrer fields).
wp_nonce_field( 'delete-comment_' . $comment_id );

// Raw token, for custom/AJAX use.
$token = wp_create_nonce( 'my-action_' . $post->ID );
```

Make action strings **specific** (include the object ID) so a nonce can't be reused across
contexts.

### Verifying — match the method to the origin

```php
// Admin screens: checks nonce AND referrer, dies on failure.
check_admin_referer( 'delete-comment_' . $comment_id );

// AJAX: checks the nonce only, dies on failure by default.
check_ajax_referer( 'process-comment' );

// Custom contexts: returns false on failure, you handle it.
if ( false === wp_verify_nonce( $_POST['my_nonce'], 'action-name' ) ) {
	wp_nonce_ays();
}
```

### Lifetime

Default 24 hours, effectively 12–24 h due to the two-tick validation window. Nonces are
per-user and per-context and become invalid on logout. They depend on `NONCE_KEY` and
`NONCE_SALT` in `wp-config.php`. To change the window:

```php
add_filter(
	'nonce_life',
	function () {
		return 4 * HOUR_IN_SECONDS;
	}
);
```

### The complete pattern

```php
// Rendering the form.
wp_nonce_field( 'update-post_' . $post->ID );

// Handling the submission — authentic (nonce) + intended (referrer) + authorized (capability).
if ( check_admin_referer( 'update-post_' . $post->ID )
	&& current_user_can( 'edit_post', $post->ID )
) {
	// Safe to proceed.
}
```

---

## 4. Capabilities, not roles

Check what a user **can do**, not what they are called:

```php
// Correct.
if ( current_user_can( 'manage_options' ) ) { ... }
if ( current_user_can( 'edit_post', $post_id ) ) { ... }

// Incorrect.
if ( 'administrator' === $user->roles[0] ) { ... }
```

For REST routes, that's the `permission_callback` — never `__return_true` on a route that
reads private data or changes state.

---

## 5. Database access

1. Prefer the WordPress API (`WP_Query`, `get_posts()`, `get_option()`,
   `update_post_meta()`, `WP_Term_Query`) over raw SQL.
2. If raw SQL is unavoidable, always `$wpdb->prepare()`:

```php
$results = $wpdb->get_results(
	$wpdb->prepare(
		"SELECT ID, post_title FROM {$wpdb->posts}
		 WHERE post_status = %s AND post_type = %s
		 LIMIT %d",
		$status,
		$post_type,
		$limit
	)
);
```

- Placeholders are **not quoted**: `%s`, `%d`, `%f`, `%i` (identifier).
- Table/column names come from `$wpdb->posts` etc., or `%i` — never from user input.
- `LIKE` needs `$wpdb->esc_like()` before the placeholder.
- `IN ( ... )` needs a generated placeholder list:

```php
$placeholders = implode( ', ', array_fill( 0, count( $ids ), '%d' ) );
$sql          = "SELECT * FROM {$wpdb->posts} WHERE ID IN ( $placeholders )";
$rows         = $wpdb->get_results( $wpdb->prepare( $sql, ...$ids ) );
```

---

## 6. Other WordPress security musts

| Area | Rule |
| --- | --- |
| Redirects | `wp_safe_redirect()` (allowlisted hosts), then `exit;` |
| HTTP requests | `wp_remote_get()` / `wp_remote_post()`, never raw cURL/`file_get_contents()` |
| File uploads | `wp_handle_upload()`, `wp_check_filetype_and_ext()`, `sanitize_file_name()`; never trust the client MIME type |
| Direct file access | Top of every PHP file: `if ( ! defined( 'ABSPATH' ) ) { exit; }` |
| Assets | `wp_enqueue_script()` / `wp_enqueue_style()`; pass data with `wp_localize_script()` |
| Secrets | Environment variables / `wp-config.php`; never in the repo |
| Serialization | Never `unserialize()` untrusted data — use `json_decode()` |
| Error output | `WP_DEBUG_DISPLAY` off in production; log with `error_log()` |
| Internationalisation | `__()`, `_e()`, `_n()`, `_x()` with the project text domain, plus escaping variants |
