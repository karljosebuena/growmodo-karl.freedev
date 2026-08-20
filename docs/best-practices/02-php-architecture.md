# 02 — PHP Architecture: PSR-12, SOLID, DI

Source: *PHP Best Practices* — rtCamp Handbook
(<https://rtcamp.com/handbook/developing-for-block-editor-and-site-editor/php-best-practices/>).

> **Scope note.** rtCamp presents these under a WordPress handbook, but the rules are
> general PHP. Several of them (PSR-12 formatting, camelCase, short arrays) **conflict
> with the WordPress Coding Standards**. Apply them to standalone, namespaced application
> code; apply [03-wordpress-php-standards.md](03-wordpress-php-standards.md) to code that
> lives in WordPress hooks/templates. See
> [09-standards-conflicts.md](09-standards-conflicts.md).

---

## 1. Follow PSR-12 for consistency

PSR-12 is the PHP-FIG coding style guide (extends PSR-2), giving uniformity across
projects and teams: indentation, `declare(strict_types=1);`, spacing, brace placement.

```php
<?php
declare(strict_types=1);

namespace App;

class User
{
    private string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
```

Key PSR-12 points: 4-space indent, class opening brace on its own line, method opening
brace on its own line, one blank line after the `namespace` and after the `use` block,
visibility declared on all properties/methods, `strict_types` declared first.

---

## 2. SOLID principles

Write maintainable, scalable object-oriented code.

| Principle | Meaning |
| --- | --- |
| **S**ingle Responsibility | A class has one reason to change |
| **O**pen/Closed | Open for extension, closed for modification |
| **L**iskov Substitution | Subtypes must be usable wherever the base type is |
| **I**nterface Segregation | Many small interfaces beat one fat interface |
| **D**ependency Inversion | Depend on abstractions, not concretions |

### Single Responsibility example

❌ The entity also knows how to persist itself:

```php
<?php
class User
{
    public function saveToDatabase(): void
    {
        // Database save logic
    }
}
```

✅ Split data from persistence:

```php
<?php
class User
{
    // Class responsible for user data
}

class UserRepository
{
    public function save(User $user): void
    {
        // Handle database logic
    }
}
```

Applied to WordPress: keep hook callbacks thin. The callback wires things up; a service
class does the work. That makes the logic unit-testable without booting WordPress.

---

## 3. Dependency injection

DI ensures loose coupling and makes classes easy to unit test by injecting collaborators
instead of constructing them internally.

```php
<?php
class Mailer
{
    public function send($message)
    {
        // send logic
    }
}

class UserNotifier
{
    private Mailer $mailer;

    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    public function notify($user)
    {
        $this->mailer->send("Notify user: " . $user->name);
    }
}
```

Prefer type-hinting an **interface** (`MailerInterface`) over a concrete class so tests
can pass a fake. Constructor injection is the default; setter injection only for
optional collaborators.

---

## 4. Namespaces

Namespaces prevent naming conflicts, which matters in large apps and when integrating
third-party libraries.

```php
<?php
namespace App\Services;

class UserService
{
    // Service code
}
```

```php
<?php
use App\Services\UserService;

$userService = new UserService();
```

In WordPress plugins/themes, always namespace (or prefix) everything with a project
prefix — the global function/class namespace is shared with core, every other plugin and
the theme. `wp` and `WordPress` are reserved prefixes.

---

## 5. Error handling and logging

Proper error handling improves debugging and makes the application more robust. Use
`try`/`catch` and a logging library such as Monolog.

```php
<?php
try {
    // Some risky operation
} catch (Exception $e) {
    error_log($e->getMessage());
    // Handle the exception
}
```

Log context (IDs, inputs), not secrets. In WordPress, `WP_Error` is the idiomatic return
type for recoverable failures; check with `is_wp_error()`.

---

## 6. Prepared statements for database queries

Always use prepared, parameterised queries to prevent SQL injection.

```php
<?php
$pdo = new PDO('mysql:host=localhost;dbname=test', 'root', '');
$stmt = $pdo->prepare('SELECT * FROM users WHERE email = :email');
$stmt->execute(['email' => $email]);
$user = $stmt->fetch();
```

Use placeholders (`:email`) rather than inserting variables into the SQL string. The
WordPress equivalent is `$wpdb->prepare()` with `%s` / `%d` / `%f` / `%i`.

---

## 7. Type hinting and return types (PHP 7+)

Makes code predictable and reduces runtime errors.

```php
<?php
function add(int $a, int $b): int
{
    return $a + $b;
}
```

---

## 8. Autoloading

Autoloading removes manual `require`/`include` calls.

```json
{
    "autoload": {
        "psr-4": {
            "App\\": "src/"
        }
    }
}
```

Then run `composer dump-autoload`. Use `autoload-dev` for test-only namespaces, and
`composer dump-autoload --optimize --classmap-authoritative` for production builds.

---

## 9. Constants for fixed values and configuration

Constants improve readability and eliminate magic numbers/strings.

```php
<?php
class Config
{
    const DB_HOST = 'localhost';
    const DB_USER = 'root';
    const DB_PASS = 'password';
}
```

> ⚠️ **Never commit real credentials.** Config constants belong in environment variables
> (`getenv()` / `$_ENV`, `wp-config.php` outside version control), not in a class in the
> repository. The example shows the shape, not the storage location.

Also consider `enum` (PHP 8.1+) instead of a bag of class constants when the values form
a closed set.

---

## 10. Avoid global variables and functions

Globals make code harder to maintain, debug and test — anything can mutate them from
anywhere, and unit tests can't isolate them.

❌:

```php
<?php
global $user;
```

✅:

```php
<?php
class User
{
    private $name;

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }
}
```

WordPress itself exposes globals (`$wpdb`, `$post`, `$wp_query`). Don't add new ones:
wrap access in a class or pass values in explicitly.

---

## Summary

PHP best practices focus on writing **secure, maintainable, and efficient** code:
adhere to a style standard, apply OOP/SOLID design, inject dependencies, namespace and
autoload, use modern type declarations, prepare every query, and avoid globals.
