<?php
/**
 * Custom post types and post meta.
 *
 * Four types (decision log in docs/roadmap.md):
 * - property     Public listings with beds/baths/price/type/location meta.
 * - testimonial  Client quotes shown in page sections; admin-only UI.
 * - faq          Question (title) + answer (content); admin-only UI.
 * - inquiry      Private storage for front-end form submissions.
 *
 * @since 1.0.0
 *
 * @package Growmodo
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register the theme's post types.
 *
 * @since 1.0.0
 *
 * @return void
 */
function growmodo_register_post_types() {
	register_post_type(
		'property',
		array(
			'labels'      => array(
				'name'          => __( 'Properties', 'growmodo' ),
				'singular_name' => __( 'Property', 'growmodo' ),
				'add_new_item'  => __( 'Add New Property', 'growmodo' ),
				'edit_item'     => __( 'Edit Property', 'growmodo' ),
			),
			'public'      => true,
			'has_archive' => true,
			'menu_icon'   => 'dashicons-admin-multisite',
			'rewrite'     => array( 'slug' => 'properties' ),
			'supports'    => array( 'title', 'editor', 'excerpt', 'thumbnail', 'page-attributes' ),
		)
	);

	register_post_type(
		'testimonial',
		array(
			'labels'    => array(
				'name'          => __( 'Testimonials', 'growmodo' ),
				'singular_name' => __( 'Testimonial', 'growmodo' ),
			),
			'public'    => false,
			'show_ui'   => true,
			'menu_icon' => 'dashicons-format-quote',
			'supports'  => array( 'title', 'editor', 'thumbnail', 'page-attributes' ),
		)
	);

	register_post_type(
		'faq',
		array(
			'labels'    => array(
				'name'          => __( 'FAQs', 'growmodo' ),
				'singular_name' => __( 'FAQ', 'growmodo' ),
			),
			'public'    => false,
			'show_ui'   => true,
			'menu_icon' => 'dashicons-editor-help',
			'supports'  => array( 'title', 'editor', 'page-attributes' ),
		)
	);

	// Submissions are created by the form handler only — no "Add New" in admin.
	register_post_type(
		'inquiry',
		array(
			'labels'       => array(
				'name'          => __( 'Inquiries', 'growmodo' ),
				'singular_name' => __( 'Inquiry', 'growmodo' ),
			),
			'public'       => false,
			'show_ui'      => true,
			'menu_icon'    => 'dashicons-email',
			'map_meta_cap' => true,
			'capabilities' => array( 'create_posts' => 'do_not_allow' ),
			'supports'     => array( 'title', 'editor' ),
		)
	);
}
add_action( 'init', 'growmodo_register_post_types' );

/**
 * Register post meta with types and sanitizers.
 *
 * @since 1.0.0
 *
 * @return void
 */
function growmodo_register_post_meta() {
	$meta = array(
		'property'    => array(
			'growmodo_beds'     => array( 'integer', 'absint' ),
			'growmodo_baths'    => array( 'integer', 'absint' ),
			'growmodo_price'    => array( 'integer', 'absint' ),
			'growmodo_size'     => array( 'integer', 'absint' ),
			'growmodo_year'     => array( 'integer', 'absint' ),
			'growmodo_type'     => array( 'string', 'sanitize_text_field' ),
			'growmodo_location' => array( 'string', 'sanitize_text_field' ),
		),
		'testimonial' => array(
			'growmodo_rating'          => array( 'integer', 'absint' ),
			'growmodo_client_name'     => array( 'string', 'sanitize_text_field' ),
			'growmodo_client_location' => array( 'string', 'sanitize_text_field' ),
		),
		'inquiry'     => array(
			'growmodo_email'          => array( 'string', 'sanitize_email' ),
			'growmodo_phone'          => array( 'string', 'sanitize_text_field' ),
			'growmodo_first_name'     => array( 'string', 'sanitize_text_field' ),
			'growmodo_last_name'      => array( 'string', 'sanitize_text_field' ),
			'growmodo_pref_location'  => array( 'string', 'sanitize_text_field' ),
			'growmodo_pref_type'      => array( 'string', 'sanitize_text_field' ),
			'growmodo_pref_beds'      => array( 'integer', 'absint' ),
			'growmodo_pref_baths'     => array( 'integer', 'absint' ),
			'growmodo_budget'         => array( 'string', 'sanitize_text_field' ),
			'growmodo_contact_method' => array( 'string', 'sanitize_text_field' ),
			'growmodo_inquiry_type'   => array( 'string', 'sanitize_text_field' ),
			'growmodo_referrer'       => array( 'string', 'sanitize_text_field' ),
			'growmodo_property_id'    => array( 'integer', 'absint' ),
		),
	);

	foreach ( $meta as $post_type => $fields ) {
		foreach ( $fields as $key => $config ) {
			register_post_meta(
				$post_type,
				$key,
				array(
					'type'              => $config[0],
					'single'            => true,
					'sanitize_callback' => $config[1],
				)
			);
		}
	}
}
add_action( 'init', 'growmodo_register_post_meta' );
