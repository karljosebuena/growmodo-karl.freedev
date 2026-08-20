# 08 — Tooling: Enforce the Standards Automatically

Source: <https://github.com/WordPress/WordPress-Coding-Standards> plus the standard
WordPress JS/CSS tooling. A standard nobody can run is a standard nobody follows — wire
these up before writing feature code.

---

## 1. PHP_CodeSniffer + WPCS

### Requirements

- PHP 7.2+
- Extensions: Filter, libxml, Tokenizer, XMLReader (recommended: iconv, Multibyte String)
- Composer

Since WPCS 3.0.0, **Composer installation is the only supported method**.

### Install (project-local — preferred)

```bash
composer config allow-plugins.dealerdirect/phpcodesniffer-composer-installer true
composer require --dev wp-coding-standards/wpcs:"^3.0"
```

### Install (global)

```bash
composer global config allow-plugins.dealerdirect/phpcodesniffer-composer-installer true
composer global require --dev wp-coding-standards/wpcs:"^3.0"
```

### Update

```bash
composer update wp-coding-standards/wpcs --with-dependencies
# or, global:
composer global update wp-coding-standards/wpcs --with-dependencies
```

Review the WPCS upgrade guide when moving from a pre-3.0 version.

### Rulesets

| Ruleset | Contents |
| --- | --- |
| `WordPress` | Everything — all sniffs available |
| `WordPress-Core` | The official WordPress PHP coding standards |
| `WordPress-Docs` | Inline documentation standards |
| `WordPress-Extra` | Extended best practices (includes `WordPress-Core`) |

### Running

```bash
vendor/bin/phpcs -ps . --standard=WordPress     # project-local install
phpcs -ps . --standard=WordPress                # global install

vendor/bin/phpcbf .                             # auto-fix what's fixable
vendor/bin/phpcs --report=summary .             # summary only
vendor/bin/phpcs path/to/file.php               # single file
```

Add a `phpcs.xml` / `.phpcs.xml.dist` at the project root and PHPCS picks it up
automatically. WPCS ships a `phpcs.xml.dist.sample` template.

### Recommended `phpcs.xml.dist`

```xml
<?xml version="1.0"?>
<ruleset name="Growmodo">
	<description>Coding standards for the Growmodo project.</description>

	<!-- What to scan. -->
	<file>.</file>
	<exclude-pattern>/vendor/*</exclude-pattern>
	<exclude-pattern>/node_modules/*</exclude-pattern>
	<exclude-pattern>/build/*</exclude-pattern>
	<exclude-pattern>*.min.js</exclude-pattern>

	<!-- Show sniff names and progress; use colours. -->
	<arg value="ps"/>
	<arg name="colors"/>
	<arg name="extensions" value="php"/>
	<arg name="parallel" value="8"/>

	<!-- The standards. -->
	<rule ref="WordPress-Core"/>
	<rule ref="WordPress-Docs"/>
	<rule ref="WordPress-Extra"/>

	<!-- Project prefixes for globals, hooks, functions, classes. -->
	<rule ref="WordPress.NamingConventions.PrefixAllGlobals">
		<properties>
			<property name="prefixes" type="array">
				<element value="growmodo"/>
				<element value="Growmodo"/>
				<element value="GROWMODO"/>
			</property>
		</properties>
	</rule>

	<!-- Text domain for i18n sniffs. -->
	<rule ref="WordPress.WP.I18n">
		<properties>
			<property name="text_domain" type="array">
				<element value="growmodo"/>
			</property>
		</properties>
	</rule>

	<!-- Minimum supported WP version for deprecation sniffs. -->
	<config name="minimum_wp_version" value="6.4"/>

	<!-- PHP cross-version compatibility (needs PHPCompatibilityWP). -->
	<config name="testVersion" value="7.4-"/>
	<rule ref="PHPCompatibilityWP"/>
</ruleset>
```

Companion packages worth adding:

```bash
composer require --dev phpcompatibility/phpcompatibility-wp:"*"
composer require --dev szepeviktor/phpstan-wordpress   # static analysis with WP stubs
composer require --dev yoast/phpunit-polyfills         # for WP core test suite
```

### Suppressing a sniff — with a reason

```php
// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in build_link().
echo $link_safe;

// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery -- Reporting query, cached below.
// ... queries ...
// phpcs:enable WordPress.DB.DirectDatabaseQuery.DirectQuery
```

Always include the `--` reason. A bare `phpcs:ignore` is a review blocker.

---

## 2. Composer scripts

```json
{
	"scripts": {
		"lint": "phpcs",
		"lint:fix": "phpcbf",
		"analyze": "phpstan analyse --memory-limit=1G",
		"test": "phpunit"
	},
	"config": {
		"allow-plugins": {
			"dealerdirect/phpcodesniffer-composer-installer": true
		}
	}
}
```

Then: `composer lint`, `composer lint:fix`, `composer analyze`.

---

## 3. JavaScript & CSS

The modern WordPress toolchain is `@wordpress/scripts`, which bundles ESLint, Stylelint,
Prettier and Jest preconfigured to WordPress standards:

```bash
npm install --save-dev @wordpress/scripts
```

```json
{
	"scripts": {
		"build": "wp-scripts build",
		"start": "wp-scripts start",
		"lint:js": "wp-scripts lint-js",
		"lint:css": "wp-scripts lint-style",
		"lint:pkg-json": "wp-scripts lint-pkg-json",
		"format": "wp-scripts format",
		"test:unit": "wp-scripts test-unit-js"
	}
}
```

Standalone configuration if not using `wp-scripts`:

```js
// .eslintrc.js
module.exports = {
	extends: [ 'plugin:@wordpress/eslint-plugin/recommended' ],
};
```

```js
// .stylelintrc.js
module.exports = {
	extends: [ '@wordpress/stylelint-config' ],
};
```

Legacy core workflow (JSHint via Grunt) is documented in
[06-html-css-js.md](06-html-css-js.md#linting-jshint--eslint).

---

## 4. `.editorconfig`

Gets tabs right in every editor, which is half of the WordPress style guide:

```ini
root = true

[*]
charset = utf-8
end_of_line = lf
insert_final_newline = true
trim_trailing_whitespace = true
indent_style = tab
indent_size = 4

[*.{yml,yaml,json,md}]
indent_style = space
indent_size = 2

[*.md]
trim_trailing_whitespace = false
```

---

## 5. Pre-commit hook

```bash
#!/bin/sh
# .git/hooks/pre-commit  (or manage via composer/husky)
CHANGED=$(git diff --cached --name-only --diff-filter=ACM | grep '\.php$')
[ -z "$CHANGED" ] && exit 0
vendor/bin/phpcs $CHANGED || {
	echo "PHPCS failed. Run 'composer lint:fix' and re-stage."
	exit 1
}
```

---

## 6. CI

```yaml
# .github/workflows/lint.yml
name: Lint
on: [ push, pull_request ]
jobs:
  php:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - uses: shivammathur/setup-php@v2
        with:
          php-version: '8.2'
          coverage: none
          tools: composer
      - run: composer install --prefer-dist --no-progress
      - run: composer lint
      - run: composer analyze
  js:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - uses: actions/setup-node@v4
        with:
          node-version: '20'
          cache: npm
      - run: npm ci
      - run: npm run lint:js
      - run: npm run lint:css
```

---

## 7. Local WordPress environment

```bash
npm install --save-dev @wordpress/env
npx wp-env start          # Docker-based WP instance
npx wp-env run cli wp --info
npx wp-env clean all
```

Alternatives: LocalWP, Laravel Valet + WP-CLI, or Docker Compose. Whichever it is, set
these in `wp-config.php` for development:

```php
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', true );
define( 'WP_DEBUG_DISPLAY', false );
define( 'SCRIPT_DEBUG', true );
define( 'SAVEQUERIES', true );
```
