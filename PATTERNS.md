# Code Patterns Doctrine

Concrete implementation patterns for the Growmodo PHP / WordPress project. Architecture,
layering, naming, and code examples. Style rules live in
[`docs/best-practices/`](./docs/best-practices/) — this file is *how we assemble things*.

---

## Assumptions (correct these, don't work around them)

The project brief is not yet in the repo (see `CLAUDE.md` § Project Context). Everything
below assumes:

| Assumption | Value | Change it when |
| --- | --- | --- |
| Deliverable | A WordPress **plugin**, optionally with a small theme/child-theme | The brief says theme-only, or plain PHP |
| Prefix | `growmodo_` (functions/hooks), `Growmodo\` (namespace), `GROWMODO_` (constants) | The brief names something else |
| Text domain | `growmodo` | Ditto |
| Minimum PHP | **7.4** — no enums, no `readonly`, no union types in shipped code | The brief states otherwise |
| Minimum WP | **6.4** | Ditto |
| Style | WordPress Coding Standards everywhere (`phpcs.xml.dist`) | Never — this is not negotiable inside WP |

**If the brief turns out to be plain PHP with no WordPress**, then §§ Security, Templating,
Hooks and Options below still apply in spirit but the implementations change: read
[`01-php-general.md`](./docs/best-practices/01-php-general.md) and
[`02-php-architecture.md`](./docs/best-practices/02-php-architecture.md), use PSR-12 + PSR-4,
and say so explicitly before writing code.

---

## Clean Git Diffs

- **Preserve existing structure** — don't reorder functions, hooks, or array keys unless necessary
- **Minimal changes** — only modify what's needed; never reformat unchanged code
- **Match existing style** — the surrounding file's formatting wins over personal preference
- **No drive-by `phpcs` fixes** in a feature PR — a standards sweep is its own commit

**For significant rewrites:** delete the entire function/section, then add the new one.
Prefer clean "all red then all green" diffs over line-by-line transformations.

---

## Request Flow Architecture

```
                             ┌─────────────────────────────────────────┐
HTTP request                 │  WordPress bootstrap → hooks fire       │
     │                       └─────────────────────────────────────────┘
     ▼
┌──────────────────┐   thin, no logic
│  Hook callback   │   admin_post_*, wp_ajax_*, REST route, shortcode,
│  (Controller)    │   template, cron, block render_callback
└────────┬─────────┘
         │  1. nonce + capability      ← Security Mandate
         │  2. sanitize/validate input
         ▼
┌──────────────────┐
│    Service       │   business logic, orchestration, returns data or WP_Error
└────────┬─────────┘
         ▼
┌──────────────────┐
│   Repository     │   the ONLY place that touches $wpdb / WP_Query / options / meta
└────────┬─────────┘
         ▼
   WP APIs / $wpdb

         ▲
         │  returns plain data (arrays / value objects / WP_Post[])
┌────────┴─────────┐
│  View / Template │   escapes at output. No queries, no business logic.
└──────────────────┘
```

### Layers

| Layer | Location | Responsibility |
| --- | --- | --- |
| Bootstrap | `growmodo.php`, `includes/class-plugin.php` | Define constants, autoload, instantiate, register hooks. Nothing else |
| Controller | `includes/controllers/class-{domain}-controller.php` | Hook entry point. Verify nonce + capability, sanitize input, delegate, render/respond |
| REST Controller | `includes/rest/class-{domain}-rest-controller.php` | `register_rest_route`, `permission_callback`, `args` schema, delegate |
| Service | `includes/services/class-{domain}-service.php` | Business logic, validation, orchestration. Returns data or `WP_Error`. Never echoes |
| Repository | `includes/repositories/class-{domain}-repository.php` | Data access: `WP_Query`, `$wpdb->prepare()`, options, meta, transients |
| Value object | `includes/models/class-{domain}.php` | Typed shape of a domain record. No persistence |
| View / Template | `templates/{context}/{name}.php`, `templates/partials/*.php` | Markup + escaping only |
| Assets | `assets/js/`, `assets/css/`, `build/` | Enqueued, never inlined into markup |

**Dependencies flow DOWN. Never import a higher layer from a lower one.**
A repository never calls a service. A service never echoes or reads `$_POST`. A template
never queries.

### Data Access Doctrine (STRICT)

**Only repositories touch data. Everything else reaches data through a service.**

- **`$wpdb` is repository-only.** No controller, service, template, cron callback or CLI
  command holds `$wpdb`. If a service needs a query that doesn't exist, add a repository
  method — never reach past the repository.
- **`WP_Query` / `get_posts()` / `get_option()` / `*_post_meta()` are repository-only** too.
  They are data access, and treating them as "just core functions" is how query logic ends
  up scattered across templates.
- **Entry surfaces call services** — controllers, REST controllers, shortcodes, blocks and
  cron handlers depend on services, never on repositories.
- **Cross-domain calls go service-to-service** — a service owns only its own domain's
  repository. To touch another domain, use that domain's *service* so its rules apply.

**Exception — templates may use loop functions.** Inside `while ( have_posts() )`, template
tags (`the_title()`, `get_the_permalink()`, `the_post_thumbnail()`) are the idiomatic API and
are allowed. Building a *new* query in a template is not.

---

## Directory Layout

```
growmodo/
├── growmodo.php                       # Plugin header + bootstrap ONLY
├── uninstall.php                      # Cleanup on delete
├── composer.json
├── package.json
├── phpcs.xml.dist
├── .editorconfig
├── CLAUDE.md
├── PATTERNS.md
├── RESOLVED_GOTCHAS.md
├── docs/
│   ├── best-practices/                # Compiled standards (read-only reference)
│   └── project-brief.md               # TODO — paste the assessment brief here
├── includes/
│   ├── class-plugin.php               # Wiring: instantiate + register hooks
│   ├── class-activator.php            # Activation / deactivation / schema
│   ├── controllers/
│   ├── rest/
│   ├── services/
│   ├── repositories/
│   ├── models/
│   ├── admin/                         # Settings pages, meta boxes, list tables
│   └── helpers/                       # Pure functions only. No state
├── templates/
│   ├── admin/
│   ├── public/
│   └── partials/
├── assets/
│   ├── css/
│   ├── js/
│   └── images/
├── languages/
│   └── growmodo.pot
└── tests/
    ├── bootstrap.php
    ├── unit/
    └── integration/
```

- **One class per file**, `class-` prefix, underscores → hyphens:
  `Growmodo\Services\Project_Service` → `includes/services/class-project-service.php`.
- `includes/helpers/` holds **pure functions only** — same input, same output, no DB, no globals.

---

## Naming Conventions

| Element | Convention | Example |
| --- | --- | --- |
| Functions, variables, hooks | `snake_case` | `growmodo_get_projects()`, `$project_id` |
| Classes, interfaces, traits | `Capitalized_With_Underscores` | `Project_Service`, `Mailer_Interface` |
| Constants | `UPPER_SNAKE_CASE`, prefixed | `GROWMODO_VERSION`, `GROWMODO_PATH` |
| Files | lowercase, hyphenated | `class-project-service.php`, `project-card.php` |
| Namespaces | `Growmodo\Sub_Domain` | `Growmodo\Repositories` |
| Hooks | `{prefix}_{noun}_{verb}` | `growmodo_project_saved`, `growmodo_projects_query_args` |
| Options | `{prefix}_{name}`, singular array where possible | `growmodo_settings` |
| Post meta | `_growmodo_{name}` (leading `_` = hidden from custom fields UI) | `_growmodo_client_id` |
| CPT / taxonomy slugs | ≤ 20 chars, prefixed, singular | `growmodo_project` |
| Transients | `{prefix}_{name}_{hash}` | `growmodo_projects_a1b2c3` |
| Nonce actions | `{prefix}_{verb}_{object}_{id}` | `growmodo_save_project_42` |
| CSS classes | lowercase, hyphenated, prefixed | `.growmodo-project-card` |
| JS variables/functions | `camelCase` (JS standard differs from PHP — intentional) | `projectId`, `fetchProjects()` |

**Everything global gets the prefix.** Functions, classes (or a namespace), constants, hooks,
options, meta keys, CSS classes, JS globals, script/style handles. `wp` and `WordPress` are
reserved prefixes.

### Variables

```php
// GOOD — explicit.
$active_project_id = (int) $request['project_id'];
$project_query_args = array( 'post_type' => 'growmodo_project', 'post_status' => 'publish' );

// BAD — ambiguous.
$id = (int) $request['project_id'];
$a  = array( 'post_type' => 'growmodo_project' );
```

---

## Bootstrap Pattern

`growmodo.php` does four things and stops: header, guard, constants, hand off.

```php
<?php
/**
 * Plugin Name:       Growmodo
 * Description:       <one line from the brief>.
 * Version:           1.0.0
 * Requires at least: 6.4
 * Requires PHP:      7.4
 * Author:            Karl Jose Buena
 * License:           GPL-2.0-or-later
 * Text Domain:       growmodo
 * Domain Path:       /languages
 *
 * @package Growmodo
 */

namespace Growmodo;

// Never allow direct file access. First line of every PHP file in the project.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'GROWMODO_VERSION', '1.0.0' );
define( 'GROWMODO_FILE', __FILE__ );
define( 'GROWMODO_PATH', plugin_dir_path( __FILE__ ) );
define( 'GROWMODO_URL', plugin_dir_url( __FILE__ ) );

require_once GROWMODO_PATH . 'vendor/autoload.php';

register_activation_hook( __FILE__, array( Activator::class, 'activate' ) );
register_deactivation_hook( __FILE__, array( Activator::class, 'deactivate' ) );

add_action(
	'plugins_loaded',
	function () {
		( new Plugin() )->register();
	}
);
```

> The closure here is deliberate and safe — it's a one-time bootstrap on `plugins_loaded`
> that nothing needs to `remove_action()`. **Everywhere else, closures on hooks are banned**
> (see § Anti-Patterns).

### The Plugin class — wiring only

```php
<?php
/**
 * Plugin wiring.
 *
 * @package Growmodo
 */

namespace Growmodo;

use Growmodo\Controllers\Project_Controller;
use Growmodo\Repositories\Project_Repository;
use Growmodo\Services\Project_Service;

/**
 * Composes the object graph and registers hooks.
 *
 * @since 1.0.0
 */
class Plugin {

	/**
	 * Registers every hook the plugin uses.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function register() {
		$repository = new Project_Repository();
		$service    = new Project_Service( $repository );

		( new Project_Controller( $service ) )->register();
		( new Admin\Settings_Page() )->register();

		add_action( 'init', array( $this, 'load_textdomain' ) );
	}

	/**
	 * Loads the plugin translations.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'growmodo', false, dirname( plugin_basename( GROWMODO_FILE ) ) . '/languages' );
	}
}
```

**Rules**

- Composition happens **here**, in one place. No `new Project_Repository()` inside a service.
- No business logic in `Plugin`. If a method does anything but wire, it belongs in a service.
- If the graph outgrows manual composition, add a tiny container — not a framework.

---

## Hook Registration Pattern

Each class registers its own hooks in a `register()` method. Callbacks are **named methods**,
never closures, so they remain removable.

```php
/**
 * Registers the hooks owned by this controller.
 *
 * @since 1.0.0
 * @return void
 */
public function register() {
	add_action( 'admin_post_growmodo_save_project', array( $this, 'handle_save' ) );
	add_action( 'wp_ajax_growmodo_search_projects', array( $this, 'handle_search' ) );
	add_filter( 'the_content', array( $this, 'append_project_meta' ), 20 );
	add_shortcode( 'growmodo_projects', array( $this, 'render_projects' ) );
}
```

- **Priority is explicit** when it matters, with a comment saying why.
- **Never** `add_action( 'x', function () { ... } )` outside bootstrap.
- One hook → one thin callback → one service call.
- Firing our own extension points: document them, then
  `do_action( 'growmodo_project_saved', $project_id, $project )` — see
  [`05-inline-documentation.md`](./docs/best-practices/05-inline-documentation.md) § hooks.

---

## Controller Pattern (form / admin-post)

Controllers are the security boundary. The order is fixed: **verify → sanitize → delegate →
redirect/render**.

```php
/**
 * Handles the project save submission.
 *
 * @since 1.0.0
 * @return void
 */
public function handle_save() {
	// 1. Authenticity + intent.
	check_admin_referer( 'growmodo_save_project' );

	// 2. Authorization — a nonce is not authorization.
	if ( ! current_user_can( 'edit_posts' ) ) {
		wp_die(
			esc_html__( 'You are not allowed to edit projects.', 'growmodo' ),
			'',
			array( 'response' => 403 )
		);
	}

	// 3. Sanitize every input. wp_unslash first — WP slashes superglobals.
	$input = array(
		'id'      => isset( $_POST['project_id'] ) ? absint( $_POST['project_id'] ) : 0,
		'title'   => isset( $_POST['title'] ) ? sanitize_text_field( wp_unslash( $_POST['title'] ) ) : '',
		'email'   => isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '',
		'status'  => isset( $_POST['status'] ) ? sanitize_key( $_POST['status'] ) : 'draft',
		'summary' => isset( $_POST['summary'] ) ? wp_kses_post( wp_unslash( $_POST['summary'] ) ) : '',
	);

	// 4. Delegate. All decisions live in the service.
	$result = $this->service->save( $input );

	// 5. Respond.
	if ( is_wp_error( $result ) ) {
		$this->redirect_with_notice( 'error', $result->get_error_code() );
		return;
	}

	$this->redirect_with_notice( 'success', 'saved' );
}

/**
 * Redirects back to the project screen with a notice flag.
 *
 * @since 1.0.0
 *
 * @param string $type   Notice type: 'success' or 'error'.
 * @param string $code   Machine-readable notice code.
 * @return void
 */
private function redirect_with_notice( $type, $code ) {
	wp_safe_redirect(
		add_query_arg(
			array(
				'page'             => 'growmodo-projects',
				'growmodo_notice'  => $type,
				'growmodo_message' => $code,
			),
			admin_url( 'admin.php' )
		)
	);
	exit;
}
```

**Controller rules**

- **Nonce + capability first, always** — before reading a single input.
- `wp_unslash()` before sanitizing; sanitizer matched to the field's meaning.
- **No business rules here.** "Is this status allowed?" is a service question.
- Always `exit;` after a redirect.
- Never echo a raw error message from an exception or `$wpdb->last_error`.

---

## REST Controller Pattern

```php
/**
 * Registers the REST routes.
 *
 * @since 1.0.0
 * @return void
 */
public function register() {
	add_action( 'rest_api_init', array( $this, 'register_routes' ) );
}

/**
 * Registers the projects collection route.
 *
 * @since 1.0.0
 * @return void
 */
public function register_routes() {
	register_rest_route(
		'growmodo/v1',
		'/projects',
		array(
			array(
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_items' ),
				'permission_callback' => array( $this, 'can_read' ),
				'args'                => array(
					'status'   => array(
						'type'              => 'string',
						'default'           => 'publish',
						'enum'              => array( 'publish', 'draft', 'pending' ),
						'sanitize_callback' => 'sanitize_key',
					),
					'per_page' => array(
						'type'              => 'integer',
						'default'           => 10,
						'minimum'           => 1,
						'maximum'           => 100,
						'sanitize_callback' => 'absint',
					),
				),
			),
		)
	);
}

/**
 * Checks whether the current user may read projects.
 *
 * @since 1.0.0
 * @return bool|\WP_Error
 */
public function can_read() {
	if ( ! current_user_can( 'read' ) ) {
		return new \WP_Error(
			'growmodo_forbidden',
			__( 'You cannot view projects.', 'growmodo' ),
			array( 'status' => 403 )
		);
	}

	return true;
}

/**
 * Returns a collection of projects.
 *
 * @since 1.0.0
 *
 * @param \WP_REST_Request $request Request object.
 * @return \WP_REST_Response|\WP_Error
 */
public function get_items( $request ) {
	$projects = $this->service->get_projects(
		array(
			'status'   => $request->get_param( 'status' ),
			'per_page' => $request->get_param( 'per_page' ),
		)
	);

	if ( is_wp_error( $projects ) ) {
		return $projects;
	}

	return rest_ensure_response( array_map( array( $this, 'prepare_item' ), $projects ) );
}
```

**REST rules**

- **Every route has a real `permission_callback`.** `__return_true` only on a route that
  exposes nothing private and changes nothing — and say why in a comment.
- Declare `args` with `type`, `enum`/`minimum`, `sanitize_callback`, `validate_callback`.
  Schema validation is free sanitization; use it instead of hand-rolling checks.
- Write routes still need nonce protection for cookie-authenticated callers — the
  `X-WP-Nonce` header (`wp_create_nonce( 'wp_rest' )`) is what `wp.apiFetch` sends.
- Return `WP_Error` with a `status`, never `wp_die()`.
- Namespace is versioned: `growmodo/v1`.

## AJAX Pattern

```php
/**
 * Returns matching projects for the admin search box.
 *
 * @since 1.0.0
 * @return void
 */
public function handle_search() {
	check_ajax_referer( 'growmodo_search_projects', 'nonce' );

	if ( ! current_user_can( 'edit_posts' ) ) {
		wp_send_json_error( array( 'message' => __( 'Not allowed.', 'growmodo' ) ), 403 );
	}

	$term    = isset( $_POST['term'] ) ? sanitize_text_field( wp_unslash( $_POST['term'] ) ) : '';
	$results = $this->service->search( $term );

	wp_send_json_success( $results );
}
```

- Register **both** `wp_ajax_{action}` and `wp_ajax_nopriv_{action}` — and only add
  `nopriv` when unauthenticated access is genuinely intended.
- Always terminate with `wp_send_json_success()` / `wp_send_json_error()`.
- Prefer REST over `admin-ajax.php` for anything new; AJAX is for legacy surfaces.

---

## Service Pattern

Services hold the rules. They receive already-sanitized primitives, return data or
`WP_Error`, and never touch superglobals or output.

```php
<?php
/**
 * Project business logic.
 *
 * @package Growmodo
 */

namespace Growmodo\Services;

use Growmodo\Repositories\Project_Repository;
use WP_Error;

/**
 * Applies project rules and orchestrates persistence.
 *
 * @since 1.0.0
 */
class Project_Service {

	/**
	 * Allowed project statuses.
	 *
	 * @since 1.0.0
	 * @var string[]
	 */
	const ALLOWED_STATUSES = array( 'draft', 'pending', 'publish' );

	/**
	 * Project repository.
	 *
	 * @since 1.0.0
	 * @var Project_Repository
	 */
	private $repository;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param Project_Repository $repository Project repository.
	 */
	public function __construct( Project_Repository $repository ) {
		$this->repository = $repository;
	}

	/**
	 * Saves a project after validating it.
	 *
	 * @since 1.0.0
	 *
	 * @param array $input {
	 *     Sanitized project input.
	 *
	 *     @type int    $id      Project ID. 0 to create.
	 *     @type string $title   Project title.
	 *     @type string $email   Contact email.
	 *     @type string $status  Project status.
	 *     @type string $summary Project summary, post-content HTML allowed.
	 * }
	 * @return int|WP_Error Project ID on success, WP_Error on failure.
	 */
	public function save( array $input ) {
		if ( '' === $input['title'] ) {
			return new WP_Error( 'growmodo_missing_title', __( 'A project title is required.', 'growmodo' ) );
		}

		if ( '' !== $input['email'] && ! is_email( $input['email'] ) ) {
			return new WP_Error( 'growmodo_invalid_email', __( 'Enter a valid contact email.', 'growmodo' ) );
		}

		if ( ! in_array( $input['status'], self::ALLOWED_STATUSES, true ) ) {
			return new WP_Error( 'growmodo_invalid_status', __( 'Unknown project status.', 'growmodo' ) );
		}

		$project_id = $this->repository->save( $input );

		if ( is_wp_error( $project_id ) ) {
			return $project_id;
		}

		/**
		 * Fires after a project has been saved.
		 *
		 * @since 1.0.0
		 *
		 * @param int   $project_id Saved project ID.
		 * @param array $input      Sanitized input used for the save.
		 */
		do_action( 'growmodo_project_saved', $project_id, $input );

		return $project_id;
	}
}
```

**Service rules**

- **Dependencies injected via constructor**, type-hinted (interface where a swap is plausible).
- **Validate before persisting**; return `WP_Error` with a prefixed, machine-readable code and
  a translated message.
- **Never** read `$_POST`, never `echo`, never call `wp_die()` / `wp_redirect()`.
- **Never** call `esc_*()` — escaping happens at output, not in the middle of the stack.
- Cross-domain work goes through the other domain's **service**.
- Fire a documented action after a state change so the plugin is extensible.
- Public methods read like domain operations (`save`, `archive`, `assign_to_client`); private
  methods carry the mechanics.

---

## Repository Pattern

The only layer that talks to data. No validation, no `WP_Error` for business reasons, no output.

```php
/**
 * Returns published projects for a client.
 *
 * @since 1.0.0
 *
 * @param int $client_id Client user ID.
 * @param int $limit     Maximum number of projects.
 * @return \WP_Post[] Project posts.
 */
public function find_by_client( $client_id, $limit = 10 ) {
	$query = new \WP_Query(
		array(
			'post_type'              => 'growmodo_project',
			'post_status'            => 'publish',
			'posts_per_page'         => $limit,
			'author'                 => $client_id,
			'orderby'                => 'date',
			'order'                  => 'DESC',
			'no_found_rows'          => true,   // Skip the COUNT query when not paginating.
			'update_post_meta_cache' => false,  // Skip when meta is not read.
			'update_post_term_cache' => false,  // Skip when terms are not read.
		)
	);

	return $query->posts;
}
```

### Raw SQL — only when the API genuinely cannot express it

```php
/**
 * Counts projects grouped by status.
 *
 * @since 1.0.0
 *
 * @global \wpdb $wpdb WordPress database abstraction object.
 *
 * @return array<string,int> Status slug => count.
 */
public function count_by_status() {
	global $wpdb;

	$cache_key = 'growmodo_project_status_counts';
	$counts    = wp_cache_get( $cache_key, 'growmodo' );

	if ( false !== $counts ) {
		return $counts;
	}

	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery -- Aggregate not expressible via WP_Query; cached below.
	$rows = $wpdb->get_results(
		$wpdb->prepare(
			"SELECT post_status, COUNT( * ) AS total
			 FROM {$wpdb->posts}
			 WHERE post_type = %s
			 GROUP BY post_status",
			'growmodo_project'
		)
	);

	$counts = array();

	foreach ( $rows as $row ) {
		$counts[ $row->post_status ] = (int) $row->total;
	}

	wp_cache_set( $cache_key, $counts, 'growmodo', 5 * MINUTE_IN_SECONDS );

	return $counts;
}
```

**Repository rules**

- `$wpdb->prepare()` on **every** query with a variable. Placeholders unquoted: `%s`, `%d`,
  `%f`, `%i`. Table names from `$wpdb->posts` / `$wpdb->prefix`, never from input.
- `IN ( ... )`: build placeholders — `implode( ', ', array_fill( 0, count( $ids ), '%d' ) )`.
- `LIKE`: `$wpdb->esc_like( $term )` **before** the placeholder.
- Every direct query is **cached** and the `phpcs:ignore` carries a reason.
- **Invalidate on write** — a `wp_cache_set()` without a matching delete on the write path is
  a bug, not an optimization.
- `no_found_rows` / `update_post_meta_cache` / `update_post_term_cache` set deliberately.
- **Never `posts_per_page => -1`** on data that can grow. Page it.
- Repositories return `WP_Post[]`, arrays, ints, or `null` — not rendered HTML.

### Options & transients

```php
/**
 * Returns the plugin settings, merged over defaults.
 *
 * @since 1.0.0
 * @return array Settings.
 */
public function get_settings() {
	$defaults = array(
		'api_key'    => '',
		'sync_limit' => 50,
	);

	return wp_parse_args( (array) get_option( 'growmodo_settings', array() ), $defaults );
}
```

- **One option holding an array** beats fifteen options — fewer autoloaded rows.
- Large or rarely-read options: `add_option( $name, $value, '', 'no' )` to skip autoload.
- Transients for expensive external calls; **always** treat a transient as possibly absent.

---

## Template & Output Pattern

Templates receive data and render it. They do not query, decide, or mutate.

```php
<?php
/**
 * Project card partial.
 *
 * @package Growmodo
 *
 * @var array $project Project data: title, permalink, summary, client_name, thumbnail_id.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<article class="growmodo-project-card">
	<h3 class="growmodo-project-card__title">
		<a href="<?php echo esc_url( $project['permalink'] ); ?>">
			<?php echo esc_html( $project['title'] ); ?>
		</a>
	</h3>

	<?php if ( ! empty( $project['thumbnail_id'] ) ) : ?>
		<?php echo wp_get_attachment_image( (int) $project['thumbnail_id'], 'medium', false, array( 'class' => 'growmodo-project-card__image' ) ); ?>
	<?php endif; ?>

	<div class="growmodo-project-card__summary">
		<?php echo wp_kses_post( $project['summary'] ); ?>
	</div>

	<p class="growmodo-project-card__meta">
		<?php
		printf(
			/* translators: %s: Client display name. */
			esc_html__( 'Client: %s', 'growmodo' ),
			esc_html( $project['client_name'] )
		);
		?>
	</p>
</article>
```

### Rendering from a controller

```php
/**
 * Renders a template with a scoped data array.
 *
 * @since 1.0.0
 *
 * @param string $relative_path Path under templates/, without extension.
 * @param array  $data          Variables exposed to the template.
 * @return string Rendered markup.
 */
private function render( $relative_path, array $data = array() ) {
	$file = GROWMODO_PATH . 'templates/' . $relative_path . '.php';

	if ( ! file_exists( $file ) ) {
		return '';
	}

	// Explicit extraction of known keys — never extract().
	$project = isset( $data['project'] ) ? $data['project'] : array();

	ob_start();
	require $file;

	return (string) ob_get_clean();
}
```

**Output rules**

- **Escape at output, matched to context:** `esc_html()` in text, `esc_attr()` in attributes,
  `esc_url()` in `href`/`src`, `esc_textarea()` in `<textarea>`, `wp_kses_post()` for rich text.
- Translations use the escaping variants directly: `esc_html__()`, `esc_html_e()`,
  `esc_attr__()` — don't nest `esc_html( __( ... ) )`.
- `printf()` with a `/* translators: */` comment for every placeholder.
- **Never `extract()`.** Assign the variables you need, explicitly.
- Shortcodes and `render_callback`s **return** a string; they never echo.
- Tabs for indentation, logical structure, lowercase tags, quoted attributes
  ([`06-html-css-js.md`](./docs/best-practices/06-html-css-js.md)).
- Accessibility is part of the markup, not a later pass
  ([`07-accessibility.md`](./docs/best-practices/07-accessibility.md)).

---

## Admin Settings Pattern

```php
/**
 * Registers the settings, section and fields.
 *
 * @since 1.0.0
 * @return void
 */
public function register_settings() {
	register_setting(
		'growmodo_settings_group',
		'growmodo_settings',
		array(
			'type'              => 'array',
			'sanitize_callback' => array( $this, 'sanitize_settings' ),
			'default'           => array(),
		)
	);

	add_settings_section(
		'growmodo_general',
		__( 'General', 'growmodo' ),
		array( $this, 'render_section_intro' ),
		'growmodo_settings_page'
	);

	add_settings_field(
		'growmodo_sync_limit',
		__( 'Sync limit', 'growmodo' ),
		array( $this, 'render_sync_limit_field' ),
		'growmodo_settings_page',
		'growmodo_general',
		array( 'label_for' => 'growmodo_sync_limit' )
	);
}

/**
 * Sanitizes the settings array before it is stored.
 *
 * @since 1.0.0
 *
 * @param mixed $input Raw submitted settings.
 * @return array Sanitized settings.
 */
public function sanitize_settings( $input ) {
	$input = is_array( $input ) ? $input : array();

	return array(
		'api_key'    => isset( $input['api_key'] ) ? sanitize_text_field( $input['api_key'] ) : '',
		'sync_limit' => isset( $input['sync_limit'] ) ? min( 500, max( 1, absint( $input['sync_limit'] ) ) ) : 50,
	);
}
```

- `register_setting()` with a **`sanitize_callback`** — the Settings API handles the nonce and
  capability for you, but not the sanitizing.
- `add_menu_page()` / `add_submenu_page()` with a capability, and check it again in the render
  callback.
- `label_for` on every field so the `<label>` is wired up (accessibility).
- Custom (non-Settings-API) admin forms are ordinary controllers: nonce + capability yourself.

---

## Custom Post Type & Taxonomy Pattern

```php
/**
 * Registers the project post type.
 *
 * @since 1.0.0
 * @return void
 */
public function register_post_type() {
	register_post_type(
		'growmodo_project',
		array(
			'labels'       => array(
				'name'          => __( 'Projects', 'growmodo' ),
				'singular_name' => __( 'Project', 'growmodo' ),
			),
			'public'       => true,
			'show_in_rest' => true,                                  // Required for the block editor.
			'supports'     => array( 'title', 'editor', 'thumbnail', 'author', 'custom-fields' ),
			'has_archive'  => true,
			'rewrite'      => array( 'slug' => 'projects' ),
			'menu_icon'    => 'dashicons-portfolio',
			'capability_type' => 'post',
		)
	);
}
```

- Registered on `init`, **never** conditionally on `is_admin()`.
- Slug ≤ 20 characters, prefixed.
- `show_in_rest => true` if the block editor or our REST routes touch it.
- Register meta with `register_post_meta()` — with `sanitize_callback`, `auth_callback`, and
  `show_in_rest` where it's exposed.
- Flush rewrite rules **only on activation**, never on every load.

---

## Asset Pattern

```php
/**
 * Enqueues the public assets.
 *
 * @since 1.0.0
 * @return void
 */
public function enqueue_public_assets() {
	// Only load where it's needed.
	if ( ! is_singular( 'growmodo_project' ) ) {
		return;
	}

	wp_enqueue_style(
		'growmodo-public',
		GROWMODO_URL . 'assets/css/public.css',
		array(),
		GROWMODO_VERSION
	);

	wp_enqueue_script(
		'growmodo-public',
		GROWMODO_URL . 'assets/js/public.js',
		array( 'wp-a11y' ),
		GROWMODO_VERSION,
		true
	);

	wp_localize_script(
		'growmodo-public',
		'growmodoData',
		array(
			'restUrl' => esc_url_raw( rest_url( 'growmodo/v1/projects' ) ),
			'nonce'   => wp_create_nonce( 'wp_rest' ),
			'i18n'    => array(
				'loading' => __( 'Loading projects…', 'growmodo' ),
			),
		)
	);
}
```

- **Always** `wp_enqueue_*` — never a hardcoded `<script>` / `<link>` in a template.
- Handles prefixed; version = `GROWMODO_VERSION` (or `filemtime()` in dev) for cache busting.
- Scripts in the footer (`true`) unless there's a documented reason.
- Data to JS via `wp_localize_script()` / `wp_add_inline_script()` / `wp_json_encode()` —
  never string-concatenated into markup.
- Conditional loading: a bail-out check at the top of the callback.
- JS follows the JS standards (camelCase, `===`, single quotes) — deliberately different from
  PHP naming.

---

## Error Handling & Logging

| Situation | Mechanism |
| --- | --- |
| Expected domain failure (validation, not found, conflict) | Return `WP_Error` with a prefixed code |
| Programmer error / impossible state | Throw an exception; catch at the controller boundary |
| Third-party/HTTP failure | `is_wp_error( $response )` on `wp_remote_*`, then return `WP_Error` |
| Anything worth investigating later | Log it — with context, without secrets |

```php
/**
 * Logs a message when debugging is enabled.
 *
 * @since 1.0.0
 *
 * @param string $message Message to log.
 * @param array  $context Context data.
 * @return void
 */
function growmodo_log( $message, array $context = array() ) {
	if ( ! defined( 'WP_DEBUG' ) || ! WP_DEBUG ) {
		return;
	}

	error_log( // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log -- Debug-only logging.
		sprintf( '[growmodo] %s %s', $message, wp_json_encode( $context ) )
	);
}
```

- **Never echo an error detail to the browser** — no `$wpdb->last_error`, no exception
  message, no stack trace. `WP_DEBUG_DISPLAY` stays off outside local.
- Check `is_wp_error()` on every `WP_Error`-returning call. An unchecked `WP_Error` passed to
  `esc_html()` is a fatal waiting to happen.
- Catch `Throwable` (not just `Exception`) at boundaries.
- No `@` suppression. No swallowing a caught exception without logging.

---

## Caching & Performance

| Pattern | Use |
| --- | --- |
| `wp_cache_get()` / `wp_cache_set()` | Per-request (and persistent, if an object cache exists) memoization |
| `get_transient()` / `set_transient()` | Expensive external calls, always with an expiry |
| Query args | `no_found_rows`, `fields => 'ids'`, `update_post_meta_cache => false` |
| Batch reads | `get_posts( array( 'post__in' => $ids ) )` once, then index — not a query per ID |
| Cron | `wp_schedule_event` for slow work; never block a page render on a sync |

```php
$cache_key = 'growmodo_projects_' . md5( wp_json_encode( $args ) );
$projects  = wp_cache_get( $cache_key, 'growmodo' );

if ( false === $projects ) {
	$projects = $this->repository->find( $args );
	wp_cache_set( $cache_key, $projects, 'growmodo', 5 * MINUTE_IN_SECONDS );
}
```

- **Measure first** (Query Monitor, `SAVEQUERIES`, Xdebug). "Optimizations" without a before
  number are theatre — see `CLAUDE.md` § Evidence Requirements.
- **No queries inside loops.** Collect IDs, fetch once, index by ID.
- Invalidate on write, in the same service method that wrote.

---

## Internationalisation

```php
__( 'Project', 'growmodo' );                       // Return.
_e( 'Project', 'growmodo' );                       // Echo — only where already escaped.
esc_html__( 'Project', 'growmodo' );               // Return, escaped. Prefer this.
esc_html_e( 'Project', 'growmodo' );               // Echo, escaped. Prefer this.
_n( '%s project', '%s projects', $count, 'growmodo' );
_x( 'Draft', 'project status', 'growmodo' );

printf(
	/* translators: %1$s: Client name. %2$d: Project count. */
	esc_html__( '%1$s has %2$d projects.', 'growmodo' ),
	esc_html( $client_name ),
	absint( $count )
);
```

- **Literal text domain**, always `'growmodo'` — never a variable or constant.
- Never interpolate into the translatable string: `__( "Hello $name" )` is broken. Use
  placeholders + `printf()`.
- Numbered placeholders (`%1$s`) whenever there's more than one.
- `/* translators: */` comment above every string with a placeholder or ambiguous meaning.

---

## Documentation Pattern

Every function, method, class, property, constant, file and hook gets a DocBlock:
summary, `@since`, `@param`, `@return`. Full rules and hook-documentation format in
[`05-inline-documentation.md`](./docs/best-practices/05-inline-documentation.md).

```php
/**
 * Fires after a project has been archived.
 *
 * @since 1.0.0
 *
 * @param int   $project_id Archived project ID.
 * @param array $context    Archive context: reason, actor ID.
 */
do_action( 'growmodo_project_archived', $project_id, $context );
```

- `@since` is three digits: `1.0.0`.
- No `@return` on hook DocBlocks.
- Comments explain **why**, not what. `// Increment the counter.` above `$i++` is noise.

---

## Testing Pattern

```php
/**
 * Project service tests.
 *
 * @package Growmodo
 */

namespace Growmodo\Tests\Unit;

use Growmodo\Repositories\Project_Repository;
use Growmodo\Services\Project_Service;
use PHPUnit\Framework\TestCase;

/**
 * Covers the project save rules.
 */
class Project_Service_Test extends TestCase {

	/**
	 * A project without a title is rejected.
	 *
	 * @return void
	 */
	public function test_save_rejects_missing_title() {
		$repository = $this->createMock( Project_Repository::class );
		$repository->expects( $this->never() )->method( 'save' );

		$service = new Project_Service( $repository );
		$result  = $service->save(
			array(
				'id'      => 0,
				'title'   => '',
				'email'   => '',
				'status'  => 'draft',
				'summary' => '',
			)
		);

		$this->assertWPError( $result );
		$this->assertSame( 'growmodo_missing_title', $result->get_error_code() );
	}
}
```

- **Services are unit-testable because repositories are injected.** That's the payoff for the
  layering — if a test needs a real database to check a rule, the rule is in the wrong layer.
- `tests/unit/` — no WordPress bootstrap needed (mock the repository; Brain Monkey if core
  functions must be stubbed).
- `tests/integration/` — real WP test suite for repositories, hooks and REST routes.
- Every bug fix ships with the test that would have caught it.

---

## Readability & Abstraction

### Size guidelines

- **Functions > 50 lines**: review for extraction
- **Classes > 300 lines**: probably more than one responsibility
- **Files > 500 lines**: strong signal to refactor
- **Nesting > 3 levels**: invert with early returns

### Single responsibility

If you describe a function with "and", split it:

```php
// BAD — "verifies AND sanitizes AND validates AND saves AND emails AND redirects".
public function handle_save() {
	/* 140 lines */
}

// GOOD — the controller orchestrates; each step is named.
public function handle_save() {
	check_admin_referer( 'growmodo_save_project' );
	$this->require_capability( 'edit_posts' );

	$input  = $this->sanitize_project_input( $_POST );
	$result = $this->service->save( $input );

	$this->respond( $result );
}
```

### Early returns over nesting

```php
// BAD.
if ( $user ) {
	if ( current_user_can( 'edit_posts' ) ) {
		if ( ! empty( $input['title'] ) ) {
			// Real work, buried three levels deep.
		}
	}
}

// GOOD.
if ( ! $user ) {
	return new WP_Error( 'growmodo_no_user', __( 'Unknown user.', 'growmodo' ) );
}

if ( ! current_user_can( 'edit_posts' ) ) {
	return new WP_Error( 'growmodo_forbidden', __( 'Not allowed.', 'growmodo' ) );
}

if ( empty( $input['title'] ) ) {
	return new WP_Error( 'growmodo_missing_title', __( 'A title is required.', 'growmodo' ) );
}

// Real work, at the top level.
```

### Extract at the third repetition

Two occurrences: leave it. Three: extract a private method or a helper. Don't build the
abstraction on the first occurrence — you don't know its shape yet.

---

## Anti-Patterns

### Don't echo unescaped data

```php
// BAD — XSS.
echo '<h2>' . $project['title'] . '</h2>';
echo "<a href='{$url}'>link</a>";

// GOOD.
echo '<h2>' . esc_html( $project['title'] ) . '</h2>';
printf( '<a href="%s">%s</a>', esc_url( $url ), esc_html__( 'link', 'growmodo' ) );
```

### Don't read superglobals outside a controller

```php
// BAD — a service reaching into the request.
class Project_Service {
	public function save() {
		$title = $_POST['title']; // Untestable, unsanitized, wrong layer.
	}
}

// GOOD — the controller sanitizes and passes primitives in.
public function save( array $input ) { /* ... */ }
```

### Don't skip the capability check because there's a nonce

```php
// BAD — any logged-in subscriber can now delete projects.
check_admin_referer( 'growmodo_delete_project' );
$this->service->delete( $project_id );

// GOOD.
check_admin_referer( 'growmodo_delete_project' );
if ( ! current_user_can( 'delete_post', $project_id ) ) {
	wp_die( esc_html__( 'Not allowed.', 'growmodo' ), '', array( 'response' => 403 ) );
}
```

### Don't check roles — check capabilities

```php
// BAD.
if ( in_array( 'administrator', (array) $user->roles, true ) ) { }

// GOOD.
if ( current_user_can( 'manage_options' ) ) { }
```

### Don't interpolate into SQL

```php
// BAD — SQL injection.
$wpdb->get_results( "SELECT * FROM {$wpdb->posts} WHERE post_title = '$title'" );

// GOOD.
$wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->posts} WHERE post_title = %s", $title ) );
```

### Don't pass closures to hooks

```php
// BAD — impossible to remove, impossible to test.
add_filter( 'the_content', function ( $content ) { return $content . 'x'; } );

// GOOD.
add_filter( 'the_content', array( $this, 'append_project_meta' ), 20 );
```

### Don't query in templates

```php
// BAD — a query in the view, and it mutates the main loop.
query_posts( array( 'post_type' => 'growmodo_project' ) );

// GOOD — the repository queries, the controller passes data, the template renders.
$projects = $this->service->get_projects( $args );
```

`query_posts()` is banned outright. To alter the main query, filter `pre_get_posts`; for a
secondary loop, use `new WP_Query()` in a repository and `wp_reset_postdata()` after.

### Don't hardcode asset tags or paths

```php
// BAD.
echo '<script src="/wp-content/plugins/growmodo/assets/js/public.js"></script>';

// GOOD.
wp_enqueue_script( 'growmodo-public', GROWMODO_URL . 'assets/js/public.js', array(), GROWMODO_VERSION, true );
```

### Don't use `extract()`, `eval()`, `create_function()`, `goto`, backticks, or `@`

All banned by the standards — [`03-wordpress-php-standards.md`](./docs/best-practices/03-wordpress-php-standards.md) § 4.9.

### Don't silence `phpcs` instead of fixing it

```php
// BAD.
echo $title; // phpcs:ignore

// GOOD — fix it. If an ignore is genuinely right, it carries a reason.
echo esc_html( $title );
```

### Don't add globals

```php
// BAD.
global $growmodo_settings;

// GOOD — inject it, or read it through the repository that owns it.
```

### Don't modify core or a parent theme

Hooks, filters, child themes, `pre_get_posts`. If a change seems to require editing core,
the approach is wrong.

### Don't leave `posts_per_page => -1`

```php
// BAD — fine with 20 rows, fatal with 20,000.
'posts_per_page' => -1,

// GOOD.
'posts_per_page' => 100,
'no_found_rows'  => true,
```

### Don't return early from a hook without the expected value

```php
// BAD — a filter that returns nothing destroys the content.
add_filter( 'the_content', array( $this, 'maybe_append' ) );
public function maybe_append( $content ) {
	if ( ! is_singular() ) {
		return; // null — content gone.
	}
}

// GOOD — filters always return a value of the expected type.
public function maybe_append( $content ) {
	if ( ! is_singular() ) {
		return $content;
	}

	return $content . $this->render( 'partials/project-meta' );
}
```

---

## Before You Open a PR

Run the checklist in
[`00-quick-reference.md`](./docs/best-practices/00-quick-reference.md) § PR checklist, plus:

```bash
composer lint          # phpcs against phpcs.xml.dist
composer lint:fix      # phpcbf for the mechanical findings
composer analyze       # PHPStan with WP stubs
composer test          # PHPUnit
npm run lint:js        # ESLint (WordPress config)
npm run lint:css       # Stylelint (WordPress config)
```

Setup for all of the above: [`08-tooling.md`](./docs/best-practices/08-tooling.md).
