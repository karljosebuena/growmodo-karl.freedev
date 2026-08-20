<?php
/**
 * Front-end asset enqueues.
 *
 * @since 1.0.0
 *
 * @package Growmodo
 */

defined( 'ABSPATH' ) || exit;

/**
 * Version string for a theme asset, used to bust caches.
 *
 * The file's modification time rather than the theme version: editing a
 * stylesheet without bumping a constant would otherwise leave returning
 * visitors — and the live site after a redeploy — on a stale cached copy.
 *
 * @since 1.0.0
 *
 * @param string $path Absolute path to the asset.
 * @return string Modification time, or the theme version if unreadable.
 */
function growmodo_asset_version( $path ) {
	$mtime = is_readable( $path ) ? filemtime( $path ) : false;

	return false === $mtime ? GROWMODO_VERSION : (string) $mtime;
}

/**
 * Enqueue the Urbanist webfont, the theme stylesheet, and the main script.
 *
 * Serves the minified builds unless SCRIPT_DEBUG is on, mirroring how core
 * ships its own assets. Regenerate them with `php tools/build-assets.php`
 * after editing style.css or main.js.
 *
 * @since 1.0.0
 *
 * @return void
 */
function growmodo_enqueue_assets() {
	$min = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
	$dir = get_template_directory();
	$uri = get_template_directory_uri();

	// Fall back to the source files if a minified build is missing.
	$style_rel  = ( '' !== $min && file_exists( $dir . '/style.min.css' ) ) ? '/style.min.css' : '/style.css';
	$script_rel = ( '' !== $min && file_exists( $dir . '/assets/js/main.min.js' ) )
		? '/assets/js/main.min.js'
		: '/assets/js/main.js';

	wp_enqueue_style(
		'growmodo-fonts',
		'https://fonts.googleapis.com/css2?family=Urbanist:wght@400;500;600;700&display=swap',
		array(),
		GROWMODO_VERSION
	);

	wp_enqueue_style(
		'growmodo-style',
		$uri . $style_rel,
		array( 'growmodo-fonts' ),
		growmodo_asset_version( $dir . $style_rel )
	);

	wp_enqueue_script(
		'growmodo-main',
		$uri . $script_rel,
		array(),
		growmodo_asset_version( $dir . $script_rel ),
		array(
			'in_footer' => true,
			'strategy'  => 'defer',
		)
	);
}
add_action( 'wp_enqueue_scripts', 'growmodo_enqueue_assets' );

/**
 * Print the pre-paint state snippet.
 *
 * Both classes have to be on <html> before the first paint, so this is inlined
 * in <head> rather than enqueued:
 *
 * - `has-js` marks that scripting is running, so CSS can hide controls that
 *   only work with JavaScript. Doing that from main.js instead would reveal
 *   them after layout and shift the page.
 * - `has-announcement-dismissed` hides a bar the visitor already closed. The
 *   bar renders visible by default — so it still works with JavaScript off —
 *   and this stops it flashing on screen before being hidden.
 *
 * @since 1.0.0
 *
 * @return void
 */
function growmodo_print_prepaint_state() {
	?>
	<script>
		document.documentElement.classList.add( 'has-js' );

		try {
			if ( window.localStorage.getItem( 'growmodo-announcement-dismissed' ) === '1' ) {
				document.documentElement.classList.add( 'has-announcement-dismissed' );
			}
		} catch ( e ) {}
	</script>
	<?php
}
add_action( 'wp_head', 'growmodo_print_prepaint_state', 1 );
