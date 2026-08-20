<?php
/**
 * Meta boxes for property and testimonial fields.
 *
 * Hand-rolled instead of ACF (decision log in docs/roadmap.md): every field
 * ships inside the theme, and saving enforces nonce + capability +
 * sanitization explicitly.
 *
 * @package Growmodo
 */

/**
 * Field definitions per post type: meta key => array( label, input type ).
 *
 * Single source of truth for both rendering and saving.
 *
 * @return array[]
 */
function growmodo_meta_fields() {
	return array(
		'property'    => array(
			'growmodo_beds'     => array( __( 'Bedrooms', 'growmodo' ), 'number' ),
			'growmodo_baths'    => array( __( 'Bathrooms', 'growmodo' ), 'number' ),
			'growmodo_price'    => array( __( 'Price (USD)', 'growmodo' ), 'number' ),
			'growmodo_type'     => array( __( 'Property type', 'growmodo' ), 'text' ),
			'growmodo_location' => array( __( 'Location', 'growmodo' ), 'text' ),
		),
		'testimonial' => array(
			'growmodo_rating'          => array( __( 'Rating (1–5)', 'growmodo' ), 'number' ),
			'growmodo_client_name'     => array( __( 'Client name', 'growmodo' ), 'text' ),
			'growmodo_client_location' => array( __( 'Client location', 'growmodo' ), 'text' ),
		),
	);
}

/**
 * Register the Details meta box on both post types.
 *
 * @return void
 */
function growmodo_add_meta_boxes() {
	foreach ( array_keys( growmodo_meta_fields() ) as $post_type ) {
		add_meta_box(
			'growmodo-details',
			__( 'Details', 'growmodo' ),
			'growmodo_render_meta_box',
			$post_type,
			'side'
		);
	}
}
add_action( 'add_meta_boxes', 'growmodo_add_meta_boxes' );

/**
 * Render the fields for the current post type's meta box.
 *
 * @param WP_Post $post Post being edited.
 * @return void
 */
function growmodo_render_meta_box( $post ) {
	$fields = growmodo_meta_fields()[ $post->post_type ];

	wp_nonce_field( 'growmodo_save_meta', 'growmodo_meta_nonce' );

	foreach ( $fields as $key => $field ) {
		printf(
			'<p><label for="%1$s"><strong>%2$s</strong></label><br /><input type="%3$s" id="%1$s" name="%1$s" value="%4$s" class="widefat" /></p>',
			esc_attr( $key ),
			esc_html( $field[0] ),
			esc_attr( $field[1] ),
			esc_attr( get_post_meta( $post->ID, $key, true ) )
		);
	}
}

/**
 * Persist meta box values: nonce + capability + explicit sanitization.
 *
 * @param int $post_id Post ID being saved.
 * @return void
 */
function growmodo_save_meta( $post_id ) {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	$fields    = growmodo_meta_fields();
	$post_type = get_post_type( $post_id );

	if ( ! isset( $fields[ $post_type ] ) ) {
		return;
	}

	$nonce = isset( $_POST['growmodo_meta_nonce'] )
		? sanitize_key( wp_unslash( $_POST['growmodo_meta_nonce'] ) )
		: '';

	if ( ! wp_verify_nonce( $nonce, 'growmodo_save_meta' ) || ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	foreach ( $fields[ $post_type ] as $key => $field ) {
		if ( ! isset( $_POST[ $key ] ) ) {
			continue;
		}

		$value = 'number' === $field[1]
			? absint( wp_unslash( $_POST[ $key ] ) )
			: sanitize_text_field( wp_unslash( $_POST[ $key ] ) );

		update_post_meta( $post_id, $key, $value );
	}
}
add_action( 'save_post', 'growmodo_save_meta' );
