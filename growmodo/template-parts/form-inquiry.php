<?php
/**
 * Shared enquiry form.
 *
 * Posts to admin-post.php and is handled by inc/form-handler.php: nonce,
 * honeypot, sanitization, then stored as a private `inquiry` post.
 *
 * @package Growmodo
 *
 * @since 1.0.0
 *
 * @var array $args {
 *     @type string $id       Element id used for the anchor and field ids. Default 'contact-form'.
 *     @type string $type     Submission type: 'contact' or 'inquiry'. Default 'contact'.
 *     @type string $property Pre-selected property title, shown read-only. Optional.
 * }
 */

defined( 'ABSPATH' ) || exit;

$growmodo_id       = isset( $args['id'] ) ? sanitize_html_class( $args['id'] ) : 'contact-form';
$growmodo_type     = isset( $args['type'] ) && 'inquiry' === $args['type'] ? 'inquiry' : 'contact';
$growmodo_property = isset( $args['property'] ) ? $args['property'] : '';

// Set by growmodo_form_redirect() after a submission.
// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Display-only status flag, no state change.
$growmodo_status = isset( $_GET['growmodo_status'] ) ? sanitize_key( wp_unslash( $_GET['growmodo_status'] ) ) : '';
?>
<form class="form" id="<?php echo esc_attr( $growmodo_id ); ?>" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post">
	<input type="hidden" name="action" value="growmodo_form" />
	<input type="hidden" name="growmodo_form_type" value="<?php echo esc_attr( $growmodo_type ); ?>" />
	<?php wp_nonce_field( 'growmodo_form', 'growmodo_nonce' ); ?>

	<?php if ( '' !== $growmodo_status ) : ?>
		<p class="notice <?php echo 'success' === $growmodo_status ? 'notice--success' : ''; ?>" id="form-status" role="status">
			<?php
			echo 'success' === $growmodo_status
				? esc_html__( 'Thank you — your message has been received. Our team will be in touch shortly.', 'growmodo' )
				: esc_html__( 'Sorry, your message could not be sent. Please check your details and try again.', 'growmodo' );
			?>
		</p>
	<?php endif; ?>

	<p class="form__honeypot" aria-hidden="true">
		<label for="<?php echo esc_attr( $growmodo_id ); ?>-website"><?php esc_html_e( 'Website', 'growmodo' ); ?></label>
		<input type="text" id="<?php echo esc_attr( $growmodo_id ); ?>-website" name="growmodo_website" tabindex="-1" autocomplete="off" />
	</p>

	<div class="form__grid">
		<p class="form__field">
			<label class="form__label" for="<?php echo esc_attr( $growmodo_id ); ?>-name">
				<?php esc_html_e( 'Full name', 'growmodo' ); ?>
			</label>
			<input
				class="form__input"
				type="text"
				id="<?php echo esc_attr( $growmodo_id ); ?>-name"
				name="growmodo_name"
				autocomplete="name"
				placeholder="<?php esc_attr_e( 'Enter your name', 'growmodo' ); ?>"
			/>
		</p>

		<p class="form__field">
			<label class="form__label" for="<?php echo esc_attr( $growmodo_id ); ?>-email">
				<?php esc_html_e( 'Email address', 'growmodo' ); ?>
				<span class="form__required" aria-hidden="true">*</span>
			</label>
			<input
				class="form__input"
				type="email"
				id="<?php echo esc_attr( $growmodo_id ); ?>-email"
				name="growmodo_email"
				autocomplete="email"
				placeholder="<?php esc_attr_e( 'Enter your email', 'growmodo' ); ?>"
				required
			/>
		</p>

		<p class="form__field">
			<label class="form__label" for="<?php echo esc_attr( $growmodo_id ); ?>-phone">
				<?php esc_html_e( 'Phone', 'growmodo' ); ?>
			</label>
			<input
				class="form__input"
				type="tel"
				id="<?php echo esc_attr( $growmodo_id ); ?>-phone"
				name="growmodo_phone"
				autocomplete="tel"
				placeholder="<?php esc_attr_e( 'Enter your phone number', 'growmodo' ); ?>"
			/>
		</p>

		<?php if ( '' !== $growmodo_property ) : ?>
			<p class="form__field">
				<label class="form__label" for="<?php echo esc_attr( $growmodo_id ); ?>-property">
					<?php esc_html_e( 'Selected property', 'growmodo' ); ?>
				</label>
				<input
					class="form__input"
					type="text"
					id="<?php echo esc_attr( $growmodo_id ); ?>-property"
					value="<?php echo esc_attr( $growmodo_property ); ?>"
					readonly
				/>
			</p>
			<?php
			/*
			 * The readonly field above is for the visitor; this is what the
			 * handler stores. Without it the lead records no listing, since a
			 * readonly input the visitor can see is still not a submitted
			 * value unless it is named.
			 */
			?>
			<input type="hidden" name="growmodo_property_id" value="<?php echo absint( get_the_ID() ); ?>" />
		<?php endif; ?>

		<p class="form__field form__field--wide">
			<label class="form__label" for="<?php echo esc_attr( $growmodo_id ); ?>-message">
				<?php esc_html_e( 'Message', 'growmodo' ); ?>
			</label>
			<textarea
				class="form__textarea"
				id="<?php echo esc_attr( $growmodo_id ); ?>-message"
				name="growmodo_message"
				placeholder="<?php esc_attr_e( 'Enter your message here…', 'growmodo' ); ?>"
			><?php echo '' !== $growmodo_property ? esc_textarea( sprintf( /* translators: %s: property title. */ __( 'I would like to know more about %s.', 'growmodo' ), $growmodo_property ) ) : ''; ?></textarea>
		</p>
	</div>

	<button class="btn btn--primary btn--block" type="submit">
		<?php esc_html_e( 'Send Your Message', 'growmodo' ); ?>
	</button>
</form>
