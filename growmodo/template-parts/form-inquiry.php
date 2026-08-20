<?php
/**
 * Shared enquiry form.
 *
 * Posts to admin-post.php and is handled by inc/form-handler.php: nonce,
 * honeypot, sanitization, then stored as a private `inquiry` post.
 *
 * The rows are built from arrays rather than written out field by field — the
 * design has thirteen of them and the markup is identical apart from the
 * label, so repeating it thirteen times would be thirteen chances to forget an
 * `esc_attr`.
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

$growmodo_texts = array(
	array(
		'name'         => 'growmodo_first_name',
		'type'         => 'text',
		'label'        => __( 'First Name', 'growmodo' ),
		'placeholder'  => __( 'Enter First Name', 'growmodo' ),
		'autocomplete' => 'given-name',
	),
	array(
		'name'         => 'growmodo_last_name',
		'type'         => 'text',
		'label'        => __( 'Last Name', 'growmodo' ),
		'placeholder'  => __( 'Enter Last Name', 'growmodo' ),
		'autocomplete' => 'family-name',
	),
	array(
		'name'         => 'growmodo_email',
		'type'         => 'email',
		'label'        => __( 'Email', 'growmodo' ),
		'placeholder'  => __( 'Enter your Email', 'growmodo' ),
		'autocomplete' => 'email',
		'required'     => true,
	),
	array(
		'name'         => 'growmodo_phone',
		'type'         => 'tel',
		'label'        => __( 'Phone', 'growmodo' ),
		'placeholder'  => __( 'Enter Phone Number', 'growmodo' ),
		'autocomplete' => 'tel',
	),
);

/*
 * The design has two forms, not one. "Let's Make it Happen" beside the listings
 * asks what the visitor is looking for, in four columns; "Let's Connect" on the
 * contact page asks why they are writing, in three. Same markup, different
 * field set — so the type decides which, rather than one form serving both
 * badly by asking a general enquirer for a bedroom count.
 *
 * Preferred Location offers the places we actually have listings in, and Budget
 * reuses the archive's price bands, so neither can drift from the catalogue or
 * from the filter beside it.
 */
$growmodo_is_inquiry = 'inquiry' === $growmodo_type;

$growmodo_selects = $growmodo_is_inquiry
	? array(
		array(
			'name'        => 'growmodo_pref_location',
			'label'       => __( 'Preferred Location', 'growmodo' ),
			'placeholder' => __( 'Select Location', 'growmodo' ),
			'options'     => growmodo_property_locations(),
		),
		array(
			'name'        => 'growmodo_pref_type',
			'label'       => __( 'Property Type', 'growmodo' ),
			'placeholder' => __( 'Select Property Type', 'growmodo' ),
			'options'     => growmodo_property_types(),
		),
		array(
			'name'        => 'growmodo_pref_baths',
			'label'       => __( 'No. of Bathrooms', 'growmodo' ),
			'placeholder' => __( 'Select no. of Bathrooms', 'growmodo' ),
			'options'     => growmodo_room_options(),
		),
		array(
			'name'        => 'growmodo_pref_beds',
			'label'       => __( 'No. of Bedrooms', 'growmodo' ),
			'placeholder' => __( 'Select no. of Bedrooms', 'growmodo' ),
			'options'     => growmodo_room_options(),
		),
		array(
			'name'        => 'growmodo_budget',
			'label'       => __( 'Budget', 'growmodo' ),
			'placeholder' => __( 'Select Budget', 'growmodo' ),
			'options'     => growmodo_price_bands(),
			'class'       => 'form__field--half',
		),
	)
	: array(
		array(
			'name'        => 'growmodo_inquiry_type',
			'label'       => __( 'Inquiry Type', 'growmodo' ),
			'placeholder' => __( 'Select Inquiry Type', 'growmodo' ),
			'options'     => growmodo_inquiry_types(),
		),
		array(
			'name'        => 'growmodo_referrer',
			'label'       => __( 'How Did You Hear About Us?', 'growmodo' ),
			'placeholder' => __( 'Select', 'growmodo' ),
			'options'     => growmodo_referrer_options(),
		),
	);

$growmodo_method_icons = array(
	'phone' => 'phone',
	'email' => 'mail',
);
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

	<div class="form__grid <?php echo $growmodo_is_inquiry ? 'form__grid--4' : 'form__grid--3'; ?>">
		<?php foreach ( $growmodo_texts as $growmodo_field ) : ?>
			<?php $growmodo_field_id = $growmodo_id . '-' . str_replace( 'growmodo_', '', $growmodo_field['name'] ); ?>
			<p class="form__field">
				<label class="form__label" for="<?php echo esc_attr( $growmodo_field_id ); ?>">
					<?php echo esc_html( $growmodo_field['label'] ); ?>
					<?php if ( ! empty( $growmodo_field['required'] ) ) : ?>
						<span class="form__required" aria-hidden="true">*</span>
					<?php endif; ?>
				</label>
				<input
					class="form__input"
					type="<?php echo esc_attr( $growmodo_field['type'] ); ?>"
					id="<?php echo esc_attr( $growmodo_field_id ); ?>"
					name="<?php echo esc_attr( $growmodo_field['name'] ); ?>"
					autocomplete="<?php echo esc_attr( $growmodo_field['autocomplete'] ); ?>"
					placeholder="<?php echo esc_attr( $growmodo_field['placeholder'] ); ?>"
					<?php echo empty( $growmodo_field['required'] ) ? '' : 'required'; ?>
				/>
			</p>
		<?php endforeach; ?>

		<?php foreach ( $growmodo_selects as $growmodo_field ) : ?>
			<?php
			$growmodo_field_id = $growmodo_id . '-' . str_replace( 'growmodo_', '', $growmodo_field['name'] );

			// A select with nothing to offer is a dead control; leave it out.
			if ( empty( $growmodo_field['options'] ) ) {
				continue;
			}
			?>
			<p class="form__field <?php echo esc_attr( isset( $growmodo_field['class'] ) ? $growmodo_field['class'] : '' ); ?>">
				<label class="form__label" for="<?php echo esc_attr( $growmodo_field_id ); ?>">
					<?php echo esc_html( $growmodo_field['label'] ); ?>
				</label>
				<select
					class="form__input"
					id="<?php echo esc_attr( $growmodo_field_id ); ?>"
					name="<?php echo esc_attr( $growmodo_field['name'] ); ?>"
				>
					<option value=""><?php echo esc_html( $growmodo_field['placeholder'] ); ?></option>
					<?php foreach ( $growmodo_field['options'] as $growmodo_value => $growmodo_label ) : ?>
						<option value="<?php echo esc_attr( $growmodo_value ); ?>">
							<?php echo esc_html( $growmodo_label ); ?>
						</option>
					<?php endforeach; ?>
				</select>
			</p>
		<?php endforeach; ?>

		<?php if ( $growmodo_is_inquiry ) : ?>
			<?php
			/*
			 * The design draws each option as a box with the matching icon and a
			 * placeholder inside it. It is rendered here as what it is — a choice
			 * between two methods — rather than as two more inputs: those would
			 * collect the email and phone number the visitor has already given
			 * above, and a form that asks twice is a defect, not fidelity.
			 */
			?>
			<fieldset class="form__field form__field--half">
				<legend class="form__label"><?php esc_html_e( 'Preferred Contact Method', 'growmodo' ); ?></legend>
				<div class="form__choices">
					<?php foreach ( growmodo_contact_methods() as $growmodo_value => $growmodo_label ) : ?>
						<label class="form__choice">
							<?php echo growmodo_icon( $growmodo_method_icons[ $growmodo_value ] ); ?>
							<span class="form__choice-label"><?php echo esc_html( $growmodo_label ); ?></span>
							<input
								type="radio"
								name="growmodo_contact_method"
								value="<?php echo esc_attr( $growmodo_value ); ?>"
								<?php checked( 'phone', $growmodo_value ); ?>
							/>
						</label>
					<?php endforeach; ?>
				</div>
			</fieldset>
		<?php endif; ?>

		<?php if ( '' !== $growmodo_property ) : ?>
			<p class="form__field form__field--half">
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
				placeholder="<?php esc_attr_e( 'Enter your Message here.', 'growmodo' ); ?>"
			><?php echo '' !== $growmodo_property ? esc_textarea( sprintf( /* translators: %s: property title. */ __( 'I would like to know more about %s.', 'growmodo' ), $growmodo_property ) ) : ''; ?></textarea>
		</p>
	</div>

	<div class="form__foot">
		<p class="form__consent">
			<input
				type="checkbox"
				id="<?php echo esc_attr( $growmodo_id ); ?>-consent"
				name="growmodo_consent"
				value="1"
				required
			/>
			<label for="<?php echo esc_attr( $growmodo_id ); ?>-consent">
				<?php echo wp_kses_post( growmodo_consent_text() ); ?>
			</label>
		</p>

		<button class="btn btn--primary form__submit" type="submit">
			<?php esc_html_e( 'Send Your Message', 'growmodo' ); ?>
		</button>
	</div>
</form>
