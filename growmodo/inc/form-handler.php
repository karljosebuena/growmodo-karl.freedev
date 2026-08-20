<?php
/**
 * Front-end form handling (contact / inquiry / newsletter).
 *
 * Submissions are stored as private `inquiry` posts instead of emailed —
 * free-host mail() is unreliable (decision log in docs/roadmap.md). Every
 * submission passes nonce + honeypot + sanitization.
 *
 * @package Growmodo
 */

/**
 * Handle a front-end form submission and redirect back with a status flag.
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

	$post_id = wp_insert_post(
		array(
			'post_type'    => 'inquiry',
			'post_status'  => 'private',
			'post_title'   => sprintf( '[%s] %s', $type, '' !== $name ? $name : $email ),
			'post_content' => $message,
			'meta_input'   => array(
				'growmodo_email' => $email,
				'growmodo_phone' => $phone,
			),
		)
	);

	growmodo_form_redirect( $post_id ? 'success' : 'error' );
}
add_action( 'admin_post_growmodo_form', 'growmodo_handle_form' );
add_action( 'admin_post_nopriv_growmodo_form', 'growmodo_handle_form' );

/**
 * Redirect back to the submitting page with a status query arg.
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
