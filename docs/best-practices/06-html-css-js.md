# 06 — WordPress HTML, CSS & JavaScript Standards

Sources:
[HTML](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/html/) ·
[CSS](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/css/) ·
[JavaScript](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/javascript/)

> Guiding philosophy (from the JS standards): *"All code in any code-base should look like
> a single person typed it, no matter how many people contributed."*

---

# HTML

## Validation

All HTML pages should be verified against the **W3C validator** to ensure the markup is
well formed. Automated validation complements, but does not replace, manual code review.

## Self-closing elements

A space is required before the forward slash:

```html
<br />   <!-- Correct -->
<br/>    <!-- Incorrect -->
```

## Attributes and tags

All tags and attributes must be **lowercase**. Attribute *values* are lowercase when a
machine interprets them, and use normal title capitalization when they're human-readable.

```html
<!-- Machine-readable value. -->
<meta http-equiv="content-type" content="text/html; charset=utf-8" />

<!-- Human-readable value. -->
<a href="http://example.com/" title="Description Here">Example.com</a>
```

## Quotes

All attributes must have a value and must be wrapped in double or single quotes.
**Failing to quote attributes can lead to security vulnerabilities.**

```html
<input type="text" name="email" disabled="disabled" />  <!-- Correct -->
<input type=text name=email disabled>                   <!-- Incorrect -->
```

For boolean attributes, omit the value entirely rather than writing `"true"` / `"false"`.

## Indentation

HTML indentation always reflects logical structure. **Use tabs, not spaces.** When mixing
PHP and HTML, indent PHP blocks to match the surrounding markup, with closing blocks at
the matching indentation level.

```php
<?php if ( ! have_posts() ) : ?>
	<div class="post-error">
		<p><?php esc_html_e( 'Sorry, no posts matched your criteria.', 'growmodo' ); ?></p>
	</div>
<?php endif; ?>
```

---

# CSS

## Structure

- Tabs (not spaces) for indentation.
- Two blank lines between sections; one blank line between blocks within a section.
- Each selector on its own line, ending with a comma or the opening brace.
- Each property-value pair on its own line, one tab in, terminated with a semicolon.
- Closing brace flush left with the selector.

```css
#selector-1,
#selector-2 {
	background: #fff;
	color: #000;
}
```

## Selectors

- Lowercase, hyphen-separated; avoid camelCase and underscores.
- Human-readable and descriptive.
- Double quotes in attribute selectors: `input[type="text"]`.
- Avoid over-qualified selectors — `.container`, not `div.container`.

```css
#comment-form {
	margin: 1em 0;
}
```

## Properties

- Colon then a single space before the value.
- Properties and values lowercase — except font names and vendor-specific values.
- Colours as hex codes or `rgba()`; shorten where possible (`#fff`, not `#FFFFFF`).
- Use shorthand for `background`, `border`, `font`, `list-style`, `margin`, `padding`.

## Property ordering

Two accepted approaches — pick one and be consistent:

1. **Logical / grouped:** Display → Positioning → Box model → Colours and Typography → Other
2. **Alphabetical**

Directional values follow **TRBL** (top, right, bottom, left); corners go top-left,
top-right, bottom-right, bottom-left.

## Vendor prefixes

Order longest to shortest: `-webkit-` → `-moz-` → unprefixed. WordPress uses **Autoprefixer**
to generate these, so author unprefixed where the build allows.

## Values

- Space after the colon, before the value.
- No padding inside parentheses.
- Every rule ends with a semicolon.
- Double quotes where quotes are needed.
- Numeric font weights (`400`, `700`) over keywords.
- Unitless zero values and unitless `line-height`.
- Leading zero on decimals (`0.5em`).
- Longer multi-part values broken across newlines, indented one level.

## Media queries

- Group media queries at the **bottom** of the stylesheet (exception: sectioned files like
  `wp-admin.css`).
- Indent the rule sets one level in.
- Test both above and below each breakpoint.

## Commenting

- Comment liberally; break comment lines at **80 characters**.
- Long stylesheets get a table of contents.
- Follow PHPDoc-style formatting.
- Section headers: newline before and after.
- Inline comments: no blank line separating them from the code they describe.

## Best practices

- When fixing an issue, **remove code before adding more**.
- Avoid "magic numbers" — arbitrary values like `margin-top: 37px`.
- Target elements directly rather than reaching them through parents.
- Use `height` only when you must include outside elements; prefer `line-height`.
- Don't restate default property combinations.

---

# JavaScript

## Code refactoring

Refactor new or updated code, not old files. **"Whitespace-only" patches are strongly
discouraged.** All new JavaScript must conform to the standards and pass linting.

## Spacing

- Tabs for indentation; no trailing whitespace.
- Lines ≤ 80 characters, hard maximum 100 (a tab counts as 4 spaces).
- All control blocks (`if`/`else`/`for`/`while`/`try`) use braces across multiple lines.
- Unary operators (`++`, `--`) have no adjacent space.
- No space before commas or semicolons.
- Spaces on both sides of the ternary `?` and `:`.
- No filler spaces in empty constructs: `{}`, `[]`, `fn()`.
- Newline at the end of every file.
- Space after the `!` negation operator.
- Function bodies indented one tab, even inside a file-level closure.

### Objects

```javascript
// Preferred for multi-line.
var obj = {
	ready: 9,
	when: 4,
	'you are': 15,
};

// Acceptable for short objects.
var obj = { ready: 9, when: 4, 'you are': 15 };
```

### Arrays and function calls

Extra spaces around elements and arguments:

```javascript
array = [ a, b ];
foo( arg );
foo( 'string', object );
prop = object[ 'default' ];
```

## Semicolons

Always use them. Never rely on Automatic Semicolon Insertion.

## Blocks, braces and line breaks

Opening brace on the same line; closing brace on the following line:

```javascript
if ( myFunction() ) {
	// Expressions
} else if ( ( a && b ) || c ) {
	// Expressions
}
```

Multi-line statements break **after** the operator:

```javascript
var html = '<p>The sum of ' + a + ' and ' + b + ' plus ' + c +
	' is ' + ( a + b + c ) + '</p>';
```

Long conditionals put each operand on its own line with extra indentation:

```javascript
if (
	firstCondition() &&
	secondCondition() &&
	thirdCondition()
) {
	doStuff();
}
```

Chained method calls: one call per line, indented when the context changes:

```javascript
elements
	.addClass( 'foo' )
	.children()
		.html( 'hello' )
	.end()
	.appendTo( 'body' );
```

## Assignments and globals

- **ES2015+:** `const` by default, `let` only when reassigned; declare at first use.
- **Legacy `var`:** a single comma-delimited statement at the top of the function;
  assignments on their own lines:

```javascript
var k, m, length,
	value = 'WordPress';
```

- Document globals at the top of the file: `/* global passwordStrength:true */`
  (`:true` means it's *defined* here; omit for read-only access).
- Backbone, Underscore and the `wp` object are registered allowed globals.
- jQuery via a closure: `( function ( $ ) { ... } )( jQuery );`
- Extend `wp` safely: `window.wp = window.wp || {};`

## Naming conventions

**camelCase — this differs from the PHP standards.**

| Element | Convention | Example |
| --- | --- | --- |
| Variables, functions | `camelCase` | `const userId = 1;` |
| Classes, constructors | `UpperCamelCase` | `class Earth {}` |
| Constants (never reassigned) | `SCREAMING_SNAKE_CASE` | `const MAX_RETRY_COUNT = 5;` |
| Acronyms | capitalized | `const currentDOMDocument = window.document;` |
| Acronym at start of a variable | respect camelCase | `const domDocument = window.document;` |
| Acronym at start of a class | capitalize | `class DOMDocument {}` |

`@wordpress/element` components use class naming whether implemented as a function or a
class.

## Comments

Precede a comment with one blank line. Capitalize the first letter, end sentences with a
period, single space after `//`.

```javascript
someStatement();

// Explanation of complex behavior follows.
$( 'p' ).doSomething();
```

JSDoc blocks open with `/**`. Inline comments are for parameter annotation only.

## Equality

Use strict equality `===` / `!==`, never `==` / `!=`.

## Type checks

| Check | Preferred |
| --- | --- |
| String | `typeof object === 'string'` |
| Number | `typeof object === 'number'` |
| Boolean | `typeof object === 'boolean'` |
| Object | `typeof object === 'object'` or `_.isObject( object )` |
| Plain object | `jQuery.isPlainObject( object )` |
| Function | `_.isFunction( object )` |
| Array | `_.isArray( object )` |
| Element | `object.nodeType` or `_.isElement( object )` |
| null | `object === null` |
| undefined (global) | `typeof variable === 'undefined'` |
| undefined (local) | `variable === undefined` |

## Strings

Single quotes; escape internal single quotes.

```javascript
var myStr = 'strings should be contained in single quotes';
var note  = 'Note the backslash before the \'single quotes\'';
```

## Switch statements

Use sparingly. Always `break` (except `default`); indent `case` one tab. Set values inside
the blocks and return **after** the switch rather than within it.

```javascript
switch ( event.keyCode ) {
	case $.ui.keyCode.ENTER:
	case $.ui.keyCode.SPACE:
		x();
		break;
	default:
		z();
}
```

## Best practices

- Arrays: shorthand `[]`, not `new Array()` — `var myArray = [ 1, 'WordPress', 2 ];`
- Objects: object literal `{}` unless a specific prototype is needed.
- Property access: dot notation, except for variable or non-identifier keys:

```javascript
prop = object.propertyName;
prop = object[ variableKey ];
prop = object['key-with-hyphens'];
```

- Iteration: cache the loop maximum so it isn't recomputed:

```javascript
for ( i = 0, max = getItemCount(); i < max; i++ ) {
	// Do stuff
}
```

- Use Underscore collection methods (`_.each`, `_.map`, `_.reduce`) where they clarify.
- jQuery iteration **only** for jQuery collections — never for raw data or plain objects:

```javascript
$tabs.each( function ( index, element ) {
	var $element = $( element );
	// Do stuff
} );
```

## Linting (JSHint / ESLint)

1. Install Node.js.
2. From the WordPress root (the directory with `package.json`), run `npm install`.
3. Run `npm run grunt jshint` to check all files.

Useful variants:

```bash
npm run grunt jshint:core                        # core files only
npm run grunt jshint:tests                       # test files only
npm run grunt jshint:core --file=admin-bar.js    # single file
```

Exclude third-party/minified code:

```javascript
/* jshint ignore:start */
// Third-party minified code
/* jshint ignore:end */
```

Modern WordPress projects use **`@wordpress/eslint-plugin`** instead of JSHint — see
[08-tooling.md](08-tooling.md).
