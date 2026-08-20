# 01 — General PHP Best Practices

Source: *Main Best Practices in PHP* — Philippe Beck
(<https://medium.com/@philippebeck/main-best-practices-in-php-ceff378df8f1>),
with corrections noted where the article's examples are outdated.

---

## 1. Writing secure PHP code

### 1.1 Input validation

Ensure user data meets specific criteria **before** processing or storing it. This
prevents malicious data from being processed, reducing the risk of injection
vulnerabilities.

```php
if ( filter_input( INPUT_POST, 'email', FILTER_VALIDATE_EMAIL ) ) {
	// Process the valid email.
} else {
	// Handle invalid email input.
}
```

**Validate, don't just clean:** validation rejects data that doesn't fit an expected
shape (an int, an email, a value from a known list). Sanitization only strips
characters. Prefer validation whenever the expected shape is known.

### 1.2 Authentication & authorization

Authentication verifies *who* the user is. Authorization decides *what* they may do.
They are separate checks and you need both.

```php
$username = filter_input( INPUT_POST, 'username' );
$password = filter_input( INPUT_POST, 'password' );

if ( password_verify( $password, $hashed_password ) ) {
	// Successful login, proceed to authorized actions.
} else {
	// Invalid credentials, deny access.
}
```

- Store passwords with `password_hash()` (bcrypt/argon2), never a raw hash function.
- Re-check authorization on **every** request, not just at login.

> ⚠️ **Correction:** the original article uses `FILTER_SANITIZE_STRING`. That filter is
> **deprecated as of PHP 8.1** and must not be used. Use
> `filter_input( INPUT_POST, 'x', FILTER_UNSAFE_RAW )` plus explicit escaping, or
> `htmlspecialchars()` at output, or in WordPress `sanitize_text_field()`.

### 1.3 Protection against SQL injection

Use prepared statements with bound parameters. Never concatenate user data into SQL.

```php
$stmt = $pdo->prepare( 'SELECT * FROM users WHERE username = :username' );
$stmt->execute( array( 'username' => $username ) );
```

Also: create the PDO connection with `PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION` and
`PDO::ATTR_EMULATE_PREPARES => false`.

In WordPress, use `$wpdb->prepare()` — see [04-wordpress-security.md](04-wordpress-security.md).

### 1.4 Protection against XSS

Escape untrusted data before rendering it:

```php
echo htmlspecialchars( $untrusted_data, ENT_QUOTES, 'UTF-8' );
```

Escaping is **context-dependent**: HTML body, HTML attribute, URL, JavaScript and CSS
each need a different escaper. In WordPress use `esc_html()`, `esc_attr()`, `esc_url()`,
`esc_js()`, `esc_textarea()`, `wp_kses_post()`.

### 1.5 Protection against CSRF

Generate a token, store it in the session, and verify it on submission.

```php
// Generate.
$csrf_token             = bin2hex( random_bytes( 32 ) );
$_SESSION['csrf_token'] = $csrf_token;

// Verify.
$submitted = filter_input( INPUT_POST, 'csrf_token' );

if ( hash_equals( $_SESSION['csrf_token'], (string) $submitted ) ) {
	// CSRF token is valid, process the form submission.
} else {
	// CSRF token is invalid, reject the form submission.
}
```

> ⚠️ **Correction:** compare tokens with `hash_equals()` (timing-attack safe) rather than
> `===`, and don't run the stored token through a sanitize filter. In WordPress, use
> nonces (`wp_nonce_field()` / `check_admin_referer()`), which implement this pattern.

### 1.6 Use secure, maintained libraries

Prefer well-reviewed components over hand-rolled cryptography or parsing:

```php
$encrypted_data = SecureCryptography::encrypt( $sensitive_data );
```

Use `sodium_*` (libsodium, bundled since PHP 7.2) for encryption. Audit dependencies with
`composer audit`.

### 1.7 Stay informed about security updates

Follow the security mailing lists / advisories for PHP, your framework and your
dependencies, and patch known vulnerabilities promptly.

---

## 2. Object-oriented principles

### 2.1 Encapsulation

Restrict access to an object's internals so state can't be modified accidentally.

```php
class Car {
	private $make;
	private $model;

	public function __construct( $make, $model ) {
		$this->make  = $make;
		$this->model = $model;
	}

	public function getMake() {
		return $this->make;
	}

	public function getModel() {
		return $this->model;
	}
}
```

### 2.2 Abstraction

Hide implementation detail and expose only what callers need.

```php
abstract class Shape {
	abstract public function calculateArea();
}

class Circle extends Shape {
	private $radius;

	public function __construct( $radius ) {
		$this->radius = $radius;
	}

	public function calculateArea() {
		return pi() * $this->radius * $this->radius;
	}
}
```

Inheritance and polymorphism follow from these two: program against the abstract type
(`Shape`), not the concrete one (`Circle`). See [02-php-architecture.md](02-php-architecture.md)
for the full SOLID treatment.

---

## 3. Performance

### 3.1 Caching (Memcached / Redis / opcache)

```php
$key     = 'unique_cache_key_for_results';
$results = $memcached->get( $key );

if ( ! $results ) {
	$results = /* fetch results from the database */;
	$memcached->set( $key, $results, $expiration_time );
}
```

Enable **OPcache** in production — it's the single highest-value PHP performance setting.

### 3.2 Database optimisation

Index the columns you filter and sort on, select only the columns you need, and limit
result sets.

```sql
CREATE INDEX idx_name ON users (name);
```

Watch for N+1 queries: one query in a loop over N rows is N+1 round trips.

### 3.3 Code optimisation

Avoid deeply nested loops, unnecessary recursion, and expensive operations repeated
inside loops. Prefer PHP's optimised built-ins:

```php
$multiplied_values = array_map(
	function ( $value ) {
		return $value * 2;
	},
	$original_array
);
```

### 3.4 Application-level caching

```php
$config = $cache->get( 'app_config' );

if ( ! $config ) {
	$config = /* fetch configuration from the database or file */;
	$cache->set( 'app_config', $config, $expiration_time );
}
```

### 3.5 HTTP caching

```php
header( 'Cache-Control: max-age=3600' ); // Cache for 1 hour.
```

### 3.6 Profile before optimising

Use Xdebug (or Blackfire / SPX) to find the actual bottleneck rather than guessing.

```php
xdebug_start_profiling();
// Run the code to be profiled.
xdebug_stop_profiling();
```

---

## 4. Errors & exceptions

### 4.1 Error handling

```php
error_reporting( E_ALL );

function customErrorHandler( $errno, $errstr, $errfile, $errline ) {
	echo "Error: $errstr in $errfile at line $errline";
}

set_error_handler( 'customErrorHandler' );

$var = 1;
if ( $var > 0 ) {
	trigger_error( 'The variable cannot be positive', E_USER_ERROR );
}
```

**In production:** `display_errors = Off`, `log_errors = On`. Never print error details,
stack traces or SQL to the browser — they leak implementation detail to attackers.

### 4.2 Exception handling

```php
try {
	$result = 10 / 0;
} catch ( DivisionByZeroError $e ) {
	echo 'A division by zero occurred: ' . $e->getMessage();
} catch ( Throwable $e ) {
	echo 'An error occurred: ' . $e->getMessage();
} finally {
	// Code executed whether an exception is thrown or not.
	echo 'Cleanup or final actions';
}
```

- Catch the **most specific** exception first; `Throwable` last.
- Catch only what you can actually handle — otherwise let it bubble to a global handler.
- `finally` is for cleanup (closing handles, releasing locks) and always runs.

---

## 5. Modern PHP features

### 5.1 Null coalescing `??`

```php
$username = filter_input( INPUT_GET, 'user' ) ?? 'Guest';
```

Useful for fallbacks when a variable may be null or unset. `??=` assigns only when
null/unset.

### 5.2 Spaceship operator `<=>`

Returns `-1`, `0` or `1` — ideal for comparison callbacks.

```php
usort(
	$products,
	function ( $a, $b ) {
		return $a['price'] <=> $b['price'];
	}
);
```

### 5.3 Anonymous classes

```php
$logger = new class {
	public function log( $message ) {
		echo $message;
	}
};

$logger->log( 'Logging a message' );
```

Good for simple, one-off objects (test doubles, tiny adapters) inside a single scope.

### 5.4 Type declarations

```php
function multiply( int $a, int $b ): int {
	return $a * $b;
}
```

Enforces stricter typing and makes intent explicit. Pair with `declare(strict_types=1);`
at the top of standalone PHP files so types are enforced rather than coerced.

### 5.5 Other modern features worth using

| Feature | PHP | Use |
| --- | --- | --- |
| Nullsafe operator `?->` | 8.0 | Chain calls that may be null |
| Named arguments | 8.0 | Self-documenting calls with optional params |
| Constructor property promotion | 8.0 | Cuts boilerplate in value objects |
| `match` expression | 8.0 | Strict, exhaustive alternative to `switch` |
| Enums | 8.1 | Replace magic string/int constants |
| `readonly` properties | 8.1 | Immutable value objects |
| First-class callable syntax `f(...)` | 8.1 | Safer than string callables |

> ⚠️ **WordPress caveat:** these are for standalone application code. Code that ships
> inside a WordPress theme/plugin must stay compatible with the project's **minimum
> supported PHP version** — check it before using 8.x-only syntax.
