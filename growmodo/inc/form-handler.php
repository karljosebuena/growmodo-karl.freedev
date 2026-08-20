<?php
/**
 * Front-end form handling (contact / inquiry / newsletter).
 *
 * Submissions are stored as private `inquiry` posts instead of emailed —
 * free-host mail() is unreliable (decision log in docs/roadmap.md). Every
 * submission passes nonce + honeypot + sanitization.
 *
 * @since 1.0.0
 *
 * @package Growmodo
 */

defined( 'ABSPATH' ) || exit;

/**
 * Handle a front-end form submission and redirect back with a status flag.
 *
 * @since 1.0.0
 *
 * @return void
 */
function growmodo_handle_form() {
	$nonce = isset( $_POST['growmodo_nonce'] )
		? sanitize_key( wp_unslash( $_POST['growmodo_nonce'] ) )
		: '';

	if ( ! wp_verify_nonce( $nonce, 'growmodo_form' ) ) {
		growmodo_form_redirect( 'error' );
	}

	// Honeypot: hidden field real users never fill. Pretend success for bots.
	if ( ! empty( $_POST['growmodo_website'] ) ) {
		growmodo_form_redirect( 'success' );
	}

	$type  = isset( $_POST['growmodo_form_type'] )
		? sanitize_key( wp_unslash( $_POST['growmodo_form_type'] ) )
		: '';
	$email = isset( $_POST['growmodo_email'] )
		? sanitize_email( wp_unslash( $_POST['growmodo_email'] ) )
		: '';

	if ( ! in_array( $type, array( 'contact', 'inquiry', 'newsletter' ), true ) || ! is_email( $email ) ) {
		growmodo_form_redirect( 'error' );
	}

	$name    = isset( $_POST['growmodo_name'] )
		? sanitize_text_field( wp_unslash( $_POST['growmodo_name'] ) )
		: '';
	$phone   = isset( $_POST['growmodo_phone'] )
		? sanitize_text_field( wp_unslash( $_POST['growmodo_phone'] ) )
		: '';
	$message = isset( $_POST['growmodo_message'] )
		? sanitize_textarea_field( wp_unslash( $_POST['growmodo_message'] ) )
		: '';

	$property_id = isset( $_POST['growmodo_property_id'] )
		? absint( wp_unslash( $_POST['growmodo_property_id'] ) )
		: 0;

	// Only accept an id that really is a published property.
	if ( $property_id > 0 && 'property' !== get_post_type( $property_id ) ) {
		$property_id = 0;
	}

	$meta = array(
		'growmodo_email' => $email,
		'growmodo_phone' => $phone,
	);

	if ( $property_id > 0 ) {
		$meta['growmodo_property_id'] = $property_id;
	}

	/*
	 * wp_insert_post() expects slashed data — it calls wp_unslash() internally
	 * (see wp-includes/post.php) — so the values sanitized above are re-slashed
	 * here. Without this a submitted backslash is silently dropped.
	 */
	$post_id = wp_insert_post(
		wp_slash(
			array(
				'post_type'    => 'inquiry',
				'post_status'  => 'private',
				'post_title'   => sprintf( '[%s] %s', $type, '' !== $name ? $name : $email ),
				'post_content' => $message,
				'meta_input'   => $meta,
			)
		)
	);

	if ( $post_id ) {
		/**
		 * Fires after a front-end enquiry has been stored.
		 *
		 * Gives a plugin somewhere to hook notification (email, Slack, a CRM)
		 * without the theme taking on a mail dependency the host may block.
		 *
		 * @since 1.0.0
		 *
		 * @param int    $post_id     ID of the created `inquiry` post.
		 * @param string $type        Form type: 'contact', 'inquiry', or 'newsletter'.
		 * @param array  $meta        Sanitized meta stored with the enquiry.
		 * @param int    $property_id Related property ID, or 0 when not applicable.
		 */
		do_action( 'growmodo_inquiry_created', $post_id, $type, $meta, $property_id );
	}

	growmodo_form_redirect( $post_id ? 'success' : 'error' );
}
add_action( 'admin_post_growmodo_form', 'growmodo_handle_form' );
add_action( 'admin_post_nopriv_growmodo_form', 'growmodo_handle_form' );

/**
 * Redirect back to the submitting page with a status query arg.
 *
 * @since 1.0.0
 *
 * @param string $status Either 'success' or 'error'.
 * @return void
 */
function growmodo_form_redirect( $status ) {
	$target = wp_get_referer();

	if ( ! $target ) {
		$target = home_url( '/' );
	}

	wp_safe_redirect( add_query_arg( 'growmodo_status', $status, $target ) . '#form-status' );
	exit;
}
