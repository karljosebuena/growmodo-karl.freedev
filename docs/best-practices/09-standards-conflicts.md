# 09 — Resolving Conflicts Between the Sources

The three source documents disagree in places. This page is the tie-breaker so we don't
argue about it mid-PR.

---

## 1. The core conflict: PSR-12 vs. WordPress Coding Standards

They are **mutually incompatible on formatting**. You cannot satisfy both in the same file.

| Topic | PSR-12 (rtCamp) | WordPress Coding Standards |
| --- | --- | --- |
| Indentation | 4 **spaces** | Real **tabs** |
| Parens spacing | `if ($a) {` | `if ( $a ) {` |
| Class brace | Own line | Own line (same) |
| Method brace | Own line | **Same line** |
| Function/variable naming | `camelCase` (common practice) | `snake_case` |
| Class naming | `StudlyCaps` | `Capitalized_With_Underscores` |
| Array syntax | `[ 1, 2, 3 ]` | `array( 1, 2, 3 )` |
| `declare(strict_types=1)` | Required | Not used in core |
| Yoda conditions | No | **Yes** |
| Short ternary `?:` | Allowed | **Forbidden** |
| Line length | Soft 120 | No hard limit (readability) |

### Decision for this project

**Default to the WordPress Coding Standards.** This is a WordPress project; core, every
plugin, WPCS, and any WordPress reviewer expect WPCS. Mixing styles per file is worse than
either style consistently.

Apply PSR-12 **only** to a self-contained, Composer-autoloaded library that:

- lives in its own directory (e.g. `lib/` or a separate package),
- has no WordPress function calls in it,
- is excluded from the WordPress ruleset in `phpcs.xml.dist` and given its own PSR-12
  ruleset,

and only if there's a concrete reason (e.g. we intend to publish it, or it's shared with a
non-WP codebase). Absent that reason, WPCS everywhere.

```xml
<!-- Example split, if we ever need it. -->
<rule ref="WordPress">
	<exclude-pattern>/lib/*</exclude-pattern>
</rule>
<rule ref="PSR12">
	<include-pattern>/lib/*</include-pattern>
</rule>
```

---

## 2. What we take from rtCamp regardless of style

The **architectural** advice is style-agnostic and we adopt all of it, written in WPCS
formatting:

- SOLID — especially Single Responsibility: hook callbacks stay thin, logic lives in
  testable classes.
- Dependency injection via constructors; type-hint interfaces.
- Namespaces (or a consistent `Growmodo_` prefix) for everything.
- Composer PSR-4 autoloading instead of manual `require` chains.
- Explicit error handling and logging; `WP_Error` at WordPress boundaries.
- Constants/enums instead of magic values — but config values come from the environment.
- No new globals.

```php
<?php
/**
 * Notifies users about project updates.
 *
 * @package Growmodo
 */

namespace Growmodo\Notifications;

use Growmodo\Contracts\Mailer_Interface;

/**
 * Sends project notifications through an injected mailer.
 */
class User_Notifier {

	/**
	 * Mailer implementation.
	 *
	 * @var Mailer_Interface
	 */
	private $mailer;

	/**
	 * Constructor.
	 *
	 * @param Mailer_Interface $mailer Mailer implementation.
	 */
	public function __construct( Mailer_Interface $mailer ) {
		$this->mailer = $mailer;
	}

	/**
	 * Notifies a user about a project update.
	 *
	 * @param int    $user_id User ID.
	 * @param string $message Message body.
	 * @return bool|WP_Error True on success, WP_Error on failure.
	 */
	public function notify( $user_id, $message ) {
		$user = get_userdata( $user_id );

		if ( ! $user ) {
			return new \WP_Error( 'invalid_user', __( 'Unknown user.', 'growmodo' ) );
		}

		return $this->mailer->send( $user->user_email, $message );
	}
}
```

That's SOLID + DI + namespaces + docblocks, in WordPress style. This is the target shape
for our application code.

---

## 3. Corrections applied to the source material

| Source claim | Correction |
| --- | --- |
| `FILTER_SANITIZE_STRING` (Medium, used 3×) | **Deprecated in PHP 8.1.** Use `FILTER_UNSAFE_RAW` + explicit escaping, or `sanitize_text_field()` in WordPress |
| CSRF token compared with `===` (Medium) | Use `hash_equals()` to avoid timing attacks; don't re-filter the stored token |
| `sort( $products, callback )` (Medium) | `sort()` takes no callback — use `usort()` |
| `array_map(function($v){ return $v * 2 })` (Medium) | Missing semicolon inside the closure in the original |
| DB credentials as class constants (rtCamp) | Never commit credentials — environment variables / `wp-config.php` outside VCS |
| `catch (Exception $e)` (rtCamp) | Catch `Throwable` to also cover PHP 7+ `Error`s |
| "PHP 7+ type hints" framing (both) | Fine, but gate any 8.x-only syntax on the project's minimum PHP version |
| WCAG "2.1 AA" (commonly cited) | WordPress now requires **WCAG 2.2 Level AA** |
| JSHint via Grunt (WP JS standards) | Superseded in practice by `@wordpress/eslint-plugin` / `@wordpress/scripts` |

---

## 4. Quick "which doc do I read?" map

| I'm writing… | Read |
| --- | --- |
| A theme template / `functions.php` / hook callback | 03, 04, 05, 06, 07 |
| A plugin service class | 02 (architecture only), 03 (style), 04, 05 |
| Anything that touches `$_POST` / `$_GET` / the DB | **04** first |
| Block editor JS / React | 06, 07, 08 |
| Stylesheets | 06, 07 |
| CI / lint setup | 08 |
| A standalone Composer library | 01, 02 |
