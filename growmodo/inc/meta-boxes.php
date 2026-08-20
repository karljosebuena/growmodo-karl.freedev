<?php
/**
 * Meta boxes for property and testimonial fields.
 *
 * Hand-rolled instead of ACF (decision log in docs/roadmap.md): every field
 * ships inside the theme, and saving enforces nonce + capability +
 * sanitization explicitly.
 *
 * @since 1.0.0
 *
 * @package Growmodo
 */

defined( 'ABSPATH' ) || exit;

/**
 * Field definitions per post type: meta key => array( label, input type ).
 *
 * Single source of truth for both rendering and saving.
 *
 * @since 1.0.0
 *
 * @return array[]
 */
function growmodo_meta_fields() {
	return array(
		'property'    => array(
			'growmodo_beds'     => array( __( 'Bedrooms', 'growmodo' ), 'number' ),
			'growmodo_baths'    => array( __( 'Bathrooms', 'growmodo' ), 'number' ),
			'growmodo_price'    => array( __( 'Price (USD)', 'growmodo' ), 'number' ),
			'growmodo_size'     => array( __( 'Size (sq ft)', 'growmodo' ), 'number' ),
			'growmodo_year'     => array( __( 'Year built', 'growmodo' ), 'number' ),
			'growmodo_type'     => array( __( 'Property type', 'growmodo' ), 'select' ),
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
 * @since 1.0.0
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
 * @since 1.0.0
 *
 * @param WP_Post $post Post being edited.
 * @return void
 */
function growmodo_render_meta_box( $post ) {
	$fields = growmodo_meta_fields()[ $post->post_type ];

	// Object-specific action so a nonce cannot be replayed against another post.
	wp_nonce_field( 'growmodo_save_meta_' . $post->ID, 'growmodo_meta_nonce' );

	foreach ( $fields as $key => $field ) {
		$value = get_post_meta( $post->ID, $key, true );

		printf(
			'<p><label for="%1$s"><strong>%2$s</strong></label><br />',
			esc_attr( $key ),
			esc_html( $field[0] )
		);

		if ( 'select' === $field[1] ) {
			printf( '<select id="%1$s" name="%1$s" class="widefat">', esc_attr( $key ) );
			printf( '<option value="">%s</option>', esc_html__( '— Select —', 'growmodo' ) );

			foreach ( growmodo_property_types() as $option => $label ) {
				printf(
					'<option value="%1$s"%2$s>%3$s</option>',
					esc_attr( $option ),
					selected( $option, $value, false ),
					esc_html( $label )
				);
			}

			echo '</select>';
		} else {
			printf(
				'<input type="%1$s" id="%2$s" name="%2$s" value="%3$s" class="widefat" />',
				esc_attr( $field[1] ),
				esc_attr( $key ),
				esc_attr( $value )
			);
		}

		echo '</p>';
	}
}

/**
 * Persist meta box values: nonce + capability + explicit sanitization.
 *
 * @since 1.0.0
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

	if ( ! wp_verify_nonce( $nonce, 'growmodo_save_meta_' . $post_id ) || ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	foreach ( $fields[ $post_type ] as $key => $field ) {
		if ( ! isset( $_POST[ $key ] ) ) {
			continue;
		}

		if ( 'number' === $field[1] ) {
			$value = absint( wp_unslash( $_POST[ $key ] ) );
		} else {
			$value = sanitize_text_field( wp_unslash( $_POST[ $key ] ) );

			// A select may only store a value from its own allowlist.
			if ( 'select' === $field[1] && ! array_key_exists( $value, growmodo_property_types() ) ) {
				$value = '';
			}
		}

		update_post_meta( $post_id, $key, $value );
	}
}
add_action( 'save_post', 'growmodo_save_meta' );
