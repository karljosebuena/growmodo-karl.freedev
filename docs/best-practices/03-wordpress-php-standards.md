# 03 — WordPress PHP Coding Standards

Source: <https://developer.wordpress.org/coding-standards/wordpress-coding-standards/php/>

> These are **mandatory** for any code running inside WordPress (theme, plugin, mu-plugin,
> template). The goal, in the handbook's words, is that the codebase stays "consistent and
> readable" no matter how many people contribute.

---

## 1. Formatting

### 1.1 Single vs. double quotes

If you're not evaluating anything in the string, use single quotes. Alternate quoting
styles to avoid escaping.

```php
// Correct.
echo '<a href="/static/link" class="button button-primary">Link name</a>';
echo "<a href='{$escaped_link}'>text with a ' single quote</a>";
```

Never put unescaped data into a string that goes to the browser — see
[04-wordpress-security.md](04-wordpress-security.md).

### 1.2 Indentation — real tabs

Use **real tabs, not spaces**; this gives the most flexibility across clients. Tabs
indent the start of a line; spaces may be used mid-line for alignment.

```php
[tab]$foo   = 'somevalue';
[tab]$foo2  = 'somevalue2';
[tab]$foo34 = 'somevalue3';
```

`switch` bodies indent one level, `case` bodies one more:

```php
switch ( $type ) {
[tab]case 'foo':
[tab][tab]some_function();
[tab][tab]break;
[tab]case 'bar':
[tab][tab]some_function();
[tab][tab]break;
}
```

### 1.3 Brace style

Opening brace on the same line. Braces are **always** used, even for a single statement.

```php
if ( condition ) {
	action1();
	action2();
} elseif ( condition2 && condition3 ) {
	action3();
	action4();
} else {
	defaultaction();
}
```

Alternative syntax is allowed in templates (closing statement on its own line):

```php
<?php if ( have_posts() ) : ?>
	<div class="hfeed">
		<?php while ( have_posts() ) : the_post(); ?>
			<article><!-- ... --></article>
		<?php endwhile; ?>
	</div>
<?php endif; ?>
```

### 1.4 Space usage

Put spaces after commas, and on both sides of logical, arithmetic, comparison, string and
assignment operators.

```php
SOME_CONST === 23;
foo() && bar();
! $foo;
array( 1, 2, 3 );
$baz . '-5';
$term .= 'X';
if ( $object instanceof Post_Type_Interface ) {}
$result = 2 ** 3; // 8.
```

Spaces inside the parentheses of control structures and function definitions/calls:

```php
foreach ( $foo as $bar ) { ...
if ( ! $foo ) { ...

function my_function( $param1 = 'foo', $param2 = 'bar' ) { ...
function my_other_function() { ...

my_function( $param1, func_param( $param2 ) );
my_other_function();
```

Nested parentheses also get inner spaces:

```php
if ( $foo && ( $bar || $baz ) ) { ...
my_function( ( $x - 1 ) * 5, $y );
```

**Type casts** — lowercase, canonical short form, one space after:

```php
$foo = (bool) $bar;     // Correct.
$foo = (boolean) $bar;  // Incorrect.
$foo = (int) $baz;      // Correct.
$foo = (integer) $baz;  // Incorrect.
```

**Array access** — spaces only around *variable* keys:

```php
$x = $foo['bar'];    // Correct.
$x = $foo[ 'bar' ];  // Incorrect.
$x = $foo[ $bar ];   // Correct.
$x = $foo[$bar];     // Incorrect.
```

**Switch `case`** — no space before the colon:

```php
switch ( $foo ) {
	case 'bar':   // Correct.
	case 'baz' :  // Incorrect.
}
```

**Increment/decrement** — no space between variable and operator:

```php
for ( $i = 0; $i < 10; $i++ ) {}   // Correct.
for ( $i = 0; $i < 10; $i ++ ) {}  // Incorrect.
```

**Spread operator** — space (or newline) before `...`, none after:

```php
function foo( &...$spread ) {
	bar( ...$spread );
	bar(
		array( ...$foo ),
		...array_values( $keyed_array )
	);
}
```

### 1.5 Trailing whitespace and closing tags

Remove trailing whitespace at the end of every line. No trailing blank lines at the end of
a function body. **Omit the closing `?>`** at the end of a PHP-only file.

### 1.6 Opening and closing PHP tags in HTML

For multi-line blocks, PHP tags go on their own lines at the same indentation as the
surrounding markup:

```php
// Correct.
function foo() {
	?>
	<div>
		<?php
		echo esc_html(
			bar(
				$baz,
				$bat
			)
		);
		?>
	</div>
	<?php
}
```

Single-line inline usage is fine:

```php
<input name="<?php echo esc_attr( $name ); ?>" />
```

```php
// Incorrect.
if ( $a === $b ) { ?>
<some html>
<?php }
```

### 1.7 No shorthand PHP tags

Never use shorthand start tags. Always use full tags.

```php
// Correct.
<?php ... ?>
<?php echo esc_html( $var ); ?>

// Incorrect.
<? ... ?>
<?= esc_html( $var ) ?>
```

### 1.8 Multi-line function calls

Each parameter on its own line, and each parameter no longer than one line — assign
multi-line values to a variable first.

```php
$bar = array(
	'use_this' => true,
	'meta_key' => 'field_name',
);
$baz = sprintf(
	/* translators: %s: Friend's name */
	__( 'Hello, %s!', 'yourtextdomain' ),
	$friend_name
);

$a = foo(
	$bar,
	$baz,
	sprintf( __( 'The best pet is a %s.' ), 'cat' )
);
```

### 1.9 SQL formatting

Break complex statements over lines, and **capitalize the SQL keywords** (`UPDATE`,
`WHERE`, …). Always escape with `$wpdb->prepare()`:

```php
$var = "dangerous'";
$id  = some_foo_number();

$wpdb->query(
	$wpdb->prepare(
		"UPDATE $wpdb->posts SET post_title = %s WHERE ID = %d",
		$var,
		$id
	)
);
```

Placeholders: `%d` integer, `%f` float, `%s` string, `%i` identifier (table/field name).
**Do not quote placeholders** — `prepare()` handles escaping and quoting.

---

## 2. Naming conventions

| Element | Convention | Example |
| --- | --- | --- |
| Functions, variables, actions, filters | lowercase, `_` separated — **never camelCase** | `function some_name( $some_variable ) {}` |
| Classes, interfaces, traits, enums | Capitalized words, `_` separated; acronyms all-caps | `class Walker_Category extends Walker {}`, `class WP_HTTP {}`, `interface Mailer_Interface {}`, `trait Forbid_Dynamic_Properties {}`, `enum Post_Status {}` |
| Constants | ALL CAPS, `_` separated | `define( 'DOING_AJAX', true );` |
| Files | lowercase, hyphen separated | `my-plugin-name.php` |
| Class files | `class-` prefix, underscores → hyphens | `class-wp-error.php` |
| Template files in `wp-includes` | `-template` suffix | `general-template.php` |

Avoid PHP reserved keywords as parameter names — they collide with PHP 8.0+ named
arguments.

### 2.1 Dynamic hook names — use interpolation

```php
do_action( "{$new_status}_{$post->post_type}", $post->ID, $post );
```

Prefer interpolation with braces over concatenation, and use succinct variable names
(`$user_id`) so the hook name stays readable.

### 2.2 Naming collisions

Prefix every global function, class, constant and hook with a unique project prefix, or
namespace them. `wp` and `WordPress` are reserved.

---

## 3. Declarations

### 3.1 Namespaces

Capitalized words separated by underscores, one namespace per file, at the top, with one
blank line before and after. No curly-brace syntax, no global-namespace declaration.

```php
namespace Prefix\Admin\Domain_URL\Sub_Domain\Event;
```

### 3.2 `use` import statements

Order: (1) namespaces/classes/interfaces/traits/enums, (2) functions, (3) constants.
No leading backslash. Aliases follow WordPress naming conventions.

```php
namespace Project_Name\Feature;

use Project_Name\Sub_Feature\Class_A;
use Project_Name\Sub_Feature\Class_C as Aliased_Class_C;
use Project_Name\Sub_Feature\{
	Class_D,
	Class_E as Aliased_Class_E,
}

use function Project_Name\Sub_Feature\function_a;
use function Project_Name\Sub_Feature\function_b as aliased_function;

use const Project_Name\Sub_Feature\CONSTANT_A;
use const Project_Name\Sub_Feature\CONSTANT_D as ALIASED_CONSTANT;
```

### 3.3 Arrays — long syntax only

```php
array( 1, 2, 3 )  // Correct.
[ 1, 2, 3 ]       // Incorrect.
```

Multi-item arrays with keys: one item per line, aligned `=>`, **trailing comma** (makes
reordering easier).

```php
$args = array(
	'post_type'   => 'page',
	'post_author' => 123,
	'post_status' => 'publish',
);
```

### 3.4 Visibility and modifiers

Always declare visibility explicitly. Never use `var`.

```php
// Correct.
class Foo {
	public $foo;
	protected function bar() {}
}

// Incorrect.
class Foo {
	var $foo;
	function bar() {}
}
```

Modifier order:

- **Classes:** `abstract`/`final` → `readonly`
- **Constants:** `final` → visibility
- **Properties:** visibility → `static`/`readonly` → type
- **Methods:** `abstract`/`final` → visibility → `static`

```php
abstract readonly class Foo {
	private const LABEL = 'Book';
	public static $foo;
	private readonly string $bar;
	abstract protected static function bar();
}
```

### 3.5 Type declarations

Exactly one space before and after the type; no space between `?` and the type.

```php
function foo( Class_Name $parameter, callable $callable, int $count = 0 ) {
	// Do something.
}

function bar(
	Interface_Name&Concrete_Class $param_a,
	string|int $param_b,
	callable $param_c = 'default_callable'
): User|false {
	// Do something.
}
```

Minimum PHP version per feature — check the project's minimum before using:

| Feature | PHP |
| --- | --- |
| Scalar types (`bool`, `int`, `float`, `string`), return types | 7.0 |
| Nullable types, `iterable`, `void` | 7.1 |
| `object` | 7.2 |
| Property types | 7.4 |
| `static` return, `mixed`, union types | 8.0 |
| Intersection types, `never` | 8.1 |
| Disjunctive normal form types | 8.2 |

Prefer `iterable` over `array` in type hints for flexibility (PHP 7.1+).

### 3.6 Trait `use` statements

One blank line before the first `use` and after the last.

```php
class Foo {

	use Bar_Trait;
	use Foo_Trait,
		Bazinga_Trait {
		Bar_Trait::method_name insteadof Bar_Trait;
		Bazinga_Trait::method_name as bazinga_method;
	}
	use Loopy_Trait {
		eat as protected;
	}

	public $baz = true;
}
```

### 3.7 Object instantiation

Always use parentheses; no space before them.

```php
$foo             = new Foo();
$anonymous_class = new class( $parameter ) { /* ... */ };
$instance        = new static();
```

### 3.8 One class per file

Only one class / interface / trait / enum per file.

### 3.9 `require` vs `include`

Use `require[_once]` — a missing file raises a fatal error rather than continuing in a
broken state.

```php
// Recommended.
require_once ABSPATH . 'file-name.php';

// Avoid.
include_once  ( ABSPATH . 'file-name.php' );
require_once     __DIR__ . '/file-name.php';
```

### 3.10 Magic constants

Uppercase magic constants, lowercase `class` keyword.

```php
add_action( 'action_name', array( __CLASS__, 'method_name' ) );
add_action( 'action_name', array( My_Class::class, 'method_name' ) );
```

---

## 4. Best practices & recommendations

### 4.1 Self-explanatory flag values

Boolean flags at call sites are unreadable. Use descriptive strings, or an `$args` array
when there are several.

```php
// Incorrect.
function eat( $what, $slowly = true ) { ... }
eat( 'mushrooms', true );

// Correct.
function eat( $what, $speed = 'slowly' ) { ... }
eat( 'mushrooms', 'slowly' );

// Many options.
function eat( $what, $args ) { ... }
eat( 'noodles', array( 'speed' => 'moderate' ) );
```

### 4.2 Ternary operators

Test for **truth**, not falsity. Never use the short ternary.

```php
// Correct.
$musictype = ( 'jazz' === $music ) ? 'cool' : 'blah';

// Incorrect.
$musictype = $music ?: 'blah';
```

### 4.3 Yoda conditions

Put the constant/literal/function call on the **left** for `==`, `!=`, `===`, `!==`, so a
typo'd `=` is a parse error instead of a silent assignment. Not used for `<`, `>`, `<=`, `>=`.

```php
if ( true === $the_force ) {
	$victorious = you_will( $be );
}
```

### 4.4 Strict comparisons

Loose comparisons are misleading — avoid them.

```php
// Correct.
if ( 0 === strpos( $text, 'WordPress' ) ) {
	echo esc_html__( 'Yay WordPress!', 'textdomain' );
}

// Incorrect.
if ( 0 == strpos( 'WordPress', 'foo' ) ) {
	echo esc_html__( 'Yay WordPress!', 'textdomain' );
}
```

### 4.5 No assignments in conditionals

```php
// Correct.
$data = $wpdb->get_var( '...' );
if ( $data ) {
	// Use $data.
}

// Incorrect.
if ( $data = $wpdb->get_var( '...' ) ) {
	// Use $data.
}
```

### 4.6 Control structures

Use `elseif`, never `else if`. Always brace.

### 4.7 Switch fall-through

Empty cases may fall through silently; a case with a body that intentionally falls
through requires an explicit `// no break` comment.

```php
switch ( $foo ) {
	case 'bar':        // Fall-through ok (empty case).
	case 'baz':
		echo esc_html( $foo );
	case 'cat':
		echo 'mouse';
		break;
	case 'dog':
		echo 'horse';
		// no break    // Explicit comment required.
	case 'fish':
		echo 'bird';
		break;
}
```

### 4.8 Error control operator `@`

Highly discouraged — it silences errors you then can't debug. Check explicitly instead.

```php
// Avoid.
@some_function();

// Better.
if ( function_exists( 'some_function' ) ) {
	some_function();
}
```

### 4.9 Constructs to avoid entirely

| Construct | Why |
| --- | --- |
| `extract()` | Creates invisible variables; impossible to trace |
| `eval()` | Dangerous, cannot be secured |
| `create_function()` | Deprecated in 7.2, removed in 8.0; uses `eval()` internally |
| `goto` | Never permitted |
| Backticks (shell exec) | Same as `shell_exec()`; usually disabled in `php.ini` |

### 4.10 Clever code vs. readability

Readability wins.

```php
// Clever but confusing.
isset( $var ) || $var = some_function();

// Clear.
if ( ! isset( $var ) ) {
	$var = some_function();
}
```

### 4.11 Closures

Fine as callbacks in limited contexts — but **never** pass a closure to `add_action()` or
`add_filter()`, because it can't be removed with `remove_action()` / `remove_filter()`.

```php
$caption = preg_replace_callback(
	'/<[a-zA-Z0-9]+(?: [^<>]+>)*/',
	function ( $matches ) {
		return preg_replace( '/[\r\n\t]+/', ' ', $matches[0] );
	},
	$caption
);
```

### 4.12 Regular expressions

Use PCRE (`preg_*`) functions, never the removed `/e` modifier — use
`preg_replace_callback()` instead. Single-quoted patterns reduce escaping noise.

### 4.13 Database queries

Prefer the WordPress API (`WP_Query`, `get_posts()`, `get_option()`, the meta API) over
direct SQL. When direct SQL is unavoidable, always `$wpdb->prepare()`.
