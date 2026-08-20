<?php
/**
 * Theme supports and navigation menus.
 *
 * @package Growmodo
 */

/**
 * Register theme supports and menu locations.
 *
 * @return void
 */
function growmodo_setup() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'custom-logo' );
	add_theme_support(
		'html5',
		array( 'search-form', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' )
	);

	register_nav_menus(
		array(
			'primary' => __( 'Primary Menu', 'growmodo' ),
			'footer'  => __( 'Footer Menu', 'growmodo' ),
		)
	);
}
add_action( 'after_setup_theme', 'growmodo_setup' );
