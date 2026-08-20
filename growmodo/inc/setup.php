<?php
/**
 * Theme supports and navigation menus.
 *
 * @since 1.0.0
 *
 * @package Growmodo
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register theme supports and menu locations.
 *
 * @since 1.0.0
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

	// Card thumbnails and testimonial avatars are the only sizes the design
	// needs; both are cropped so grids never shift.
	add_image_size( 'growmodo-card', 640, 480, true );
	add_image_size( 'growmodo-avatar', 88, 88, true );
}
add_action( 'after_setup_theme', 'growmodo_setup' );

/**
 * Register the blog sidebar widget area.
 *
 * Used by the blog templates (index, single post, archive, search); the
 * marketing pages are full-width by design and do not call get_sidebar().
 *
 * @since 1.0.0
 *
 * @return void
 */
function growmodo_register_sidebars() {
	register_sidebar(
		array(
			'name'          => __( 'Blog Sidebar', 'growmodo' ),
			'id'            => 'blog-sidebar',
			'description'   => __( 'Appears beside blog posts, archives, and search results.', 'growmodo' ),
			'before_widget' => '<section id="%1$s" class="widget card %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget__title card__title">',
			'after_title'   => '</h2>',
		)
	);
}
add_action( 'widgets_init', 'growmodo_register_sidebars' );
