<?php
/**
 * Meta boxes for property, testimonial and enquiry fields.
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
 * Field definitions per post type: meta key => array( label, input type, options ).
 *
 * Single source of truth for both rendering and saving. A `select` names the
 * function returning its own option list, which is also the allowlist the save
 * handler validates against — so a field's options are declared once.
 *
 * Enquiries are included because the front-end form captures a dozen fields; a
 * lead nobody can read in wp-admin is a lead that was not really captured.
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
			'growmodo_type'     => array( __( 'Property type', 'growmodo' ), 'select', 'growmodo_property_types' ),
			'growmodo_location' => array( __( 'Location', 'growmodo' ), 'text' ),
			'growmodo_features' => array( __( 'Key features and amenities (one per line)', 'growmodo' ), 'textarea' ),
		),
		'testimonial' => array(
			'growmodo_rating'          => array( __( 'Rating (1–5)', 'growmodo' ), 'number' ),
			'growmodo_client_name'     => array( __( 'Client name', 'growmodo' ), 'text' ),
			'growmodo_client_location' => array( __( 'Client location', 'growmodo' ), 'text' ),
		),
		'inquiry'     => array(
			'growmodo_first_name'     => array( __( 'First name', 'growmodo' ), 'text' ),
			'growmodo_last_name'      => array( __( 'Last name', 'growmodo' ), 'text' ),
			'growmodo_email'          => array( __( 'Email', 'growmodo' ), 'email' ),
			'growmodo_phone'          => array( __( 'Phone', 'growmodo' ), 'text' ),
			'growmodo_contact_method' => array( __( 'Preferred contact method', 'growmodo' ), 'select', 'growmodo_contact_methods' ),
			'growmodo_inquiry_type'   => array( __( 'Inquiry type', 'growmodo' ), 'select', 'growmodo_inquiry_types' ),
			'growmodo_referrer'       => array( __( 'Heard about us via', 'growmodo' ), 'select', 'growmodo_referrer_options' ),
			'growmodo_pref_location'  => array( __( 'Preferred location', 'growmodo' ), 'text' ),
			'growmodo_pref_type'      => array( __( 'Preferred property type', 'growmodo' ), 'select', 'growmodo_property_types' ),
			'growmodo_pref_beds'      => array( __( 'Bedrooms wanted', 'growmodo' ), 'number' ),
			'growmodo_pref_baths'     => array( __( 'Bathrooms wanted', 'growmodo' ), 'number' ),
			'growmodo_budget'         => array( __( 'Budget', 'growmodo' ), 'select', 'growmodo_price_bands' ),
		),
	);
}

/**
 * Option list for a select field, from the function its definition names.
 *
 * The same list renders the options and validates what comes back, so a value
 * the editor was never offered cannot be stored.
 *
 * @since 1.0.0
 *
 * @param array $field Field definition from growmodo_meta_fields().
 * @return array Labels keyed by stored value; empty when the field names none.
 */
function growmodo_field_options( $field ) {
	if ( ! isset( $field[2] ) || ! is_callable( $field[2] ) ) {
		return array();
	}

	return (array) call_user_func( $field[2] );
}

/**
 * Register the Details meta box on every post type with fields.
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

			foreach ( growmodo_field_options( $field ) as $option => $label ) {
				printf(
					'<option value="%1$s"%2$s>%3$s</option>',
					esc_attr( $option ),
					selected( $option, $value, false ),
					esc_html( $label )
				);
			}

			echo '</select>';
		} elseif ( 'textarea' === $field[1] ) {
			printf(
				'<textarea id="%1$s" name="%1$s" rows="6" class="widefat">%2$s</textarea>',
				esc_attr( $key ),
				esc_textarea( $value )
			);
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
		} elseif ( 'textarea' === $field[1] ) {
			$value = sanitize_textarea_field( wp_unslash( $_POST[ $key ] ) );
		} else {
			$value = sanitize_text_field( wp_unslash( $_POST[ $key ] ) );

			// A select may only store a value from its own allowlist.
			if ( 'select' === $field[1] && ! array_key_exists( $value, growmodo_field_options( $field ) ) ) {
				$value = '';
			}
		}

		update_post_meta( $post_id, $key, $value );
	}
}
add_action( 'save_post', 'growmodo_save_meta' );
