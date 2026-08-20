# 00 — Quick Reference & PR Checklist

One page to skim before writing code, and to run through before opening a PR.

## The five rules that matter most

1. **Never trust input. Never output unescaped.** Sanitize/validate on the way in,
   escape on the way out, as late as possible.
2. **Every state-changing request needs a nonce _and_ a capability check.**
   `check_admin_referer()` + `current_user_can()`.
3. **All SQL goes through `$wpdb->prepare()`** (or better: use the WP API instead of SQL).
4. **Strict comparisons only** (`===`, `!==`); Yoda conditions in WordPress code.
5. **Escape/sanitize functions must match the context** (`esc_html`, `esc_attr`,
   `esc_url`, `esc_textarea`, `wp_kses_post`).

## WordPress PHP formatting cheat sheet

```php
<?php
/**
 * Short summary of the file.
 *
 * @package Growmodo
 */

// Tabs for indentation. Spaces allowed mid-line for alignment.
$args = array(          // Long array syntax, one item per line, trailing comma.
	'post_type'   => 'page',
	'post_status' => 'publish',
);

if ( ! empty( $args ) && 'page' === $args['post_type'] ) {   // Yoda + spaces inside parens.
	do_something( $args['post_type'], (int) $count );        // Space after comma; lowercase cast.
} elseif ( $other ) {                                        // elseif, never "else if".
	do_other();
} else {
	do_default();
}

echo esc_html( $title );                    // Escape late, at output.
$x = $foo['bar'];                           // No spaces for literal keys...
$y = $foo[ $bar ];                          // ...spaces for variable keys.
```

| Thing | WordPress rule |
| --- | --- |
| Indentation | Real **tabs** |
| Quotes | Single quotes unless interpolating |
| Arrays | `array( … )` — **not** `[ … ]` |
| Functions / variables / hooks | `snake_case` lowercase |
| Classes / interfaces / traits / enums | `Capitalized_With_Underscores` (`WP_HTTP`, `Mailer_Interface`) |
| Constants | `UPPER_SNAKE_CASE` |
| Files | `my-plugin-name.php`, classes as `class-wp-error.php` |
| Comparison | Strict + Yoda: `if ( true === $flag )` |
| Ternary | Test for truth; **no** short ternary `?:` |
| Includes | `require_once` over `include_once` |
| Closing tag | Omit `?>` at end of PHP-only files |
| Banned | `extract()`, `eval()`, `create_function()`, `goto`, backticks, `@`, `var`, short PHP tags |

## PR checklist

### Security

- [ ] All `$_GET` / `$_POST` / `$_REQUEST` / `$_COOKIE` / `$_SERVER` reads are
      validated or sanitized (`sanitize_text_field()`, `absint()`, `sanitize_email()`, …).
- [ ] Every echo/print of dynamic data is escaped with the context-correct function.
- [ ] Forms use `wp_nonce_field()`; handlers use `check_admin_referer()` /
      `check_ajax_referer()` / `wp_verify_nonce()`.
- [ ] Authorization checked with `current_user_can()` — a nonce is **not** authorization.
- [ ] No string-interpolated SQL; `$wpdb->prepare()` with `%s`/`%d`/`%f`/`%i`, unquoted.
- [ ] No secrets, keys or credentials committed.
- [ ] File uploads validated (`wp_check_filetype_and_ext()`, `sanitize_file_name()`).
- [ ] Redirects use `wp_safe_redirect()`; remote calls use `wp_remote_*` (not raw cURL).

### Correctness & standards

- [ ] `phpcs` passes with the project ruleset (no new warnings).
- [ ] Strict comparisons; no assignments inside conditionals.
- [ ] Text is internationalized: `__()`, `esc_html__()`, `_n()` with the project text
      domain, and translator comments for `%s` placeholders.
- [ ] No closures passed to `add_action()` / `add_filter()` (they can't be removed).
- [ ] Errors handled explicitly (`WP_Error`, try/catch, `is_wp_error()`), logged not echoed.

### Documentation

- [ ] Every new function, method, class, constant and file has a DocBlock with
      `@since`, `@param`, `@return`.
- [ ] Every new `do_action()` / `apply_filters()` is documented immediately above it.

### Frontend

- [ ] Assets enqueued via `wp_enqueue_script()` / `wp_enqueue_style()` with versions —
      never hardcoded `<script>`/`<link>` tags.
- [ ] Markup validates; tags/attributes lowercase; attributes quoted.
- [ ] Keyboard-operable, visible focus, labelled controls, `alt` text — WCAG 2.2 AA.

### Performance

- [ ] No queries inside loops; no unbounded `posts_per_page => -1`.
- [ ] Expensive work cached (`wp_cache_*`, transients, or object cache).
- [ ] Only the assets a page needs are loaded.
