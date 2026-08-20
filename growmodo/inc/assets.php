<?php
/**
 * Front-end asset enqueues.
 *
 * @package Growmodo
 */

/**
 * Enqueue the Urbanist webfont, the theme stylesheet, and the main script.
 *
 * @return void
 */
function growmodo_enqueue_assets() {
	wp_enqueue_style(
		'growmodo-fonts',
		'https://fonts.googleapis.com/css2?family=Urbanist:wght@400;500;600;700&display=swap',
		array(),
		GROWMODO_VERSION
	);

	wp_enqueue_style(
		'growmodo-style',
		get_stylesheet_uri(),
		array( 'growmodo-fonts' ),
		GROWMODO_VERSION
	);

	wp_enqueue_script(
		'growmodo-main',
		get_template_directory_uri() . '/assets/js/main.js',
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
