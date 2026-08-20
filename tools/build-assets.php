<?php
/**
 * Asset minifier.
 *
 * Generates growmodo/style.min.css and growmodo/assets/js/main.min.js from the
 * hand-written sources. Committed output, run manually — the theme needs no
 * build step to install or develop, but the brief asks for minified CSS/JS, and
 * WordPress core itself ships .min files alongside sources and switches between
 * them on SCRIPT_DEBUG. This reproduces that pattern.
 *
 * Standalone WordPress-free tooling, so PSR-12 applies here rather than WPCS
 * (see docs/best-practices/09-standards-conflicts.md).
 *
 * usage: php tools/build-assets.php
 *
 * @package Growmodo
 */

declare(strict_types=1);

/**
 * Minify CSS while preserving the contents of quoted strings.
 *
 * Naive whitespace collapsing corrupts `url("data:image/svg+xml,...")` values,
 * so the source is walked character by character and quoted runs are copied
 * verbatim.
 */
function growmodo_minify_css(string $css): string
{
    $out = '';
    $len = strlen($css);
    $quote = '';

    for ($i = 0; $i < $len; $i++) {
        $char = $css[$i];

        // Inside a quoted string: copy through until the matching quote.
        if ('' !== $quote) {
            $out .= $char;
            if ($char === $quote && '\\' !== $css[$i - 1]) {
                $quote = '';
            }
            continue;
        }

        if ('"' === $char || "'" === $char) {
            $quote = $char;
            $out .= $char;
            continue;
        }

        // Strip comments.
        if ('/' === $char && $i + 1 < $len && '*' === $css[$i + 1]) {
            $end = strpos($css, '*/', $i + 2);
            $i = false === $end ? $len : $end + 1;
            continue;
        }

        // Collapse whitespace runs to a single space.
        if (preg_match('/\s/', $char)) {
            if ('' !== $out && ' ' !== substr($out, -1)) {
                $out .= ' ';
            }
            continue;
        }

        $out .= $char;
    }

    // Drop spaces that are never significant, and the last semicolon in a block.
    $out = preg_replace('/\s*([{}:;,>])\s*/', '$1', $out);
    $out = str_replace(';}', '}', $out);

    // `and(` / `not(` in media queries need their space back.
    $out = preg_replace('/\b(and|not|or)\(/', '$1 (', $out);

    return trim($out);
}

/**
 * Minify JavaScript conservatively.
 *
 * Comments and indentation go; newlines stay. Collapsing lines would risk
 * automatic-semicolon-insertion bugs for a saving of a few hundred bytes, which
 * is not a trade worth making without a real parser.
 */
function growmodo_minify_js(string $js): string
{
    // Remove block comments.
    $js = preg_replace('#/\*.*?\*/#s', '', $js);

    $lines = [];

    foreach (explode("\n", $js) as $line) {
        // Strip whole-line comments only; trailing ones may sit inside strings.
        $trimmed = trim($line);

        if ('' === $trimmed || str_starts_with($trimmed, '//')) {
            continue;
        }

        $lines[] = $trimmed;
    }

    return implode("\n", $lines) . "\n";
}

$root = dirname(__DIR__) . '/growmodo';

$targets = [
    $root . '/style.css' => $root . '/style.min.css',
    $root . '/assets/js/main.js' => $root . '/assets/js/main.min.js',
];

foreach ($targets as $source => $destination) {
    if (!is_readable($source)) {
        fwrite(STDERR, "error: cannot read {$source}\n");
        exit(1);
    }

    $contents = (string) file_get_contents($source);
    $minified = str_ends_with($source, '.css')
        ? growmodo_minify_css($contents)
        : growmodo_minify_js($contents);

    file_put_contents($destination, $minified);

    $before = strlen($contents);
    $after = strlen($minified);
    $saved = 0 === $before ? 0 : round(100 - ($after / $before * 100));

    printf(
        "%-28s %6d B -> %6d B  (-%d%%)\n",
        basename($destination),
        $before,
        $after,
        $saved
    );
}
