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
 * Room-count options offered by the bedroom and bathroom selects.
 *
 * Single source for the markup and for validating what comes back.
 *
 * @since 1.0.0
 *
 * @return string[] Labels keyed by count.
 */
function growmodo_room_options() {
	return array(
		'1' => '1',
		'2' => '2',
		'3' => '3',
		'4' => '4',
		'5' => _x( '5+', 'five or more rooms', 'growmodo' ),
	);
}

/**
 * Reasons a visitor might be writing, offered on the contact form.
 *
 * @since 1.0.0
 *
 * @return string[] Labels keyed by stored value.
 */
function growmodo_inquiry_types() {
	return array(
		'buying'     => __( 'Buying a property', 'growmodo' ),
		'selling'    => __( 'Selling a property', 'growmodo' ),
		'renting'    => __( 'Renting a property', 'growmodo' ),
		'management' => __( 'Property management', 'growmodo' ),
		'valuation'  => __( 'Valuation or advice', 'growmodo' ),
		'other'      => __( 'Something else', 'growmodo' ),
	);
}

/**
 * Where a visitor heard about us, offered on the contact form.
 *
 * @since 1.0.0
 *
 * @return string[] Labels keyed by stored value.
 */
function growmodo_referrer_options() {
	return array(
		'search'   => __( 'Search engine', 'growmodo' ),
		'social'   => __( 'Social media', 'growmodo' ),
		'referral' => __( 'Recommended by someone', 'growmodo' ),
		'advert'   => __( 'An advert', 'growmodo' ),
		'event'    => __( 'An event or open house', 'growmodo' ),
		'other'    => __( 'Somewhere else', 'growmodo' ),
	);
}

/**
 * Ways a visitor can ask to be contacted.
 *
 * @since 1.0.0
 *
 * @return string[] Labels keyed by stored value.
 */
function growmodo_contact_methods() {
	return array(
		'phone' => __( 'Phone', 'growmodo' ),
		'email' => __( 'Email', 'growmodo' ),
	);
}

/**
 * The consent sentence beside the form's agreement checkbox.
 *
 * Each policy is linked only when the site actually has that page, so the
 * sentence never offers a link to nowhere. Privacy comes from core's own
 * setting; Terms has no core equivalent, so it is looked up by slug.
 *
 * @since 1.0.0
 *
 * @return string Sentence containing anchors; safe for wp_kses_post().
 */
function growmodo_consent_text() {
	// Core's privacy setting is authoritative when it is configured; the slug
	// lookup means the link still works on a site where nobody has set it.
	$privacy_url = get_privacy_policy_url();

	if ( '' === $privacy_url ) {
		$privacy_url = growmodo_page_url( 'privacy-policy' );
	}

	return sprintf(
		/* translators: 1: Terms of Use link or label, 2: Privacy Policy link or label. */
		__( 'I agree with %1$s and %2$s', 'growmodo' ),
		growmodo_policy_link( growmodo_page_url( 'terms-conditions' ), __( 'Terms of Use', 'growmodo' ) ),
		growmodo_policy_link( $privacy_url, __( 'Privacy Policy', 'growmodo' ) )
	);
}

/**
 * Permalink for a page by slug, or an empty string when it does not exist.
 *
 * @since 1.0.0
 *
 * @param string $slug Page slug.
 * @return string
 */
function growmodo_page_url( $slug ) {
	$page = get_page_by_path( $slug );

	return $page instanceof WP_Post ? (string) get_permalink( $page ) : '';
}

/**
 * Render a policy name as a link, or as plain text when there is no page.
 *
 * @since 1.0.0
 *
 * @param string $url   Policy URL, or an empty string.
 * @param string $label Policy name.
 * @return string Escaped anchor, or the escaped label alone.
 */
function growmodo_policy_link( $url, $label ) {
	if ( '' === $url ) {
		return esc_html( $label );
	}

	return sprintf(
		'<a href="%1$s">%2$s</a>',
		esc_url( $url ),
		esc_html( $label )
	);
}

/**
 * Read one posted text field, sanitized.
 *
 * @since 1.0.0
 *
 * @param string $key POST key.
 * @return string Sanitized value, or an empty string.
 */
function growmodo_posted_text( $key ) {
	// phpcs:ignore WordPress.Security.NonceVerification.Missing -- The caller verifies the nonce first.
	return isset( $_POST[ $key ] ) ? sanitize_text_field( wp_unslash( $_POST[ $key ] ) ) : '';
}

/**
 * Read one posted field that must be a key of its own option list.
 *
 * Validation, not just sanitization: a value the form never offered is dropped
 * rather than stored, so the option lists in the markup are also the only
 * values that can reach the database.
 *
 * @since 1.0.0
 *
 * @param string $key     POST key.
 * @param array  $allowed Option list to check against.
 * @return string The value, or an empty string when it is not in the list.
 */
function growmodo_posted_choice( $key, $allowed ) {
	$value = growmodo_posted_text( $key );

	return array_key_exists( $value, $allowed ) ? $value : '';
}

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

	if ( ! in_array( $type, array( 'contact', 'inquiry', 'property', 'newsletter' ), true ) || ! is_email( $email ) ) {
		growmodo_form_redirect( 'error' );
	}

	/*
	 * The agreement checkbox is `required` in the markup, but that is the
	 * browser's opinion and a POST need not come from the form at all. The
	 * newsletter field in the footer has no checkbox, so it is exempt.
	 */
	if ( 'newsletter' !== $type && empty( $_POST['growmodo_consent'] ) ) {
		growmodo_form_redirect( 'error' );
	}

	$first   = growmodo_posted_text( 'growmodo_first_name' );
	$last    = growmodo_posted_text( 'growmodo_last_name' );
	$phone   = growmodo_posted_text( 'growmodo_phone' );
	$message = isset( $_POST['growmodo_message'] )
		? sanitize_textarea_field( wp_unslash( $_POST['growmodo_message'] ) )
		: '';

	$name = trim( $first . ' ' . $last );

	$property_id = isset( $_POST['growmodo_property_id'] )
		? absint( wp_unslash( $_POST['growmodo_property_id'] ) )
		: 0;

	// Only accept an id that really is a published property.
	if ( $property_id > 0 && 'property' !== get_post_type( $property_id ) ) {
		$property_id = 0;
	}

	$meta = array(
		'growmodo_email'          => $email,
		'growmodo_phone'          => $phone,
		'growmodo_first_name'     => $first,
		'growmodo_last_name'      => $last,
		'growmodo_pref_location'  => growmodo_posted_choice( 'growmodo_pref_location', growmodo_property_locations() ),
		'growmodo_pref_type'      => growmodo_posted_choice( 'growmodo_pref_type', growmodo_property_types() ),
		'growmodo_pref_beds'      => (int) growmodo_posted_choice( 'growmodo_pref_beds', growmodo_room_options() ),
		'growmodo_pref_baths'     => (int) growmodo_posted_choice( 'growmodo_pref_baths', growmodo_room_options() ),
		'growmodo_budget'         => growmodo_posted_choice( 'growmodo_budget', growmodo_price_bands() ),
		'growmodo_contact_method' => growmodo_posted_choice( 'growmodo_contact_method', growmodo_contact_methods() ),
		'growmodo_inquiry_type'   => growmodo_posted_choice( 'growmodo_inquiry_type', growmodo_inquiry_types() ),
		'growmodo_referrer'       => growmodo_posted_choice( 'growmodo_referrer', growmodo_referrer_options() ),
	);

	/*
	 * The two forms send different halves of this list, so anything the
	 * submitted one did not ask about is dropped rather than stored empty.
	 */
	$meta = array_filter(
		$meta,
		static function ( $value ) {
			return '' !== $value && 0 !== $value;
		}
	);

	$meta['growmodo_email'] = $email;

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
		 * @param string $type        Form type: 'contact', 'inquiry', 'property', or 'newsletter'.
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
