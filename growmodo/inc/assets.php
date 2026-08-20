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
		GROWMODO_VERSION
	);

	wp_enqueue_script(
		'growmodo-main',
		$uri . $script_rel,
		array(),
		GROWMODO_VERSION,
		array(
			'in_footer' => true,
			'strategy'  => 'defer',
		)
	);
}
add_action( 'wp_enqueue_scripts', 'growmodo_enqueue_assets' );

/**
 * Print the announcement-bar visibility snippet.
 *
 * This has to run before first paint, so it is inlined in <head> rather than
 * enqueued: the bar renders visible by default (and therefore still works with
 * JavaScript disabled), and this hides it for visitors who already dismissed it
 * without the bar flashing on screen first.
 *
 * @since 1.0.0
 *
 * @return void
 */
function growmodo_print_announcement_state() {
	?>
	<script>
		try {
			if ( window.localStorage.getItem( 'growmodo-announcement-dismissed' ) === '1' ) {
				document.documentElement.classList.add( 'has-announcement-dismissed' );
			}
		} catch ( e ) {}
	</script>
	<?php
}
add_action( 'wp_head', 'growmodo_print_announcement_state', 1 );
