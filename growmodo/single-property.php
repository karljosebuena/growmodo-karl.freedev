<?php
/**
 * Single property: title row, gallery, description and features, enquiry form,
 * estimated costs, FAQs.
 *
 * @since 1.0.0
 *
 * @package Growmodo
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();

	$growmodo_beds     = (int) get_post_meta( get_the_ID(), 'growmodo_beds', true );
	$growmodo_baths    = (int) get_post_meta( get_the_ID(), 'growmodo_baths', true );
	$growmodo_price    = (int) get_post_meta( get_the_ID(), 'growmodo_price', true );
	$growmodo_size     = (int) get_post_meta( get_the_ID(), 'growmodo_size', true );
	$growmodo_year     = (int) get_post_meta( get_the_ID(), 'growmodo_year', true );
	$growmodo_type     = get_post_meta( get_the_ID(), 'growmodo_type', true );
	$growmodo_location = get_post_meta( get_the_ID(), 'growmodo_location', true );
	$growmodo_features = growmodo_lines( get_post_meta( get_the_ID(), 'growmodo_features', true ) );
	$growmodo_images   = growmodo_property_images( get_the_ID() );

	// The design pairs the title with the location; the price sits opposite it.
	$growmodo_where = '' === $growmodo_location
		? get_the_title()
		: sprintf(
			/* translators: 1: property title, 2: location. */
			_x( '%1$s, %2$s', 'property and its location', 'growmodo' ),
			get_the_title(),
			$growmodo_location
		);
	?>

	<article <?php post_class(); ?>>
		<section class="property-head">
			<div class="container property-head__inner">
				<div class="property-head__title">
					<h1 class="page-hero__title"><?php echo esc_html( get_the_title() ); ?></h1>

					<?php if ( '' !== $growmodo_location ) : ?>
						<p class="tag">
							<?php echo growmodo_icon( 'pin' ); ?>
							<?php echo esc_html( $growmodo_location ); ?>
						</p>
					<?php endif; ?>
				</div>

				<?php if ( $growmodo_price > 0 ) : ?>
					<p class="property-head__price">
						<span class="property__price-label"><?php esc_html_e( 'Price', 'growmodo' ); ?></span>
						<span class="property__price"><?php echo esc_html( growmodo_format_price( $growmodo_price ) ); ?></span>
					</p>
				<?php endif; ?>
			</div>
		</section>

		<?php if ( ! empty( $growmodo_images ) ) : ?>
			<section class="section section--tight">
				<div class="container">
					<div class="gallery">
						<?php
						get_template_part(
							'template-parts/carousel',
							null,
							array(
								'items'    => $growmodo_images,
								'thumbs'   => $growmodo_images,
								'card'     => 'card-image',
								'label'    => __( 'Property images', 'growmodo' ),
								'per_view' => 2,
							)
						);
						?>
					</div>
				</div>
			</section>
		<?php endif; ?>

		<section class="section section--tight">
			<div class="container">
				<div class="grid grid--pair">
					<div class="card">
						<h2 class="card__title"><?php esc_html_e( 'Description', 'growmodo' ); ?></h2>

						<div class="card__text entry-content">
							<?php
							if ( '' !== trim( get_the_content() ) ) {
								the_content();
							} else {
								echo '<p>' . esc_html( get_the_excerpt() ) . '</p>';
							}
							?>
						</div>

						<ul class="property-stats">
							<?php if ( $growmodo_beds > 0 ) : ?>
								<li class="property-stats__item">
									<span class="property-stats__label">
										<?php echo growmodo_icon( 'bed' ); ?>
										<?php esc_html_e( 'Bedrooms', 'growmodo' ); ?>
									</span>
									<?php // Zero-padded, as the design sets these figures. ?>
									<span class="property-stats__value"><?php echo esc_html( sprintf( '%02d', $growmodo_beds ) ); ?></span>
								</li>
							<?php endif; ?>

							<?php if ( $growmodo_baths > 0 ) : ?>
								<li class="property-stats__item">
									<span class="property-stats__label">
										<?php echo growmodo_icon( 'bath' ); ?>
										<?php esc_html_e( 'Bathrooms', 'growmodo' ); ?>
									</span>
									<span class="property-stats__value"><?php echo esc_html( sprintf( '%02d', $growmodo_baths ) ); ?></span>
								</li>
							<?php endif; ?>

							<?php if ( $growmodo_size > 0 ) : ?>
								<li class="property-stats__item">
									<span class="property-stats__label">
										<?php echo growmodo_icon( 'cube' ); ?>
										<?php esc_html_e( 'Area', 'growmodo' ); ?>
									</span>
									<span class="property-stats__value"><?php echo esc_html( growmodo_format_size( $growmodo_size ) ); ?></span>
								</li>
							<?php endif; ?>

							<?php if ( $growmodo_year > 0 ) : ?>
								<li class="property-stats__item">
									<span class="property-stats__label">
										<?php echo growmodo_icon( 'calendar' ); ?>
										<?php esc_html_e( 'Built', 'growmodo' ); ?>
									</span>
									<?php // No number_format_i18n: a year is not a quantity and must not gain a thousands separator. ?>
									<span class="property-stats__value"><?php echo absint( $growmodo_year ); ?></span>
								</li>
							<?php endif; ?>

							<?php if ( '' !== $growmodo_type ) : ?>
								<li class="property-stats__item">
									<span class="property-stats__label">
										<?php echo growmodo_icon( 'house' ); ?>
										<?php esc_html_e( 'Type', 'growmodo' ); ?>
									</span>
									<span class="property-stats__value"><?php echo esc_html( $growmodo_type ); ?></span>
								</li>
							<?php endif; ?>
						</ul>
					</div>

					<?php if ( ! empty( $growmodo_features ) ) : ?>
						<div class="card">
							<h2 class="card__title"><?php esc_html_e( 'Key Features and Amenities', 'growmodo' ); ?></h2>

							<ul class="feature-list">
								<?php foreach ( $growmodo_features as $growmodo_feature ) : ?>
									<li class="feature-list__item">
										<?php echo growmodo_icon( 'sparkle' ); ?>
										<?php echo esc_html( $growmodo_feature ); ?>
									</li>
								<?php endforeach; ?>
							</ul>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</section>

		<section class="section section--bordered" id="property-inquiry">
			<div class="container property-inquiry">
				<?php
				get_template_part(
					'template-parts/section-head',
					null,
					array(
						'title' => sprintf(
							/* translators: %s: property title. */
							__( 'Inquire About %s', 'growmodo' ),
							get_the_title()
						),
						'text'  => __( 'Interested in this property? Fill out the form below and our team will reach out with full details, including scheduling a viewing and answering any questions you may have.', 'growmodo' ),
					)
				);

				get_template_part(
					'template-parts/form-inquiry',
					null,
					array(
						'id'       => 'property-inquiry-form',
						'type'     => 'property',
						'property' => $growmodo_where,
					)
				);
				?>
			</div>
		</section>

		<?php
		get_template_part(
			'template-parts/property-pricing',
			null,
			array( 'price' => $growmodo_price )
		);

		get_template_part( 'template-parts/faq-section' );
		?>
	</article>

	<?php
endwhile;

get_footer();
