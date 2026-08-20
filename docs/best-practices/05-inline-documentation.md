# 05 — Inline Documentation Standards (PHPDoc)

Source: <https://developer.wordpress.org/coding-standards/inline-documentation-standards/php/>

---

## 1. What must be documented

- Functions and class methods
- Classes and class members (properties, constants)
- Required / included files
- **Hooks** — actions and filters
- Inline comments
- File headers
- Constants

---

## 2. DocBlock formatting rules

- The DocBlock **immediately precedes** the element it documents — no code in between.
- Use **spaces** (not tabs) inside DocBlocks for alignment.
- Wrap at **80 characters**; up to 120 if the DocBlock is indented 20+ positions.

### Summary

- A brief, **one-sentence** explanation, maximum two lines.
- No HTML or Markdown formatting.
- Third-person singular verbs — mentally prefix "It": *"It displays…"*, not *"It display…"*.
- Ends with a period.

### Description

- Complete sentences with periods.
- Limited Markdown allowed — backticks for variables/code.
- Hyphens for unordered lists, numbers for ordered lists, blank line before and after.
- Code samples indented 4 spaces with blank lines around them.

---

## 3. Tag order

For functions and methods:

1. Summary line
2. Description (if needed)
3. `@ignore` (if applicable)
4. `@since`
5. `@access` (only if private)
6. `@see`, `@link`
7. `@global`
8. `@param`
9. `@return`

---

## 4. Supported tags

| Tag | Usage |
| --- | --- |
| `@since` | Always **3-digit** version (`3.9.0`, not `3.9`). Exception: `@since MU (3.0.0)` |
| `@param` | Type, variable name, description; note optional parameters |
| `@return` | Type and description. Avoid `@return void` outside bundled themes |
| `@global` | PHP globals used: type, variable, optional description |
| `@deprecated` | Version + replacement, accompanied by a matching `@see` |
| `@access` | Only for core-only functions or classes implementing "private" core APIs |
| `@see` | Reference a related function/method/class, or an inline hook |
| `@link` | URL for further information — not for linking to other functions |
| `@type` | Types of values inside an array parameter (used with `@param` hash notation) |
| `@var` | Data type of a class property or constant |
| `@ignore` | Exclude the element from parsing entirely |
| `@todo` | Planned, not-yet-implemented changes |
| `@internal` | Notes for internal use only |
| `@method` | Document "magic" methods on a class |
| `@package` | Package name (always `WordPress` in core; use the project name here) |
| `@subpackage` | Component / subpackage |

---

## 5. Examples

### Function

```php
/**
 * Retrieves the featured projects for a client.
 *
 * Results are cached for one hour in the object cache. Pass `$force` to bypass
 * the cache — for example after a project is published.
 *
 * @since 1.0.0
 * @since 1.2.0 Added the `$force` parameter.
 *
 * @see growmodo_get_project_ids()
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param int   $client_id Client user ID.
 * @param array $args {
 *     Optional. Query arguments. Default empty array.
 *
 *     @type int    $limit  Maximum number of projects to return. Default 10.
 *     @type string $status Project status to filter by. Default 'publish'.
 * }
 * @param bool  $force     Optional. Whether to bypass the cache. Default false.
 * @return WP_Post[]|WP_Error Array of project post objects, or WP_Error on failure.
 */
function growmodo_get_featured_projects( $client_id, $args = array(), $force = false ) {
	// ...
}
```

### Class and members

```php
/**
 * Handles project synchronisation with the remote API.
 *
 * @since 1.0.0
 */
class Growmodo_Project_Sync {

	/**
	 * Cache group used for all sync data.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private $cache_group = 'growmodo_sync';

	/**
	 * Registers the hooks used by this class.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function register() {
		// ...
	}
}
```

### File header

Summary has **no** period; include `@package`, `@subpackage`, `@link`, `@since` as
relevant.

```php
<?php
/**
 * Project synchronisation
 *
 * Wires the sync scheduler to WordPress cron and exposes the manual sync action.
 *
 * @package    Growmodo
 * @subpackage Sync
 * @since      1.0.0
 */
```

### Constant

Summary, `@since`, `@var`.

```php
/**
 * Maximum number of projects synced per batch.
 *
 * @since 1.0.0
 * @var int
 */
define( 'GROWMODO_SYNC_BATCH_SIZE', 50 );
```

---

## 6. Documenting hooks

Document immediately before `do_action()`, `apply_filters()` or their `*_ref_array()`
variants:

- Summary describing the purpose, or what is being filtered.
- Description if needed.
- `@since`.
- `@param` for each argument (hash notation for array arguments).
- **No `@return`** — actions return nothing, filters return the first parameter.

```php
/**
 * Filters the list of featured projects before display.
 *
 * @since 1.0.0
 *
 * @param WP_Post[] $projects  Array of project post objects.
 * @param int       $client_id Client user ID.
 */
$projects = apply_filters( 'growmodo_featured_projects', $projects, $client_id );

/**
 * Fires after a project batch has been synced.
 *
 * @since 1.0.0
 *
 * @param int   $batch_size Number of projects in the batch.
 * @param array $results    Sync results keyed by project ID.
 */
do_action( 'growmodo_after_project_sync', $batch_size, $results );
```

### Duplicate hooks

Document the first-added (or logically primary) occurrence fully. Subsequent occurrences
of the same hook get a one-line reference:

```php
/** This action is documented in includes/class-growmodo-project-sync.php */
do_action( 'growmodo_after_project_sync', $batch_size, $results );
```

---

## 7. Versioning rules

`@since` uses three digits (`4.4.0`, not `4.4`). For significant changes, add a changelog
line per version:

```
@since 3.0.0
@since 3.8.0 Added the `post__in` argument.
@since 4.1.0 The `$force` parameter is now optional.
```

Significant changes = new arguments/parameters, a required parameter becoming optional,
behavioural changes, and parameter renames.

---

## 8. Special cases

- **Deprecated functions:** include the version, the replacement function name, and a
  matching `@see`.
- **Array parameters:** document with `@type` hash notation in the *originating* function
  only; other functions cross-reference it via `@see`.
